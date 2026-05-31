<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'review_text' => 'required|string|min:3|max:2000',
        ]);

        $user = Auth::user();

        Review::create([
            'review_text' => $request->review_text,
            'Users_idUser' => $user->idUser,
        ]);

        return redirect()->back()->with('success', 'Спасибо за ваш отзыв!');
    }
}