<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function getFavorites()
    {
        $favorites = Favorite::with('comic:id,title,cover_url')
                             ->where('user_id', Auth::id())
                             ->orderBy('created_at', 'desc')
                             ->get()
                             ->map(function ($fav) {
                                 return [
                                     'favorite_id' => $fav->id,
                                     'comic' => $fav->comic,
                                     'added_at' => $fav->created_at
                                 ];
                             });

        return response()->json([
            'success' => true,
            'message' => 'Daftar pustaka pribadi berhasil dimuat.',
            'data' => $favorites
        ], 200);
    }

    public function addFavorite(Request $request)
    {
        $request->validate([
            'comic_id' => 'required|exists:comics,id'
        ]);

        $userId = Auth::id();

        $exists = Favorite::where('user_id', $userId)
                          ->where('comic_id', $request->comic_id)
                          ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Komik ini sudah berada di daftar favorit Anda.'
            ], 409);
        }

        $favorite = Favorite::create([
            'user_id' => $userId,
            'comic_id' => $request->comic_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komik berhasil ditambahkan ke pustaka favorit.',
            'data' => $favorite
        ], 201);
    }

    public function removeFavorite($comic_id)
    {
        $favorite = Favorite::where('user_id', Auth::id())
                            ->where('comic_id', $comic_id)
                            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Komik tidak ditemukan di dalam pustaka Anda.'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komik berhasil dihapus dari daftar favorit.'
        ], 200);
    }
}