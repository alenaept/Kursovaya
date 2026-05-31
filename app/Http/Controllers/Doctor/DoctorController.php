<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DoctorController extends Controller
{

    public function getAppointments(Request $request, $doctorId)
{
    $date = $request->get('date', Carbon::today()->toDateString());
    
    $appointments = DB::table('appointment as a')
        ->join('users as u', 'a.Users_idUser', '=', 'u.idUser')
        ->join('schedule_slot as s', 'a.schedule_slot_id_schedule_slot', '=', 's.id_schedule_slot')
        ->leftJoin('prices as p', 'a.prices_id_prices', '=', 'p.id_prices')
        ->leftJoin('patient_notes as n', 'a.id_appointment', '=', 'n.appointment_id_appointment')
        ->where('s.doctors_id_doctor', $doctorId)
        ->where('s.slot_date', $date)
        ->orderBy('s.slot_time', 'asc')
        ->select(
            'a.id_appointment as id',
            'u.first_name as patient_first_name',
            'u.last_name as patient_last_name',
            'u.phone as patient_phone',
            'u.date_of_birth as patient_birth_date',
            'u.gender as patient_gender',
            's.slot_date as date',
            's.slot_time as time',
            'p.description as service',
            'p.price as price',
            'n.reason as reason'
        )
        ->get()
        ->map(function($appointment) {
            $birthDate = $appointment->patient_birth_date 
                ? Carbon::parse($appointment->patient_birth_date)->format('d.m.Y') 
                : 'Не указана';
            
            $gender = $appointment->patient_gender === 'male' ? 'Мужской' 
                    : ($appointment->patient_gender === 'female' ? 'Женский' : 'Не указан');
            
            return [
                'id' => $appointment->id,
                'patient_name' => $appointment->patient_first_name . ' ' . $appointment->patient_last_name,
                'patient_phone' => $appointment->patient_phone,
                'patient_birth_date' => $birthDate,
                'patient_gender' => $gender,
                'service' => $appointment->service ?? 'Консультация',
                'price' => (float)($appointment->price ?? 1500),
                'date' => Carbon::parse($appointment->date)->format('d.m.Y'),
                'time' => substr($appointment->time, 0, 5),
                'reason' => $appointment->reason ?? 'Не указана'
            ];
        });
    
    return response()->json($appointments);
}
    
    public function getSchedule(Request $request, $doctorId)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $slots = DB::table('schedule_slot')
            ->where('doctors_id_doctor', $doctorId)
            ->where('slot_date', $date)
            ->orderBy('slot_time')
            ->get()
            ->map(function($slot) {
                return [
                    'id' => $slot->id_schedule_slot,
                    'slot_time' => substr($slot->slot_time, 0, 5),
                    'end_time' => substr($slot->end_time, 0, 5),
                    'is_available' => $slot->is_available,
                    'booked_by' => $slot->booked_by,
                    'patient_name' => $slot->booked_by ? DB::table('users')->where('idUser', $slot->booked_by)->value('first_name') : null
                ];
            });
        
        return response()->json($slots);
    }

}