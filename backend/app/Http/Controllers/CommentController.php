<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CommentController extends Controller
{
    public function store(Request $request, $chapter_id)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $comment = Comment::create([
            'chapter_id' => $chapter_id,
            'user_id' => JWTAuth::parseToken()->authenticate()->id,
            'content' => $request->content
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil ditambahkan.',
            'data' => $comment
        ], 201);
    }

    public function index($chapter_id)
    {
        $comments = Comment::with('user')
                    ->where('chapter_id', $chapter_id)
                    ->latest()
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::where('user_id', JWTAuth::parseToken()->authenticate()->id)->findOrFail($id);

        $comment->update([
            'content' => $request->content
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil diperbarui.',
            'data' => $comment
        ]);
    }

    public function destroy($id)
    {
        $comment = Comment::where('user_id', JWTAuth::parseToken()->authenticate()->id)->findOrFail($id);
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dihapus.'
        ]);
    }
}