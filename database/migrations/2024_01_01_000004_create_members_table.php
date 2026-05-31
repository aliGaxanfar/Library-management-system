<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId')->unique();
            $table->string('memberId')->unique();
            $table->string('membershipType')->default('STANDARD'); // STANDARD, PREMIUM
            $table->date('joinDate');
            $table->date('expiryDate');
            $table->decimal('totalFinesDue', 8, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('userId')->references('userId')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
