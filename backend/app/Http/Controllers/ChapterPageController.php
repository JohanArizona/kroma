<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChapterPageController extends Controller
{
    // Tampilkan daftar halaman per episode
    public function index($chapter_id)
    {
        $chapter = Chapter::find($chapter_id);

        if (!$chapter) {
            return response()->json([
                'success' => false,
                'message' => 'Data episode tidak ditemukan.'
            ], 404);
        }

        // Gunakan DB::table() agar id dikembalikan sebagai nilai asli DB (tidak di-cast oleh model)
        $pages = DB::table('chapter_pages')
            ->where('chapter_id', (string) $chapter->id)
            ->orderBy('page_number', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar halaman berhasil dimuat.',
            'data'    => $pages
        ], 200);
    }

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
        $lastPage      = DB::table('chapter_pages')
                            ->where('chapter_id', (string) $chapter->id)
                            ->max('page_number') ?? 0;

        foreach ($request->file('pages') as $index => $file) {
            $pageNumber      = $lastPage + $index + 1;
            $path            = $file->store("comics/chapters/{$chapter->id}", 'public');

            $uploadedPages[] = [
                'id'          => (string) Str::uuid(),
                'chapter_id'  => (string) $chapter->id,
                'page_number' => $pageNumber,
                'image_url'   => $path,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp
            ];
        }

        DB::table('chapter_pages')->insert($uploadedPages);

        return response()->json([
            'success' => true,
            'message' => count($uploadedPages) . ' halaman berhasil diunggah.',
            'data'    => $uploadedPages
        ], 201);
    }

    // Hapus satu halaman berdasarkan page_number (lebih reliable dari id)
    public function deletePage($chapter_id, $page_number)
    {
        $chapter = Chapter::find($chapter_id);

        if (!$chapter) {
            return response()->json([
                'success' => false,
                'message' => 'Data episode tidak ditemukan.'
            ], 404);
        }

        $page = DB::table('chapter_pages')
            ->where('chapter_id', (string) $chapter->id)
            ->where('page_number', (int) $page_number)
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Halaman tidak ditemukan.'
            ], 404);
        }

        // Hapus file fisik dari storage
        if ($page->image_url) {
            Storage::disk('public')->delete($page->image_url);
        }

        // Hapus record dari DB
        DB::table('chapter_pages')
            ->where('chapter_id', (string) $chapter->id)
            ->where('page_number', (int) $page_number)
            ->delete();

        // Rapikan ulang nomor halaman setelah penghapusan
        $remaining = DB::table('chapter_pages')
            ->where('chapter_id', (string) $chapter->id)
            ->orderBy('page_number', 'asc')
            ->get();

        foreach ($remaining as $idx => $p) {
            DB::table('chapter_pages')
                ->where('chapter_id', (string) $chapter->id)
                ->where('id', $p->id)
                ->update(['page_number' => $idx + 1, 'updated_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Halaman berhasil dihapus dan urutan dirapikan.'
        ], 200);
    }

    // 3.3.5 - Reorder Pages
    public function reorder(Request $request, $chapter_id)
    {
        $validator = Validator::make($request->all(), [
            'pages'               => 'required|array',
            'pages.*.id'          => 'required',
            'pages.*.page_number' => 'required|integer|min:1|distinct'
        ], [
            'pages.required'               => 'Data halaman tidak boleh kosong.',
            'pages.*.id.required'          => 'ID halaman tidak boleh kosong.',
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
            // Pass 1: Set ke nilai sementara untuk hindari unique constraint conflict.
            // Cast id ke string agar MySQL gunakan string comparison pada kolom char(36).
            foreach ($request->pages as $index => $pageData) {
                DB::table('chapter_pages')
                    ->where('id', (string) $pageData['id'])
                    ->update([
                        'page_number' => 10000 + $index,
                        'updated_at'  => now()
                    ]);
            }

            // Pass 2: Set ke nilai akhir yang diinginkan
            foreach ($request->pages as $pageData) {
                DB::table('chapter_pages')
                    ->where('id', (string) $pageData['id'])
                    ->update([
                        'page_number' => $pageData['page_number'],
                        'updated_at'  => now()
                    ]);
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
                'message' => 'Gagal memperbarui urutan halaman: ' . $e->getMessage()
            ], 500);
        }
    }
}