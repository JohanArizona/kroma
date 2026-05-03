<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\ChapterPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChapterController extends Controller
{
    // 3.3.1 - Tambah Episode Baru
    public function store(Request $request, $comic_id)
    {
        $comic = Comic::find($comic_id);

        if (!$comic) {
            return response()->json([
                'success' => false,
                'message' => 'Data komik tidak ditemukan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'chapter_number' => 'required|numeric|min:0',
            'title'          => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $chapterExists = Chapter::where('comic_id', $comic->id)
            ->where('chapter_number', $request->chapter_number)
            ->exists();

        if ($chapterExists) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor episode sudah ada pada komik ini.'
            ], 409);
        }

        $chapter = Chapter::create([
            'id'             => Str::uuid(),
            'comic_id'       => $comic->id,
            'chapter_number' => $request->chapter_number,
            'title'          => $request->title,
            'created_by'     => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Episode baru berhasil ditambahkan.',
            'data'    => $chapter
        ], 201);
    }

    // 3.3.3 - Update Metadata Episode
    public function update(Request $request, $id)
    {
        $chapter = Chapter::find($id);

        if (!$chapter) {
            return response()->json([
                'success' => false,
                'message' => 'Data episode tidak ditemukan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'chapter_number' => 'sometimes|numeric|min:0',
            'title'          => 'sometimes|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $validatedData = $validator->validated();

        if (isset($validatedData['chapter_number']) &&
            $validatedData['chapter_number'] != $chapter->chapter_number) {

            $exists = Chapter::where('comic_id', $chapter->comic_id)
                ->where('chapter_number', $validatedData['chapter_number'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor episode sudah ada pada komik ini.'
                ], 409);
            }
        }

        $validatedData['updated_by'] = Auth::id();
        $chapter->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Detail episode berhasil diperbarui.',
            'data'    => $chapter
        ], 200);
    }

    // 3.3.4 - Hapus Episode
    public function destroy($id)
    {
        $chapter = Chapter::find($id);

        if (!$chapter) {
            return response()->json([
                'success' => false,
                'message' => 'Data episode tidak ditemukan.'
            ], 404);
        }

        Storage::disk('public')->deleteDirectory("comics/chapters/{$chapter->id}");
        $chapter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Episode beserta seluruh halamannya berhasil dihapus.'
        ], 200);
    }
}