<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SpecialOffer;

class SpecialOfferController extends Controller
{
    public function index()
    {
        $offers = SpecialOffer::select('id_specialOffers', 'title', 'percent', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($offers);
    }
    
    public function show($id)
    {
        $offer = SpecialOffer::findOrFail($id);
        return response()->json($offer);
    }
}