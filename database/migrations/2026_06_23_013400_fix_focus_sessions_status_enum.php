<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'in_progress' dan 'paused' ke ENUM, pertahankan yang sudah ada
        DB::statement("
            ALTER TABLE focus_sessions
            MODIFY COLUMN status
            ENUM('in_progress', 'paused', 'completed', 'interrupted', 'cancelled')
            NOT NULL
            DEFAULT 'in_progress'
        ");
    }

    public function down(): void
    {
        // Kembalikan ke ENUM asal (hati-hati: row dengan status lain akan error)
        DB::statement("
            ALTER TABLE focus_sessions
            MODIFY COLUMN status
            ENUM('completed', 'interrupted', 'cancelled')
            NOT NULL
        ");
    }
};