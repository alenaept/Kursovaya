<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index()
    {
        try {
            $reviews = DB::table('reviews as r')
                ->leftJoin('users as u', 'r.Users_idUser', '=', 'u.idUser')
                ->select('r.*', 'u.first_name', 'u.last_name')
                ->orderBy('r.created_at', 'desc')
                ->get()
                ->map(function($review) {
                    $exists = DB::table('published_reviews')
                        ->where('idreviews', $review->idreviews)
                        ->exists();
                    
                    $review->is_published = $exists;
                    $review->user = (object)[
                        'first_name' => $review->first_name,
                        'last_name' => $review->last_name
                    ];
                    return $review;
                });
            
            return response()->json($reviews);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function publish($id)
    {
        try {
            $exists = DB::table('published_reviews')->where('idreviews', $id)->exists();
            
            if (!$exists) {
                DB::table('published_reviews')->insert([
                    'idreviews' => $id,
                    'published_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json(['message' => 'Отзыв опубликован']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function unpublish($id)
    {
        try {
            DB::table('published_reviews')->where('idreviews', $id)->delete();
            return response()->json(['message' => 'Отзыв снят с публикации']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            DB::table('published_reviews')->where('idreviews', $id)->delete();
            DB::table('reviews')->where('idreviews', $id)->delete();
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}