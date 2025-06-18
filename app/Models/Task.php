<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',        // Nama tugas
        'deskripsi',   // Deskripsi tugas
        'tanggal',     // Tanggal tugas
        'course_id',   // ID Mata Kuliah
        'completed',   // Status selesai
        'user_id',     // ID Pengguna
    ];

    // Casting untuk memastikan format data
    protected $casts = [
        'tanggal' => 'datetime',   // Mengubah ke format datetime
        'completed' => 'boolean',  // Status completed berupa boolean
    ];

    // Relasi dengan model Course
    public function course()
    {
        return $this->belongsTo(Course::class);  // Satu tugas hanya punya satu mata kuliah
    }

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);  // Satu tugas hanya punya satu pengguna
    }
}
