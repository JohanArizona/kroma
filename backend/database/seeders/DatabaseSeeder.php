<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // ---------------------------------------------------
        // 1. SEEDER USERS (1 Admin, 1 Member)
        // ---------------------------------------------------
        $adminId = Str::uuid();
        $memberId = Str::uuid();

        DB::table('users')->insert([
            [
                'id' => $adminId,
                'name' => 'Admin Kroma',
                'email' => 'admin@kroma.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'avatar_url' => 'default-avatar.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $memberId,
                'name' => 'Member Kroma',
                'email' => 'member@kroma.com',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'avatar_url' => 'default-avatar.png',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        // ---------------------------------------------------
        // 2. SEEDER GENRES
        // ---------------------------------------------------
        $genreActionId = Str::uuid();
        $genreComedyId = Str::uuid();

        DB::table('genres')->insert([
            ['id' => $genreActionId, 'name' => 'Action', 'created_at' => $now, 'updated_at' => $now],
            ['id' => $genreComedyId, 'name' => 'Comedy', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ---------------------------------------------------
        // 3. SEEDER COMICS (Dibuat oleh Admin)
        // ---------------------------------------------------
        $comicId = Str::uuid();

        DB::table('comics')->insert([
            'id' => $comicId,
            'title' => 'Petualangan Mahasiswa Fasilkom',
            'author' => 'Johan Arizona',
            'synopsis' => 'Kisah epik sebuah tim menyelesaikan backend Kroma.',
            'status' => 'ongoing',
            'cover_url' => 'default-cover.jpg', // Placeholder Gambar Kosong
            'banner_url' => null,
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ---------------------------------------------------
        // 4. SEEDER COMIC_GENRE (Tabel Pivot)
        // ---------------------------------------------------
        DB::table('comic_genre')->insert([
            'comic_id' => $comicId,
            'genre_id' => $genreActionId,
        ]);

        // ---------------------------------------------------
        // 5. SEEDER CHAPTERS
        // ---------------------------------------------------
        $chapterId = Str::uuid();

        DB::table('chapters')->insert([
            'id' => $chapterId,
            'comic_id' => $comicId,
            'chapter_number' => 1.00,
            'title' => 'Awal Mula Migrasi',
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ---------------------------------------------------
        // 6. SEEDER CHAPTER PAGES
        // ---------------------------------------------------
        DB::table('chapter_pages')->insert([
            [
                'id' => Str::uuid(),
                'chapter_id' => $chapterId,
                'page_number' => 1,
                'image_url' => 'default-page-1.jpg', // Placeholder
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'chapter_id' => $chapterId,
                'page_number' => 2,
                'image_url' => 'default-page-2.jpg', // Placeholder
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        // ---------------------------------------------------
        // 7. SEEDER INTERAKSI MEMBER (Favorit, Komentar, Histori)
        // ---------------------------------------------------
        DB::table('favorites')->insert([
            'id' => Str::uuid(),
            'user_id' => $memberId,
            'comic_id' => $comicId,
            'created_at' => $now,
        ]);

        DB::table('comments')->insert([
            'id' => Str::uuid(),
            'user_id' => $memberId,
            'chapter_id' => $chapterId,
            'content' => 'Wah gila sih ini seru banget komiknya bang!',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('reading_histories')->insert([
            'id' => Str::uuid(),
            'user_id' => $memberId,
            'comic_id' => $comicId,
            'chapter_id' => $chapterId,
            'last_read_at' => $now,
            'created_at' => $now,
        ]);
    }
}