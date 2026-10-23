<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiLink extends Model
{
    use HasFactory;

    protected $table = 'bi_links';

    protected $fillable = [
        'name',
        'link',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ✅ Optional: Add relationship to users
    public function users()
    {
        return $this->hasMany(User::class, 'bi_link_id');
    }
}
