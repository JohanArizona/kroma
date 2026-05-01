<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil dimuat.',
            'data' => $user
        ], 200);
    }
    
    public function updateName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = Auth::user();
        $user->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Nama pengguna berhasil diperbarui.',
            'data' => $user
        ], 200);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::user();

        // Hapus avatar lama jika bukan avatar default
        if ($user->avatar_url && $user->avatar_url !== 'default-avatar.png') {
            Storage::disk('public')->delete($user->avatar_url);
        }

        // Simpan gambar baru ke folder storage/app/public/avatars
        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->update(['avatar_url' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar berhasil diperbarui.',
            'data' => $user
        ], 200);
    }

    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar_url === 'default-avatar.png') {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak memiliki avatar kustom.'
            ], 400);
        }

        // Hapus file fisik dari server
        Storage::disk('public')->delete($user->avatar_url);
        
        // Kembalikan ke default
        $user->update(['avatar_url' => 'default-avatar.png']);

        return response()->json([
            'success' => true,
            'message' => 'Avatar kustom berhasil dihapus.',
            'data' => $user
        ], 200);
    }
}