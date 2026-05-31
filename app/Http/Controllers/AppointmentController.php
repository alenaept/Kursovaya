<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    
    public function getDoctors()
    {
        $doctors = DB::table('doctors')
            ->select('id_doctor', 'first_name', 'last_name', 'experience_years')
            ->get();
        
        return response()->json($doctors);
    }
    
    // Получить доступные слоты конкретного врача
    public function getDoctorAvailableSlots($doctorId, Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        
        $slots = DB::table('schedule_slot')
            ->where('doctors_id_doctor', $doctorId)
            ->where('slot_date', $date)
            ->where('is_available', 1)
            ->whereNull('booked_by')
            ->orderBy('slot_time', 'asc')
            ->get()
            ->map(function($slot) {
                return [
                    'id' => $slot->id_schedule_slot,
                    'start_time' => substr($slot->slot_time, 0, 5),
                    'end_time' => substr($slot->end_time, 0, 5)
                ];
            });
        
        return response()->json($slots);
    }
    
    public function bookSlot(Request $request)
    {
        $request->validate([
            'slot_id' => 'required|integer',
            'price_id' => 'nullable|integer',
            'reason' => 'nullable|string'
        ]);
        
        $userId = Auth::check() ? Auth::id() : 1;
        
        try {
            $slot = DB::table('schedule_slot')
                ->where('id_schedule_slot', $request->slot_id)
                ->where('is_available', 1)
                ->whereNull('booked_by')
                ->first();
            
            if (!$slot) {
                return response()->json(['error' => 'Это время уже занято'], 409);
            }
            
            DB::table('schedule_slot')
                ->where('id_schedule_slot', $request->slot_id)
                ->update([
                    'booked_by' => $userId,
                    'is_available' => 0,
                    'updated_at' => now()
                ]);
            
            $appointmentId = DB::table('appointment')->insertGetId([
                'Users_idUser' => $userId,
                'doctors_id_doctor' => $slot->doctors_id_doctor,
                'schedule_slot_id_schedule_slot' => $slot->id_schedule_slot,
                'prices_id_prices' => $request->price_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            if ($request->reason) {
                DB::table('patient_notes')->insert([
                    'reason' => $request->reason,
                    'appointment_id_appointment' => $appointmentId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Вы успешно записаны на прием!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка: ' . $e->getMessage()], 500);
        }
    }
    
    // Получить записи пользователя
    public function getUserAppointments()
    {
        $userId = Auth::check() ? Auth::id() : 1;
        
        $appointments = DB::table('appointment as a')
            ->join('doctors as d', 'a.doctors_id_doctor', '=', 'd.id_doctor')
            ->join('schedule_slot as s', 'a.schedule_slot_id_schedule_slot', '=', 's.id_schedule_slot')
            ->leftJoin('prices as p', 'a.prices_id_prices', '=', 'p.id_prices')
            ->leftJoin('patient_notes as n', 'a.id_appointment', '=', 'n.appointment_id_appointment')
            ->where('a.Users_idUser', $userId)
            ->orderBy('s.slot_date', 'desc')
            ->orderBy('s.slot_time', 'asc')
            ->select(
                'a.id_appointment as id',
                'd.first_name as doctor_first_name',
                'd.last_name as doctor_last_name',
                's.slot_date as date',
                's.slot_time as time',
                'p.description as service',
                'p.price as price',
                'n.reason as reason'
            )
            ->get()
            ->map(function($appointment) {
                return [
                    'id' => $appointment->id,
                    'doctor_name' => $appointment->doctor_first_name . ' ' . $appointment->doctor_last_name,
                    'service' => $appointment->service ?? 'Консультация',
                    'date' => Carbon::parse($appointment->date)->format('d.m.Y'),
                    'time' => substr($appointment->time, 0, 5),
                    'price' => (float)($appointment->price ?? 1500),
                    'reason' => $appointment->reason ?? 'Не указана'
                ];
            });
        
        return response()->json($appointments);
    }
    
    // Отменить запись
    public function cancelAppointment($id)
    {
        $userId = Auth::check() ? Auth::id() : 1;
        
        $appointment = DB::table('appointment')
            ->where('id_appointment', $id)
            ->where('Users_idUser', $userId)
            ->first();
        
        if (!$appointment) {
            return response()->json(['error' => 'Запись не найдена'], 404);
        }
        
        try {
            DB::table('schedule_slot')
                ->where('id_schedule_slot', $appointment->schedule_slot_id_schedule_slot)
                ->update([
                    'booked_by' => null,
                    'is_available' => 1,
                    'updated_at' => now()
                ]);
            
            DB::table('appointment')->where('id_appointment', $id)->delete();
            
            DB::table('patient_notes')->where('appointment_id_appointment', $id)->delete();
            
            return response()->json(['success' => true, 'message' => 'Запись отменена']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка отмены: ' . $e->getMessage()], 500);
        }
    }
}