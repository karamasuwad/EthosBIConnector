<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'bi_link_id',
        'bi_link_name',
        'is_user',
        'is_active',
        'slpCode',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'is_user' => 'boolean',
    ];

    // ✅ Optional: Relationship to BI Link
    public function biLink()
    {
        return $this->belongsTo(BiLink::class, 'bi_link_id');
    }
}
