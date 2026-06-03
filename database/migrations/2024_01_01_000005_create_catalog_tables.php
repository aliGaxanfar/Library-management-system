<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Authors
        Schema::create('authors', function (Blueprint $table) {
            $table->id('authorId');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('nationality')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        // Categories (self-referencing hierarchy)
        Schema::create('categories', function (Blueprint $table) {
            $table->id('categoryId');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parentCategoryId')->nullable();
            $table->timestamps();

            $table->foreign('parentCategoryId')->references('categoryId')->on('categories')->onDelete('set null');
        });

        // Books
        Schema::create('books', function (Blueprint $table) {
            $table->id('bookId');
            $table->string('isbn')->unique();
            $table->string('title');
            $table->integer('publishedYear')->nullable();
            $table->integer('totalCopies')->default(1);
            $table->integer('availableCopies')->default(1);
            $table->string('location')->nullable();
            $table->enum('status', ['AVAILABLE', 'BORROWED', 'RESERVED', 'LOST'])->default('AVAILABLE');
            $table->timestamps();
        });

        // Book-Author pivot (many-to-many)
        Schema::create('book_author', function (Blueprint $table) {
            $table->unsignedBigInteger('bookId');
            $table->unsignedBigInteger('authorId');
            $table->primary(['bookId', 'authorId']);

            $table->foreign('bookId')->references('bookId')->on('books')->onDelete('cascade');
            $table->foreign('authorId')->references('authorId')->on('authors')->onDelete('cascade');
        });

        // Book-Category pivot (many-to-many)
        Schema::create('book_category', function (Blueprint $table) {
            $table->unsignedBigInteger('bookId');
            $table->unsignedBigInteger('categoryId');
            $table->primary(['bookId', 'categoryId']);

            $table->foreign('bookId')->references('bookId')->on('books')->onDelete('cascade');
            $table->foreign('categoryId')->references('categoryId')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_category');
        Schema::dropIfExists('book_author');
        Schema::dropIfExists('books');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('authors');
    }
};
