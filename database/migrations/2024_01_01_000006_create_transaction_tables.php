<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Loans
        Schema::create('loans', function (Blueprint $table) {
            $table->id('loanId');
            $table->unsignedBigInteger('memberId');  // FK -> members.id
            $table->unsignedBigInteger('bookId');
            $table->unsignedBigInteger('librarianId'); // FK -> librarians.id
            $table->date('issueDate');
            $table->date('dueDate');
            $table->date('returnDate')->nullable();
            $table->enum('status', ['ACTIVE', 'RETURNED', 'OVERDUE'])->default('ACTIVE');
            $table->integer('renewalCount')->default(0);
            $table->timestamps();

            $table->foreign('memberId')->references('id')->on('members')->onDelete('restrict');
            $table->foreign('bookId')->references('bookId')->on('books')->onDelete('restrict');
            $table->foreign('librarianId')->references('id')->on('librarians')->onDelete('restrict');
        });

        // Fines
        Schema::create('fines', function (Blueprint $table) {
            $table->id('fineId');
            $table->unsignedBigInteger('loanId')->unique(); // 1 loan -> 0..1 fine
            $table->unsignedBigInteger('memberId');
            $table->decimal('amount', 8, 2);
            $table->string('reason');
            $table->date('issuedDate');
            $table->date('paidDate')->nullable();
            $table->enum('status', ['PENDING', 'PAID', 'WAIVED'])->default('PENDING');
            $table->timestamps();

            $table->foreign('loanId')->references('loanId')->on('loans')->onDelete('cascade');
            $table->foreign('memberId')->references('id')->on('members')->onDelete('restrict');
        });

        // Reservations
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservationId');
            $table->unsignedBigInteger('memberId');
            $table->unsignedBigInteger('bookId');
            $table->unsignedBigInteger('loanId')->nullable(); // converts to loan
            $table->dateTime('reservationDate');
            $table->dateTime('expiryDate');
            $table->enum('status', ['PENDING', 'CONFIRMED', 'CANCELLED', 'EXPIRED'])->default('PENDING');
            $table->integer('queuePosition')->default(1);
            $table->timestamps();

            $table->foreign('memberId')->references('id')->on('members')->onDelete('restrict');
            $table->foreign('bookId')->references('bookId')->on('books')->onDelete('restrict');
            $table->foreign('loanId')->references('loanId')->on('loans')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('fines');
        Schema::dropIfExists('loans');
    }
};
