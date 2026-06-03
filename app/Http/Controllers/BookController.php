<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['authors', 'categories']);

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%$search%")
                  ->orWhere('isbn', 'like', "%$search%")
                  ->orWhereHas('authors', fn($q) =>
                        $q->where('firstName', 'like', "%$search%")
                          ->orWhere('lastName', 'like', "%$search%")
                  );
        }

        if ($category = $request->input('category')) {
            $query->whereHas('categories', fn($q) => $q->where('categoryId', $category));
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $books = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        $book->load(['authors', 'categories', 'loans' => fn($q) => $q->latest()->take(5)]);
        return view('books.show', compact('book'));
    }

    public function create()
    {
        $authors    = Author::orderBy('lastName')->get();
        $categories = Category::all();
        return view('books.create', compact('authors', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'isbn'           => 'required|string|unique:books,isbn',
            'title'          => 'required|string|max:200',
            'publishedYear'  => 'nullable|integer|min:1000|max:' . date('Y'),
            'totalCopies'    => 'required|integer|min:1',
            'location'       => 'nullable|string|max:100',
            'authors'        => 'required|array|min:1',
            'authors.*'      => 'exists:authors,authorId',
            'categories'     => 'nullable|array',
            'categories.*'   => 'exists:categories,categoryId',
        ]);

        $book = Book::create([
            'isbn'            => $data['isbn'],
            'title'           => $data['title'],
            'publishedYear'   => $data['publishedYear'],
            'totalCopies'     => $data['totalCopies'],
            'availableCopies' => $data['totalCopies'],
            'location'        => $data['location'],
            'status'          => 'AVAILABLE',
        ]);

        $book->authors()->sync($data['authors']);
        $book->categories()->sync($data['categories'] ?? []);

        AuditLog::record(Auth::id(), 'CREATE', 'books', $book->bookId, $data, request()->ip());

        return redirect()->route('books.index')->with('success', "Book '{$book->title}' added successfully.");
    }

    public function edit(Book $book)
    {
        $book->load(['authors', 'categories']);
        $authors    = Author::orderBy('lastName')->get();
        $categories = Category::all();
        return view('books.edit', compact('book', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'isbn'           => "required|string|unique:books,isbn,{$book->bookId},bookId",
            'title'          => 'required|string|max:200',
            'publishedYear'  => 'nullable|integer|min:1000|max:' . date('Y'),
            'totalCopies'    => 'required|integer|min:1',
            'location'       => 'nullable|string|max:100',
            'status'         => 'required|in:AVAILABLE,BORROWED,RESERVED,LOST',
            'authors'        => 'required|array|min:1',
            'authors.*'      => 'exists:authors,authorId',
            'categories'     => 'nullable|array',
            'categories.*'   => 'exists:categories,categoryId',
        ]);

        $old = $book->toArray();
        $book->update($data);
        $book->authors()->sync($data['authors']);
        $book->categories()->sync($data['categories'] ?? []);

        AuditLog::record(Auth::id(), 'UPDATE', 'books', $book->bookId,
            ['before' => $old, 'after' => $book->toArray()], request()->ip());

        return redirect()->route('books.show', $book)->with('success', 'Book updated.');
    }

    public function destroy(Book $book)
    {
        AuditLog::record(Auth::id(), 'DELETE', 'books', $book->bookId, $book->toArray(), request()->ip());
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Book deleted.');
    }
}
