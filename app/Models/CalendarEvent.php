<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CalendarEvent extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'user_id',
        'title',
        'date',
    ];

    // Relasi ke user (jika kamu ingin akses $event->user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
