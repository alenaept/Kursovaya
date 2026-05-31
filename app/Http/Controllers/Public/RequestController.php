<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;

class RequestController extends Controller
{
    
    public function store(HttpRequest $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:45',
            'phone' => 'required|string|max:20'
        ]);
        
        $requestModel = Request::create($validated);
        
        return response()->json([
            'message' => 'Заявка успешно отправлена',
            'request' => $requestModel
        ], 201);
    }
}