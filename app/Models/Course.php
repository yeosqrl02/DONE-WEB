<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Menambahkan 'user_id' ke dalam fillable agar bisa diisi massal
    protected $fillable = ['name', 'user_id'];  // Tambahkan 'user_id' untuk memastikan pemiliknya adalah user yang login

    // Relasi dengan tabel tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);  // Satu mata kuliah dapat memiliki banyak tugas
    }

    // Relasi dengan tabel users
    public function user()
    {
        return $this->belongsTo(User::class);  // Setiap mata kuliah hanya dimiliki oleh satu pengguna
    }
}
