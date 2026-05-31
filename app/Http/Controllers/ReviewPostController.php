<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewPostController extends Controller
{
    /**
     * Получить все отзывы
     */
    public function index()
    {
        $reviews = Review::with('user:id,name,surname')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($review) {
                return [
                    'id' => $review->id,
                    'review_text' => $review->review_text,
                    'created_at' => $review->created_at,
                    'user_name' => $review->user ? $review->user->name : 'Пользователь',
                    'user_surname' => $review->user ? $review->user->surname : ''
                ];
            });
        
        return response()->json($reviews);
    }

    /**
     * Получить последние N отзывов
     */
    public function latest($limit = 50)
    {
        $reviews = Review::with('user:id,name,surname')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($review) {
                return [
                    'id' => $review->id,
                    'review_text' => $review->review_text,
                    'created_at' => $review->created_at,
                    'user_name' => $review->user ? $review->user->name : 'Пользователь',
                    'user_surname' => $review->user ? $review->user->surname : ''
                ];
            });
        
        return response()->json($reviews);
    }

    /**
     * Получить отзывы с пагинацией
     */
    public function paginate(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $reviews = Review::with('user:id,name,surname')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        $reviews->getCollection()->transform(function($review) {
            return [
                'id' => $review->id,
                'review_text' => $review->review_text,
                'created_at' => $review->created_at,
                'user_name' => $review->user ? $review->user->name : 'Пользователь',
                'user_surname' => $review->user ? $review->user->surname : ''
            ];
        });
        
        return response()->json($reviews);
    }

    /**
     * Создать новый отзыв
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'review_text' => 'required|string|min:3|max:1000',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $review = Review::create([
            'review_text' => $request->review_text,
            'Users_idUser' => $request->user_id,
            'created_at' => now()
        ]);

        $review->load('user:id,name,surname');

        return response()->json([
            'id' => $review->id,
            'review_text' => $review->review_text,
            'created_at' => $review->created_at,
            'user_name' => $review->user ? $review->user->name : 'Пользователь',
            'user_surname' => $review->user ? $review->user->surname : ''
        ], 201);
    }

    /**
     * Показать один отзыв
     */
    public function show($id)
    {
        $review = Review::with('user:id,name,surname')->find($id);
        
        if (!$review) {
            return response()->json([
                'message' => 'Отзыв не найден'
            ], 404);
        }
        
        return response()->json([
            'id' => $review->id,
            'review_text' => $review->review_text,
            'created_at' => $review->created_at,
            'user_name' => $review->user ? $review->user->name : 'Пользователь',
            'user_surname' => $review->user ? $review->user->surname : ''
        ]);
    }

    /**
     * Обновить отзыв
     */
    public function update(Request $request, $id)
    {
        $review = Review::find($id);
        
        if (!$review) {
            return response()->json([
                'message' => 'Отзыв не найден'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'review_text' => 'required|string|min:3|max:1000'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        
        $review->update([
            'review_text' => $request->review_text
        ]);
        
        $review->load('user:id,name,surname');
        
        return response()->json([
            'id' => $review->id,
            'review_text' => $review->review_text,
            'created_at' => $review->created_at,
            'user_name' => $review->user ? $review->user->name : 'Пользователь',
            'user_surname' => $review->user ? $review->user->surname : ''
        ]);
    }

    public function destroy($id)
    {
        $review = Review::find($id);
        
        if (!$review) {
            return response()->json([
                'message' => 'Отзыв не найден'
            ], 404);
        }
        
        $review->delete();
        
        return response()->json([
            'message' => 'Отзыв успешно удален'
        ]);
    }
}