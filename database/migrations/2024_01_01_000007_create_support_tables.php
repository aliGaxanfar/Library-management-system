<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notifId');
            $table->unsignedBigInteger('memberId');
            $table->enum('type', ['DUE_REMINDER', 'OVERDUE', 'FINE', 'AVAIL']);
            $table->string('title');
            $table->text('message');
            $table->boolean('isRead')->default(false);
            $table->dateTime('deliveredAt')->nullable();
            $table->enum('channel', ['EMAIL', 'SMS', 'IN_APP'])->default('IN_APP');
            $table->timestamps();

            $table->foreign('memberId')->references('id')->on('members')->onDelete('cascade');
        });

        // Reports
        Schema::create('reports', function (Blueprint $table) {
            $table->id('reportId');
            $table->unsignedBigInteger('adminId'); // FK -> admins.id
            $table->string('title');
            $table->enum('type', ['BORROW', 'MEMBER', 'FINE', 'INVENTORY']);
            $table->dateTime('generatedAt');
            $table->enum('format', ['PDF', 'CSV'])->default('PDF');
            $table->json('parameters')->nullable(); // flexible filter params
            $table->timestamps();

            $table->foreign('adminId')->references('id')->on('admins')->onDelete('restrict');
        });

        // AuditLog (immutable)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('logId');
            $table->unsignedBigInteger('performedBy'); // FK -> users.userId
            $table->string('action');        // CREATE, UPDATE, DELETE
            $table->string('tableName');
            $table->unsignedBigInteger('recordId');
            $table->dateTime('performedAt');
            $table->string('ipAddress')->nullable();
            $table->json('changes')->nullable(); // before/after snapshot
            // No timestamps() — immutable record

            $table->foreign('performedBy')->references('userId')->on('users')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('notifications');
    }
};
