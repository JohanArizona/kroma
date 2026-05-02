<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Models\Genre;

class DiscoveryController extends Controller
{
    public function popular()
    {
        $comics = Comic::withCount('favorites')
            ->orderByDesc('favorites_count')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar komik populer berhasil dimuat.',
            'data' => $comics
        ], 200);
    }

    public function byGenre($genre_name){
        $genre = Genre::where('name', $genre_name)->first();

        if (!$genre) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori genre tidak ditemukan.',
                'data' => null
            ], 404);
        }

        $comics = $genre->comics()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => "Katalog komik bergenre '{$genre_name}' berhasil dimuat.",
            'data' => $comics
        ], 200);
    }
}