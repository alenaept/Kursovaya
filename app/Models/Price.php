<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $table = 'prices';
    protected $primaryKey = 'id_prices'; 
    
    protected $fillable = [
        'category',
        'description', 
        'price'
    ];
    
    public $timestamps = true; 
}