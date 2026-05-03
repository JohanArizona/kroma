<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChapterPageController extends Controller
{
    // 3.3.2 - Bulk Upload Pages
    public function bulkUpload(Request $request, $chapter_id)
    {
        $validator = Validator::make($request->all(), [
            'pages'   => 'required|array',
            'pages.*' => 'required|image|mimes:jpeg,png,webp|max:2048'
        ], [
            'pages.required'   => 'Harap unggah minimal satu berkas gambar.',
            'pages.*.required' => 'Harap unggah minimal satu berkas gambar.',
            'pages.*.image'    => 'Berkas yang diunggah harus berupa gambar.',
            'pages.*.mimes'    => 'Format gambar tidak didukung. Gunakan jpeg, png, atau webp.',
            'pages.*.max'      => 'Berkas yang diunggah harus berupa gambar (maks. 2MB per file).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $chapter = Chapter::find($chapter_id);

        if (!$chapter) {
            return response()->json([
                'success' => false,
                'message' => 'Data episode tidak ditemukan.'
            ], 404);
        }

        $uploadedPages = [];
        $timestamp     = now();
        $lastPage      = ChapterPage::where('chapter_id', $chapter->id)
                            ->max('page_number') ?? 0;

        foreach ($request->file('pages') as $index => $file) {
            $pageNumber      = $lastPage + $index + 1;
            $path            = $file->store("comics/chapters/{$chapter->id}", 'public');

            $uploadedPages[] = [
                'id'          => Str::uuid(),
                'chapter_id'  => $chapter->id,
                'page_number' => $pageNumber,
                'image_url'   => $path,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp
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
        $validator = Validator::make($request->all(), [
            'pages'               => 'required|array',
            'pages.*.id'          => 'required|exists:chapter_pages,id',
            'pages.*.page_number' => 'required|integer|min:1|distinct'
        ], [
            'pages.required'               => 'Data halaman tidak boleh kosong.',
            'pages.*.id.required'          => 'ID halaman tidak boleh kosong.',
            'pages.*.id.exists'            => 'ID halaman tidak ditemukan.',
            'pages.*.page_number.required' => 'Nomor halaman tidak boleh kosong.',
            'pages.*.page_number.integer'  => 'Nomor halaman harus berupa angka.',
            'pages.*.page_number.min'      => 'Nomor halaman minimal 1.',
            'pages.*.page_number.distinct' => 'Nomor halaman tidak boleh duplikat.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $chapter = Chapter::find($chapter_id);

        if (!$chapter) {
            return response()->json([
                'success' => false,
                'message' => 'Data episode tidak ditemukan.'
            ], 404);
        }

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