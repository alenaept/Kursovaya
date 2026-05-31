<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;

class SpecialOfferController extends Controller
{
    public function index()
    {
        try {
            $offers = SpecialOffer::all();
            return response()->json($offers);
        } catch (\Exception $e) {
            \Log::error('SpecialOffer index error: ' . $e->getMessage());
            return response()->json(['error' => 'Ошибка загрузки'], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:45',
                'percent' => 'required|numeric|min:0|max:100',
                'desc' => 'required|string|max:155'
            ]);
            
            $offer = SpecialOffer::create($validated);
            return response()->json($offer, 201);
        } catch (\Exception $e) {
            \Log::error('SpecialOffer store error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $offer = SpecialOffer::findOrFail($id);
            
            $validated = $request->validate([
                'title' => 'string|max:45',
                'percent' => 'numeric|min:0|max:100',
                'desc' => 'string|max:155'
            ]);
            $validated['admin_id_admin'] = auth()->guard('admin')->id() ?? 1;
            $offer->update($validated);
            return response()->json($offer);
        } catch (\Exception $e) {
            \Log::error('SpecialOffer update error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $offer = SpecialOffer::findOrFail($id);
            $offer->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            \Log::error('SpecialOffer destroy error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}