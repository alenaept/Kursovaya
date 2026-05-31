<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointment';
    protected $primaryKey = 'id_appointment';
    
    protected $fillable = [
        'Users_idUser',
        'doctors_id_doctor',
        'schedule_slot_id_schedule_slot',
        'prices_id_prices'
    ];
    
    public $timestamps = true;
    
    public function user()
    {
        return $this->belongsTo(User::class, 'Users_idUser', 'idUser');
    }
    
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id_doctor', 'id_doctor');
    }
    
    public function scheduleSlot()
    {
        return $this->belongsTo(ScheduleSlot::class, 'schedule_slot_id_schedule_slot', 'id_schedule_slot');
    }
    
    public function price()
    {
        return $this->belongsTo(Price::class, 'prices_id_prices', 'id_prices');
    }
}