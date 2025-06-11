<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('replies', function (Blueprint $table) {
            $table->id(); // Kolom ID balasan (auto increment)
            $table->foreignId('discussion_id')->constrained()->onDelete('cascade'); // Relasi dengan tabel discussions
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi dengan tabel users (yang memberikan balasan)
            $table->text('isi'); // Kolom untuk menyimpan isi balasan
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Membalikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('replies'); // Menghapus tabel replies
    }
};
