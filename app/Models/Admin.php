<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;
protected $primaryKey = 'id_admins'; 
public $timestamps = false; 
    protected $fillable = ['email', 'password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed', 
        ];
    }
}