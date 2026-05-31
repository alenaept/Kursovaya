<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Model\Appointment;

class PatientNote extends Model
{
    protected $table = 'patient_notes';
    protected $primaryKey = 'id_patient_notes';
    
    protected $fillable = ['reason', 'appointment_id_appointment'];
    
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id_appointment', 'id_appointment');
    }
}