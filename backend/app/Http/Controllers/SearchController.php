<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comic;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->query('query');

        $comics = Comic::where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%")
                    ->with('genres')
                    ->get();

        return response()->json([
            'success' => true,
            'message' => 'Hasil pencarian komik.',
            'data' => $comics
        ]);
    }
}