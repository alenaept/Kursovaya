<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleSlot extends Model
{
    protected $table = 'schedule_slot'; 
    protected $primaryKey = 'id_schedule_slot';
    
protected $fillable = [
    'start_time', 'end_time', 'is_available', 'doctors_id_doctor'
];

protected $casts = [
    'start_time' => 'datetime',
    'end_time' => 'datetime',
    'is_available' => 'boolean'
];
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id_doctor', 'id_doctor');
    }
    
    public function appointment()
    {
        return $this->hasOne(Appointment::class, 'schedule_slot_id_schedule_slot', 'id_schedule_slot');
    }
}