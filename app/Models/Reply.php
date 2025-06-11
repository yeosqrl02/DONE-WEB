<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = ['discussion_id', 'user_id', 'isi']; // Kolom yang bisa diisi

    // Relasi dengan User (pemilik balasan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Discussion (diskusi yang dibalas)
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
}
