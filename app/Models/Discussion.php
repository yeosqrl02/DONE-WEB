<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'pertanyaan']; // Kolom yang bisa diisi

    // Relasi dengan User (pemilik diskusi)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Replies (balasan dari diskusi)
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}
