<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Comic;
use App\Models\Chapter;

class DashboardController extends Controller
{
    public function getStats()
    {
        $totalUsers    = User::where('role', 'member')->count();
        $totalComics   = Comic::count();
        $totalChapters = Chapter::count();

        return response()->json([
            'success' => true,
            'message' => 'Statistik dasbor berhasil dimuat.',
            'data'    => [
                'total_users'    => $totalUsers,
                'total_comics'   => $totalComics,
                'total_chapters' => $totalChapters
            ]
        ], 200);
    }
}