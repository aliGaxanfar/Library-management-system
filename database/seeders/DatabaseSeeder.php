<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Librarian;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. USERS & ROLES ─────────────────────────────────────────
        $adminUser = User::create([
            'fullName'     => 'Alice Admin',
            'email'        => 'admin@lms.test',
            'passwordHash' => Hash::make('password'),
            'role'         => 'ADMIN',
            'isActive'     => true,
        ]);
        Admin::create(['userId' => $adminUser->userId, 'adminLevel' => 2]);

        $libUser = User::create([
            'fullName'     => 'Bob Librarian',
            'email'        => 'librarian@lms.test',
            'passwordHash' => Hash::make('password'),
            'role'         => 'LIBRARIAN',
            'isActive'     => true,
        ]);
        $librarian = Librarian::create([
            'userId'     => $libUser->userId,
            'employeeId' => 'EMP-001',
            'department' => 'Circulation',
        ]);

        $memberUsers = [
            ['fullName' => 'Carol Chen',    'email' => 'carol@lms.test',   'type' => 'PREMIUM'],
            ['fullName' => 'David Dunn',    'email' => 'david@lms.test',   'type' => 'STANDARD'],
            ['fullName' => 'Eva Martinez',  'email' => 'eva@lms.test',     'type' => 'STANDARD'],
            ['fullName' => 'Frank Osei',    'email' => 'frank@lms.test',   'type' => 'PREMIUM'],
        ];

        $members = [];
        foreach ($memberUsers as $i => $mu) {
            $u = User::create([
                'fullName'     => $mu['fullName'],
                'email'        => $mu['email'],
                'passwordHash' => Hash::make('password'),
                'role'         => 'MEMBER',
                'isActive'     => true,
            ]);
            $members[] = Member::create([
                'userId'         => $u->userId,
                'memberId'       => 'MBR-' . str_pad($u->userId, 5, '0', STR_PAD_LEFT),
                'membershipType' => $mu['type'],
                'joinDate'       => Carbon::today()->subMonths(rand(1, 18)),
                'expiryDate'     => Carbon::today()->addMonths(rand(6, 18)),
                'totalFinesDue'  => 0,
            ]);
        }

        // ── 2. AUTHORS ───────────────────────────────────────────────
        $authors = collect([
            ['firstName' => 'George',  'lastName' => 'Orwell',      'nationality' => 'British'],
            ['firstName' => 'J.K.',    'lastName' => 'Rowling',     'nationality' => 'British'],
            ['firstName' => 'Frank',   'lastName' => 'Herbert',     'nationality' => 'American'],
            ['firstName' => 'Agatha',  'lastName' => 'Christie',    'nationality' => 'British'],
            ['firstName' => 'Gabriel', 'lastName' => 'García Márquez', 'nationality' => 'Colombian'],
            ['firstName' => 'Toni',    'lastName' => 'Morrison',    'nationality' => 'American'],
            ['firstName' => 'Haruki',  'lastName' => 'Murakami',    'nationality' => 'Japanese'],
            ['firstName' => 'Chimamanda', 'lastName' => 'Adichie',  'nationality' => 'Nigerian'],
        ])->map(fn($a) => Author::create($a));

        // ── 3. CATEGORIES ────────────────────────────────────────────
        $fiction    = Category::create(['name' => 'Fiction',    'description' => 'Fictional works']);
        $nonFiction = Category::create(['name' => 'Non-Fiction','description' => 'Factual works']);
        $scifi      = Category::create(['name' => 'Sci-Fi',     'description' => 'Science fiction', 'parentCategoryId' => $fiction->categoryId]);
        $mystery    = Category::create(['name' => 'Mystery',    'description' => 'Crime & mystery',  'parentCategoryId' => $fiction->categoryId]);
        $literary   = Category::create(['name' => 'Literary',   'description' => 'Literary fiction', 'parentCategoryId' => $fiction->categoryId]);
        $dystopian  = Category::create(['name' => 'Dystopian',  'description' => 'Dystopian fiction','parentCategoryId' => $scifi->categoryId]);

        // ── 4. BOOKS ─────────────────────────────────────────────────
        $booksData = [
            ['isbn' => '978-0451524935', 'title' => '1984',                      'year' => 1949, 'copies' => 4, 'author' => 0, 'cats' => [$fiction->categoryId, $dystopian->categoryId]],
            ['isbn' => '978-0547928227', 'title' => 'Animal Farm',               'year' => 1945, 'copies' => 3, 'author' => 0, 'cats' => [$fiction->categoryId]],
            ['isbn' => '978-0439708180', 'title' => "Harry Potter and the Sorcerer's Stone", 'year' => 1997, 'copies' => 5, 'author' => 1, 'cats' => [$fiction->categoryId]],
            ['isbn' => '978-0441013593', 'title' => 'Dune',                      'year' => 1965, 'copies' => 3, 'author' => 2, 'cats' => [$scifi->categoryId]],
            ['isbn' => '978-0062073501', 'title' => 'And Then There Were None',  'year' => 1939, 'copies' => 4, 'author' => 3, 'cats' => [$mystery->categoryId]],
            ['isbn' => '978-0060935467', 'title' => 'One Hundred Years of Solitude', 'year' => 1967, 'copies' => 2, 'author' => 4, 'cats' => [$fiction->categoryId, $literary->categoryId]],
            ['isbn' => '978-0452284234', 'title' => 'Beloved',                   'year' => 1987, 'copies' => 3, 'author' => 5, 'cats' => [$fiction->categoryId, $literary->categoryId]],
            ['isbn' => '978-0679720201', 'title' => 'Norwegian Wood',            'year' => 1987, 'copies' => 2, 'author' => 6, 'cats' => [$fiction->categoryId, $literary->categoryId]],
            ['isbn' => '978-1400079179', 'title' => 'Purple Hibiscus',           'year' => 2003, 'copies' => 2, 'author' => 7, 'cats' => [$fiction->categoryId]],
            ['isbn' => '978-1616953560', 'title' => 'Americanah',                'year' => 2013, 'copies' => 3, 'author' => 7, 'cats' => [$fiction->categoryId, $literary->categoryId]],
        ];

        $books = [];
        foreach ($booksData as $bd) {
            $book = Book::create([
                'isbn'            => $bd['isbn'],
                'title'           => $bd['title'],
                'publishedYear'   => $bd['year'],
                'totalCopies'     => $bd['copies'],
                'availableCopies' => $bd['copies'],
                'location'        => 'Shelf ' . chr(65 + array_search($bd, $booksData)),
                'status'          => 'AVAILABLE',
            ]);
            $book->authors()->attach($authors[$bd['author']]->authorId);
            $book->categories()->attach($bd['cats']);
            $books[] = $book;
        }

        // ── 5. LOANS (mix of active, returned, overdue) ───────────────
        // Active loan - member 0 borrowing book 0
        $loan1 = Loan::create([
            'memberId'     => $members[0]->id,
            'bookId'       => $books[0]->bookId,
            'librarianId'  => $librarian->id,
            'issueDate'    => Carbon::today()->subDays(5),
            'dueDate'      => Carbon::today()->addDays(9),
            'status'       => 'ACTIVE',
            'renewalCount' => 0,
        ]);
        $books[0]->decrement('availableCopies');

        // Active loan - member 1 borrowing book 2
        $loan2 = Loan::create([
            'memberId'     => $members[1]->id,
            'bookId'       => $books[2]->bookId,
            'librarianId'  => $librarian->id,
            'issueDate'    => Carbon::today()->subDays(3),
            'dueDate'      => Carbon::today()->addDays(11),
            'status'       => 'ACTIVE',
            'renewalCount' => 1,
        ]);
        $books[2]->decrement('availableCopies');

        // Overdue loan - member 2 (7 days overdue)
        $loan3 = Loan::create([
            'memberId'     => $members[2]->id,
            'bookId'       => $books[3]->bookId,
            'librarianId'  => $librarian->id,
            'issueDate'    => Carbon::today()->subDays(21),
            'dueDate'      => Carbon::today()->subDays(7),
            'status'       => 'OVERDUE',
            'renewalCount' => 0,
        ]);
        $books[3]->decrement('availableCopies');

        // Returned loan - member 3 (returned on time)
        $loan4 = Loan::create([
            'memberId'     => $members[3]->id,
            'bookId'       => $books[4]->bookId,
            'librarianId'  => $librarian->id,
            'issueDate'    => Carbon::today()->subDays(20),
            'dueDate'      => Carbon::today()->subDays(6),
            'returnDate'   => Carbon::today()->subDays(8),
            'status'       => 'RETURNED',
            'renewalCount' => 0,
        ]);

        // Returned late loan - member 0 (generated a fine)
        $loan5 = Loan::create([
            'memberId'     => $members[0]->id,
            'bookId'       => $books[5]->bookId,
            'librarianId'  => $librarian->id,
            'issueDate'    => Carbon::today()->subDays(30),
            'dueDate'      => Carbon::today()->subDays(16),
            'returnDate'   => Carbon::today()->subDays(10),
            'status'       => 'RETURNED',
            'renewalCount' => 0,
        ]);

        // ── 6. FINES ─────────────────────────────────────────────────
        // Fine for overdue loan (loan3) - pending
        $fine1 = Fine::create([
            'loanId'     => $loan3->loanId,
            'memberId'   => $members[2]->id,
            'amount'     => Fine::calculateAmount(7),
            'reason'     => 'Book 7 days overdue',
            'issuedDate' => Carbon::today()->subDays(7),
            'status'     => 'PENDING',
        ]);
        $members[2]->increment('totalFinesDue', $fine1->amount);

        // Fine for late return (loan5) - paid
        $fine2 = Fine::create([
            'loanId'     => $loan5->loanId,
            'memberId'   => $members[0]->id,
            'amount'     => Fine::calculateAmount(6),
            'reason'     => 'Book returned 6 days late',
            'issuedDate' => Carbon::today()->subDays(10),
            'paidDate'   => Carbon::today()->subDays(8),
            'status'     => 'PAID',
        ]);

        // ── 7. RESERVATIONS ─────────────────────────────────────────
        // Member 1 reserves a borrowed book
        Reservation::create([
            'memberId'        => $members[1]->id,
            'bookId'          => $books[3]->bookId,
            'reservationDate' => now()->subDays(2),
            'expiryDate'      => now()->addDays(5),
            'status'          => 'PENDING',
            'queuePosition'   => 1,
        ]);

        // Member 3 reserves the same book (queue pos 2)
        Reservation::create([
            'memberId'        => $members[3]->id,
            'bookId'          => $books[3]->bookId,
            'reservationDate' => now()->subDays(1),
            'expiryDate'      => now()->addDays(6),
            'status'          => 'PENDING',
            'queuePosition'   => 2,
        ]);

        // ── 8. NOTIFICATIONS ─────────────────────────────────────────
        Notification::create([
            'memberId'    => $members[2]->id,
            'type'        => 'OVERDUE',
            'title'       => 'Book Overdue',
            'message'     => "Your loan of 'Dune' is 7 days overdue. Please return it immediately.",
            'isRead'      => false,
            'deliveredAt' => now()->subDays(7),
            'channel'     => 'IN_APP',
        ]);
        Notification::create([
            'memberId'    => $members[2]->id,
            'type'        => 'FINE',
            'title'       => 'Fine Issued',
            'message'     => 'A fine of $3.50 has been issued for the overdue return of Dune.',
            'isRead'      => false,
            'deliveredAt' => now()->subDays(7),
            'channel'     => 'IN_APP',
        ]);
        Notification::create([
            'memberId'    => $members[0]->id,
            'type'        => 'DUE_REMINDER',
            'title'       => 'Due Date Reminder',
            'message'     => "Reminder: '1984' is due in 9 days.",
            'isRead'      => false,
            'deliveredAt' => now(),
            'channel'     => 'IN_APP',
        ]);

        // ── 9. AUDIT LOGS ────────────────────────────────────────────
        AuditLog::record($adminUser->userId, 'CREATE', 'users', $libUser->userId,  [], '127.0.0.1');
        AuditLog::record($libUser->userId,   'CREATE', 'loans', $loan1->loanId,    [], '127.0.0.1');
        AuditLog::record($libUser->userId,   'CREATE', 'loans', $loan2->loanId,    [], '127.0.0.1');
        AuditLog::record($libUser->userId,   'CREATE', 'loans', $loan3->loanId,    [], '127.0.0.1');
        AuditLog::record($libUser->userId,   'UPDATE', 'loans', $loan4->loanId,    ['action' => 'return'], '127.0.0.1');
        AuditLog::record($libUser->userId,   'UPDATE', 'loans', $loan5->loanId,    ['action' => 'return'], '127.0.0.1');
        AuditLog::record($libUser->userId,   'CREATE', 'fines', $fine1->fineId,    [], '127.0.0.1');
        AuditLog::record($libUser->userId,   'UPDATE', 'fines', $fine2->fineId,    ['action' => 'markPaid'], '127.0.0.1');

        $this->command->info('✅  Seeder complete. Login credentials:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',     'admin@lms.test',     'password'],
                ['Librarian', 'librarian@lms.test', 'password'],
                ['Member 1',  'carol@lms.test',     'password'],
                ['Member 2',  'david@lms.test',     'password'],
            ]
        );
    }
}
