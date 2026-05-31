<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Doctor extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'doctors';
    protected $primaryKey = 'id_doctor';
    
    protected $fillable = [
        'first_name', 
        'last_name', 
        'email', 
        'phone', 
        'experience_years', 
        'password',
        'date_of_birth',
        'photo_url', 
        'admins_id_admins'
    ];
    
    protected $hidden = ['password'];

    public function scheduleSlots()
    {
        return $this->hasMany(ScheduleSlot::class, 'doctors_id_doctor', 'id_doctor');
    }
}