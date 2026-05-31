<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublishedReview extends Model
{
    protected $table = 'published_reviews';
    
    protected $fillable = ['review_id', 'published_at'];
    
    public $timestamps = true;
}