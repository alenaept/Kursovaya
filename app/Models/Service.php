<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'id_services';
    
    protected $fillable = ['name', 'description', 'price', 'admin_id_admin'];
    
    protected $casts = [
        'price' => 'decimal:2'
    ];
}