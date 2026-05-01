<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Models\Genre;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComicController extends Controller
{
    public function index()
    {
        $comics = Comic::with('genres')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar komik berhasil dimuat.',
            'data' => $comics
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:comics',
            'author' => 'required|string|max:255',
            'synopsis' => 'required|string',
            'status' => 'required|in:ongoing,completed',
            'genres' => 'required|array',
            'cover' => 'required|image|max:2048',
            'banner' => 'nullable|image|max:4096'
        ]);

        $coverPath = $request->file('cover')->store('comics/covers', 'public');
        $bannerPath = $request->hasFile('banner') ? $request->file('banner')->store('comics/banners', 'public') : null;

        $comic = Comic::create([
            'id' => Str::uuid(),
            'title' => $request->title,
            'author' => $request->author,
            'synopsis' => $request->synopsis,
            'status' => $request->status,
            'cover_url' => $coverPath,
            'banner_url' => $bannerPath,
            'created_by' => Auth::id()
        ]);

        $genreIds = Genre::whereIn('name', $request->genres)->pluck('id');
        $comic->genres()->attach($genreIds);

        return response()->json([
            'success' => true,
            'message' => 'Data komik berhasil ditambahkan.',
            'data' => $comic->load('genres')
        ], 201);
    }

    public function updateMetadata(Request $request, $id)
    {
        $comic = Comic::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255|unique:comics,title,' . $id,
            'author' => 'sometimes|string|max:255',
            'synopsis' => 'sometimes|string',
            'status' => 'sometimes|in:ongoing,completed',
            'genres' => 'sometimes|array'
        ]);

        if ($request->has('genres')) {
            $genreIds = Genre::whereIn('name', $request->genres)->pluck('id');
            $comic->genres()->sync($genreIds);
            unset($validatedData['genres']);
        }

        $validatedData['updated_by'] = Auth::id();
        $comic->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Metadata komik berhasil diperbarui.',
            'data' => $comic->load('genres')
        ], 200);
    }

    public function updateMedia(Request $request, $id)
    {
        $comic = Comic::findOrFail($id);

        $request->validate([
            'cover' => 'sometimes|image|max:2048',
            'banner' => 'sometimes|image|max:4096'
        ]);

        $updateData = ['updated_by' => Auth::id()];

        if ($request->hasFile('cover')) {
            if ($comic->cover_url) {
                Storage::disk('public')->delete($comic->cover_url);
            }
            $updateData['cover_url'] = $request->file('cover')->store('comics/covers', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($comic->banner_url) {
                Storage::disk('public')->delete($comic->banner_url);
            }
            $updateData['banner_url'] = $request->file('banner')->store('comics/banners', 'public');
        }

        $comic->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Aset visual komik berhasil diperbarui.',
            'data' => $comic
        ], 200);
    }

    public function destroy($id)
    {
        $comic = Comic::findOrFail($id);

        if ($comic->cover_url) Storage::disk('public')->delete($comic->cover_url);
        if ($comic->banner_url) Storage::disk('public')->delete($comic->banner_url);
        
        $comic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data komik dan seluruh relasinya berhasil dihapus.'
        ], 200);
    }
}