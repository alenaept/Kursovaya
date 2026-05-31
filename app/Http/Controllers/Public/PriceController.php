<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Price;

class PriceController extends Controller
{
    public function getGroupedPrices()
    {
        try {
            $prices = Price::all();
            
            if ($prices->isEmpty()) {
                return response()->json([]);
            }
            
            $grouped = [];
            
            foreach ($prices as $price) {
                $category = $price->category ?? 'Услуги';
                
                if (!isset($grouped[$category])) {
                    $grouped[$category] = [];
                }
                
                $grouped[$category][] = [
                    'id' => $price->id_prices,
                    'description' => $price->description,
                    'price' => $price->price
                ];
            }
            
            return response()->json($grouped);
        } catch (\Exception $e) {
            \Log::error('Price grouped error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        $prices = Price::all();
        return response()->json($prices);
    }
}