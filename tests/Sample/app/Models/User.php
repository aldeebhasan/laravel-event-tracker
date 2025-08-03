<?php

namespace Aldeebhasan\LaravelEventTracker\Tests\Sample\App\Models;

use Aldeebhasan\LaravelEventTracker\Tests\Sample\Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'password',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static function newFactory(): Factory
    {
        return new UserFactory;
    }
}
