<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\PublishedReview;

class ReviewController extends Controller
{
    public function index()
    {
        $publishedReviewIds = PublishedReview::pluck('idreviews')->toArray();

        if (empty($publishedReviewIds)) {
            return response()->json([]);
        }
        
        $reviews = Review::with('user')
            ->whereIn('idreviews', $publishedReviewIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($review) {
                return [
                    'id' => $review->idreviews,
                    'text' => $review->review_text,
                    'user_name' => $review->user->first_name . ' ' . $review->user->last_name,
                    'created_at' => $review->created_at
                ];
            });
        
        return response()->json($reviews);
    }
}