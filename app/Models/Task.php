<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'tanggal',
        'course_id',
        'completed',
        // 'user_id',  // hapus jika kolom ini tidak ada
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'completed' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Hapus method user() jika tidak ada relasi user_id
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
