<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{

    public function index()
    {
        $services = Service::select('id_services', 'name', 'description', 'price')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($services);
    }
    

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }
}