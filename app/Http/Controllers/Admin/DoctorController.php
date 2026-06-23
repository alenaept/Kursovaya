<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index()
    {
        try {
            $doctors = Doctor::all();
            return response()->json($doctors);
        } catch (\Exception $e) {
            \Log::error('Doctor index error: ' . $e->getMessage());
            return response()->json(['error' => 'Ошибка загрузки'], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:45',
                'last_name' => 'required|string|max:45',
                'email' => 'required|email|unique:doctors,email',
                'phone' => 'nullable|string|max:20',
                'experience_years' => 'nullable|integer|min:0',
                'date_of_birth' => 'nullable|date',
                'photo_url' => 'nullable|url|max:225',
                'password' => 'required|string|min:6'
            ]);
            
            $validated['password'] = Hash::make($validated['password']);
            
            $doctor = Doctor::create($validated);
            
            return response()->json($doctor, 201);
        } catch (\Exception $e) {
            \Log::error('Doctor store error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $doctor = Doctor::findOrFail($id);
            
            $validated = $request->validate([
                'first_name' => 'required|string|max:45',
                'last_name' => 'required|string|max:45',
                'email' => 'required|email|unique:doctors,email,' . $id . ',id_doctor',
                'phone' => 'nullable|string|max:20',
                'experience_years' => 'nullable|integer|min:0',
                'date_of_birth' => 'nullable|date',
                'photo_url' => 'nullable|url|max:225',
                'password' => 'nullable|string|min:6'
            ]);
            
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }
            
            $doctor->update($validated);
            
            return response()->json($doctor);
        } catch (\Exception $e) {
            \Log::error('Doctor update error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $doctor = Doctor::findOrFail($id);
            $doctor->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            \Log::error('Doctor destroy error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}