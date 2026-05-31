<?php
namespace App\Http\Controllers;

use App\Models\ScheduleSlot;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleSlotController extends Controller
{

    public function getAvailableSlots(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $slots = ScheduleSlot::with(['doctor', 'appointment'])
            ->where('is_available', true)
            ->whereDoesntHave('appointment')
            ->whereDate('start_time', $date)
            ->orderBy('start_time')
            ->get()
            ->map(function($slot) {
                return [
                    'id' => $slot->id_schedule_slot,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'doctor' => [
                        'id' => $slot->doctor->id_doctor,
                        'name' => $slot->doctor->first_name . ' ' . $slot->doctor->last_name,
                        'specialization' => $slot->doctor->specialization ?? 'Врач'
                    ]
                ];
            });
        
        return response()->json($slots);
    }
    
    public function getDoctorAvailableSlots($doctorId, Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $slots = ScheduleSlot::with('appointment')
            ->where('doctors_id_doctor', $doctorId)
            ->where('is_available', true)
            ->whereDoesntHave('appointment')
            ->whereDate('start_time', $date)
            ->orderBy('start_time')
            ->get()
            ->map(function($slot) {
                return [
                    'id' => $slot->id_schedule_slot,
                    'start_time' => $slot->start_time->format('H:i'),
                    'end_time' => $slot->end_time->format('H:i'),
                    'date' => $slot->start_time->format('d.m.Y')
                ];
            });
        
        return response()->json($slots);
    }
}