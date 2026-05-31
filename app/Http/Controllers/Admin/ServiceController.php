<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        try {
            $services = Service::all();
            return response()->json($services);
        } catch (\Exception $e) {
            \Log::error('Service index error: ' . $e->getMessage());
            return response()->json(['error' => 'Ошибка загрузки'], 500);
        }
    }
    
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:45',
                'description' => 'required|string|max:225',
                'price' => 'required|numeric|min:0'
            ]);
            
            $service = Service::create($validated);
            return response()->json($service, 201);
        } catch (\Exception $e) {
            \Log::error('Service store error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'string|max:45',
                'description' => 'string|max:225',
                'price' => 'numeric|min:0'
            ]);
            $validated['admin_id_admin'] = auth()->guard('admin')->id() ?? 1;
            $service->update($validated);
            return response()->json($service);
        } catch (\Exception $e) {
            \Log::error('Service update error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            \Log::error('Service destroy error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}