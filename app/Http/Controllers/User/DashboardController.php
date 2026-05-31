<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $appointments = DB::table('appointment as a')
            ->join('doctors as d', 'a.doctors_id_doctor', '=', 'd.id_doctor')
            ->join('schedule_slot as s', 'a.schedule_slot_id_schedule_slot', '=', 's.id_schedule_slot')
            ->leftJoin('prices as p', 'a.prices_id_prices', '=', 'p.id_prices')
            ->leftJoin('patient_notes as n', 'a.id_appointment', '=', 'n.appointment_id_appointment')
            ->where('a.Users_idUser', $user->idUser)
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
        

        $reviews = Review::where('Users_idUser', $user->idUser)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return Inertia::render('User/Dashboard', [
            'user' => $user,
            'appointments' => $appointments,
            'reviews' => $reviews,
            'success' => session('success')
        ]);
    }
}