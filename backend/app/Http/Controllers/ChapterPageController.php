<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChapterPageController extends Controller
{
    // 3.3.2 - Bulk Upload Pages
    public function bulkUpload(Request $request, $chapter_id)
    {
        $request->validate([
            'pages'   => 'required|array',
            'pages.*' => 'required|image|mimes:jpeg,png,webp|max:2048'
        ]);

        $chapter = Chapter::findOrFail($chapter_id);

        $uploadedPages = [];
        $timestamp     = now();
        $lastPage      = ChapterPage::where('chapter_id', $chapter->id)
                            ->max('page_number') ?? 0;

        foreach ($request->file('pages') as $index => $file) {
            $pageNumber      = $lastPage + $index + 1;
            $path            = $file->store("comics/chapters/{$chapter->id}", 'public');

            $uploadedPages[] = [
                'id'         => Str::uuid(),
                'chapter_id' => $chapter->id,
                'page_number'=> $pageNumber,
                'image_url'  => $path,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        ChapterPage::insert($uploadedPages);

        return response()->json([
            'success' => true,
            'message' => count($uploadedPages) . ' halaman berhasil diunggah.',
            'data'    => $uploadedPages
        ], 201);
    }

    // 3.3.5 - Reorder Pages
    public function reorder(Request $request, $chapter_id)
    {
        $request->validate([
            'pages'              => 'required|array',
            'pages.*.id'         => 'required|exists:chapter_pages,id',
            'pages.*.page_number'=> 'required|integer|min:1|distinct'
        ]);

        $chapter = Chapter::findOrFail($chapter_id);

        DB::beginTransaction();
        try {
            foreach ($request->pages as $pageData) {
                ChapterPage::where('chapter_id', $chapter->id)
                    ->where('id', $pageData['id'])
                    ->update(['page_number' => $pageData['page_number']]);
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan halaman berhasil diperbarui.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan halaman.'
            ], 500);
        }
    }
}