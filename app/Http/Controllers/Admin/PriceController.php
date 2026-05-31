<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{

    public function index()
    {
        return response()->json(Price::orderBy('category')->get());
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:45',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0'
        ]);
        
        $price = Price::create($validated);
        
        return response()->json($price, 201);
    }
    
    public function update(Request $request, $id)
    {
        $price = Price::findOrFail($id);
        
        $validated = $request->validate([
            'category' => 'string|max:45',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0'
        ]);
        $validated['admin_id_admin'] = auth()->guard('admin')->id() ?? 1;
        $price->update($validated);
        
        return response()->json($price);
    }
    
    // Удаление цены
    public function destroy($id)
    {
        $price = Price::findOrFail($id);
        $price->delete();
        
        return response()->json(null, 204);
    }
}