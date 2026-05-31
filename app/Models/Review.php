<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id_reviews';
    
    protected $fillable = ['review_text', 'Users_idUser'];
    
    public $timestamps = true;
    
    public function user()
    {
        return $this->belongsTo(User::class, 'Users_idUser', 'idUser');
    }
}