<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    protected $table = 'specialOffers';
    protected $primaryKey = 'id_specialOffers';
    
    protected $fillable = ['title', 'percent', 'desc'];
    
    public $timestamps = true;
}