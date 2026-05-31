<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Models\Admin;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index()
    {
        $requests = Request::orderBy('created_at', 'desc')
            ->get()
            ->map(function($request) {
                $request->is_locked = $request->status === 'processing';
                $request->locked_by = $request->processedBy ? $request->processedBy->first_name . ' ' . $request->processedBy->last_name : null;
                return $request;
            });
        
        return response()->json($requests);
    }
    

    public function lock($id)
    {
        $request = Request::findOrFail($id);
        
        if ($request->status === 'processing') {
            return response()->json(['error' => 'Заявка уже обрабатывается другим администратором'], 409);
        }
        

        $adminId = auth()->guard('admin')->id() ?? 1;
        
        $request->update([
            'status' => 'processing',
            'processed_by' => $adminId,
            'processed_at' => now()
        ]);
        
        return response()->json(['message' => 'Заявка закрыта для других администраторов']);
    }
    
    
    public function destroy($id)
    {
        $request = Request::findOrFail($id);
        $request->delete();
        return response()->json(null, 204);
    }
}