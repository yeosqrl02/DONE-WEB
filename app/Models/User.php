<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi massal.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'mobile',
        'address',
        'job_title',
        'location',
        'website',
        'github',
        'twitter',
        'instagram',
        'facebook',
        'avatar', // ✅ Tambahkan ini agar avatar bisa disimpan
        'user_id',
    ];

    /**
     * Atribut yang harus disembunyikan.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang dikonversi ke tipe data tertentu.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
