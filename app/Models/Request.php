<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = 'requests';
    protected $primaryKey = 'id_requests';
    
    protected $fillable = ['name', 'phone', 'is_processed', 'processed_by', 'processed_at', 'status'];
    
    public $timestamps = true;
    
    public function processedBy()
    {
        return $this->belongsTo(Admin::class, 'processed_by', 'id_admins');
    }


}