<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Doctor;

class DoctorController extends Controller
{
    
    public function index()
    {
        $doctors = Doctor::select(
                'id_doctor', 
                'first_name', 
                'last_name', 
                'photo_url', 
                'experience_years',
                'email',
                'phone'
            )
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($doctors);
    }
    

    public function show($id)
    {
        $doctor = Doctor::select(
                'id_doctor', 
                'first_name', 
                'last_name', 
                'photo_url', 
                'experience_years',
                'email',
                'phone',
                'date_of_birth'
            )
            ->findOrFail($id);
        
        return response()->json($doctor);
    }
}