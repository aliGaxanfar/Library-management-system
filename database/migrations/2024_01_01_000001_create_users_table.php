<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('userId');
            $table->string('fullName');
            $table->string('email')->unique();
            $table->string('passwordHash');
            $table->enum('role', ['ADMIN', 'LIBRARIAN', 'MEMBER'])->default('MEMBER');
            $table->boolean('isActive')->default(true);
            $table->timestamps(); // createdAt / updatedAt
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
