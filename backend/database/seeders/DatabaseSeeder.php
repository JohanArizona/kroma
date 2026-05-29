<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ---------------------------------------------------
        // 1. SEEDER USERS (1 Admin, 1 Member)
        // ---------------------------------------------------
        $adminId  = Str::uuid();
        $memberId = Str::uuid();

        DB::table('users')->insert([
            [
                'id'         => $adminId,
                'name'       => 'Admin Kroma',
                'email'      => 'admin@kroma.com',
                'password'   => Hash::make('password123'),
                'role'       => 'admin',
                'avatar_url' => 'default-avatar.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => $memberId,
                'name'       => 'Member Kroma',
                'email'      => 'member@kroma.com',
                'password'   => Hash::make('password123'),
                'role'       => 'member',
                'avatar_url' => 'default-avatar.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // ---------------------------------------------------
        // 2. SEEDER GENRES
        // ---------------------------------------------------
        $genres = [
            'Action', 'Comedy', 'Drama', 'Fantasy',
            'Horror', 'Mystery', 'Romance', 'Sci-Fi',
            'Slice of Life', 'Supernatural',
        ];

        $genreIds = [];
        foreach ($genres as $name) {
            $id = Str::uuid();
            $genreIds[$name] = $id;
            DB::table('genres')->insert([
                'id'         => $id,
                'name'       => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ---------------------------------------------------
        // 3. SEEDER COMICS + PIVOT
        // Cover URL pakai Open Library, format:
        // https://covers.openlibrary.org/b/isbn/{ISBN}-L.jpg
        // ---------------------------------------------------
        $comics = [
            [
                'title'    => 'One Piece',
                'author'   => 'Eiichiro Oda',
                'synopsis' => 'Monkey D. Luffy berlayar mengarungi lautan untuk menemukan harta karun legendaris One Piece dan menjadi Raja Bajak Laut.',
                'status'   => 'ongoing',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781569319017-L.jpg',
                'genres'   => ['Action', 'Comedy', 'Fantasy'],
            ],
            [
                'title'    => 'Naruto',
                'author'   => 'Masashi Kishimoto',
                'synopsis' => 'Naruto Uzumaki, ninja muda dengan rubah ekor sembilan bersegel di dalam tubuhnya, berjuang untuk mendapatkan pengakuan dan menjadi Hokage.',
                'status'   => 'completed',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781569319000-L.jpg',
                'genres'   => ['Action', 'Drama', 'Fantasy'],
            ],
            [
                'title'    => 'Attack on Titan',
                'author'   => 'Hajime Isayama',
                'synopsis' => 'Umat manusia berlindung di balik tiga tembok raksasa dari serangan Titan, makhluk pemangsa manusia yang misterius.',
                'status'   => 'completed',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781612620244-L.jpg',
                'genres'   => ['Action', 'Drama', 'Horror'],
            ],
            [
                'title'    => 'Death Note',
                'author'   => 'Tsugumi Ohba',
                'synopsis' => 'Light Yagami menemukan buku catatan maut yang bisa membunuh siapapun yang namanya ditulis di dalamnya, dan memutuskan untuk membersihkan dunia dari kejahatan.',
                'status'   => 'completed',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781421501680-L.jpg',
                'genres'   => ['Mystery', 'Supernatural', 'Drama'],
            ],
            [
                'title'    => 'Fullmetal Alchemist',
                'author'   => 'Hiromu Arakawa',
                'synopsis' => 'Dua bersaudara, Edward dan Alphonse Elric, mencari Philosopher\'s Stone untuk memulihkan tubuh mereka setelah sebuah ritual alkimia yang gagal.',
                'status'   => 'completed',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781591169208-L.jpg',
                'genres'   => ['Action', 'Fantasy', 'Drama'],
            ],
            [
                'title'    => 'Demon Slayer: Kimetsu no Yaiba',
                'author'   => 'Koyoharu Gotouge',
                'synopsis' => 'Tanjiro Kamado bergabung dengan Demon Slayer Corps untuk membalas dendam atas keluarganya dan menyembuhkan adiknya yang berubah menjadi iblis.',
                'status'   => 'completed',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781974700523-L.jpg',
                'genres'   => ['Action', 'Supernatural', 'Drama'],
            ],
            [
                'title'    => 'Jujutsu Kaisen',
                'author'   => 'Gege Akutami',
                'synopsis' => 'Yuji Itadori menelan jari iblis legendaris Ryomen Sukuna dan terpaksa masuk ke dunia para penyihir jujutsu untuk menghadapi kutukan-kutukan mematikan.',
                'status'   => 'ongoing',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781974710027-L.jpg',
                'genres'   => ['Action', 'Supernatural', 'Horror'],
            ],
            [
                'title'    => 'My Hero Academia',
                'author'   => 'Kohei Horikoshi',
                'synopsis' => 'Di dunia di mana sebagian besar orang memiliki kekuatan super, Izuku Midoriya yang terlahir tanpa kekuatan bermimpi menjadi pahlawan terbesar.',
                'status'   => 'ongoing',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781421582696-L.jpg',
                'genres'   => ['Action', 'Comedy', 'Drama'],
            ],
            [
                'title'    => 'Hunter x Hunter',
                'author'   => 'Yoshihiro Togashi',
                'synopsis' => 'Gon Freecss meninggalkan pulau asalnya untuk menjadi Hunter dan mencari ayahnya yang misterius, Ging, seorang Hunter legendaris.',
                'status'   => 'ongoing',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781591167532-L.jpg',
                'genres'   => ['Action', 'Fantasy', 'Mystery'],
            ],
            [
                'title'    => 'Bleach',
                'author'   => 'Tite Kubo',
                'synopsis' => 'Ichigo Kurosaki yang memiliki kemampuan melihat roh mendapatkan kekuatan Shinigami dan harus melindungi orang-orang yang dicintainya dari ancaman Hollow.',
                'status'   => 'completed',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781591164418-L.jpg',
                'genres'   => ['Action', 'Supernatural', 'Comedy'],
            ],
            [
                'title'    => 'Dragon Ball',
                'author'   => 'Akira Toriyama',
                'synopsis' => 'Son Goku, anak laki-laki dengan ekor monyet dan kekuatan luar biasa, berpetualang mencari tujuh bola naga yang dapat mengabulkan segala keinginan.',
                'status'   => 'completed',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781569319208-L.jpg',
                'genres'   => ['Action', 'Comedy', 'Fantasy'],
            ],
            [
                'title'    => 'Tokyo Ghoul',
                'author'   => 'Sui Ishida',
                'synopsis' => 'Ken Kaneki menjadi setengah ghoul setelah serangan brutal dan harus berjuang mempertahankan kemanusiaannya di tengah konflik antara manusia dan ghoul.',
                'status'   => 'completed',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781421580364-L.jpg',
                'genres'   => ['Action', 'Horror', 'Drama'],
            ],
            [
                'title'    => 'Chainsaw Man',
                'author'   => 'Tatsuki Fujimoto',
                'synopsis' => 'Denji, pemuda miskin pemburu iblis, menyatu dengan anjing iblisnya Pochita dan menjadi Chainsaw Man, senjata ampuh biro publik pemburu iblis.',
                'status'   => 'ongoing',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781974709939-L.jpg',
                'genres'   => ['Action', 'Horror', 'Comedy'],
            ],
            [
                'title'    => 'The Promised Neverland',
                'author'   => 'Kaiu Shirai',
                'synopsis' => 'Emma, Norman, dan Ray menemukan rahasia gelap di balik kehidupan panti asuhan Grace Field yang tampak sempurna dan berjuang untuk melarikan diri.',
                'status'   => 'completed',
                'cover'    => 'https://ia601909.us.archive.org/view_archive.php?archive=/31/items/l_covers_0013/l_covers_0013_23.zip&file=0013238718-L.jpg',
                'genres'   => ['Mystery', 'Horror', 'Drama'],
            ],
            [
                'title'    => 'Spy x Family',
                'author'   => 'Tatsuya Endo',
                'synopsis' => 'Seorang mata-mata ulung harus membangun keluarga palsu untuk menyelesaikan misinya, tanpa menyadari bahwa istri dan anaknya pun menyimpan rahasia besar.',
                'status'   => 'ongoing',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781974715466-L.jpg',
                'genres'   => ['Action', 'Comedy', 'Slice of Life'],
            ],
            [
                'title'    => 'Haikyu!!',
                'author'   => 'Haruichi Furudate',
                'synopsis' => 'Shoyo Hinata, remaja bertubuh pendek bercita-cita menjadi pemain voli terhebat, membuktikan bahwa ukuran bukan segalanya di atas lapangan.',
                'status'   => 'completed',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781421587660-L.jpg',
                'genres'   => ['Comedy', 'Drama', 'Slice of Life'],
            ],
            [
                'title'    => 'One-Punch Man',
                'author'   => 'ONE',
                'synopsis' => 'Saitama menjadi pahlawan sebagai hobi dan kini bisa mengalahkan semua musuh hanya dengan satu pukulan, membuatnya merasa bosan dengan pertarungan.',
                'status'   => 'ongoing',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781421585741-L.jpg',
                'genres'   => ['Action', 'Comedy', 'Sci-Fi'],
            ],
            [
                'title'    => 'Vinland Saga',
                'author'   => 'Makoto Yukimura',
                'synopsis' => 'Thorfinn, putra seorang pejuang Viking legendaris, mengejar dendam atas kematian ayahnya di tengah peperangan brutal era Viking.',
                'status'   => 'ongoing',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781612624204-L.jpg',
                'genres'   => ['Action', 'Drama', 'Fantasy'],
            ],
            [
                'title'    => 'Berserk',
                'author'   => 'Kentaro Miura',
                'synopsis' => 'Guts, seorang pejuang yang terlahir di medan perang, mengarungi dunia gelap penuh iblis sambil mencari balas dendam kepada temannya yang berkhianat.',
                'status'   => 'ongoing',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781593070205-L.jpg',
                'genres'   => ['Action', 'Fantasy', 'Horror'],
            ],
            [
                'title'    => 'Vagabond',
                'author'   => 'Takehiko Inoue',
                'synopsis' => 'Kisah fiksi tentang kehidupan Miyamoto Musashi, pendekar pedang legendaris Jepang, dalam perjalanannya menjadi yang tak tertandingi di dunia.',
                'status'   => 'ongoing',
                'cover'    => 'https://covers.openlibrary.org/b/isbn/9781591160496-L.jpg',
                'genres'   => ['Action', 'Drama'],
            ],
        ];

        foreach ($comics as $comicData) {
            $comicId = Str::uuid();

            DB::table('comics')->insert([
                'id'         => $comicId,
                'title'      => $comicData['title'],
                'author'     => $comicData['author'],
                'synopsis'   => $comicData['synopsis'],
                'status'     => $comicData['status'],
                'cover_url'  => $comicData['cover'],
                'banner_url' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($comicData['genres'] as $genreName) {
                DB::table('comic_genre')->insert([
                    'comic_id' => $comicId,
                    'genre_id' => $genreIds[$genreName],
                ]);
            }
        }

        // ---------------------------------------------------
        // 4. SEEDER CHAPTERS (1 chapter untuk One Piece)
        // ---------------------------------------------------
        // Ambil comic_id One Piece dari DB
        $onePieceId = DB::table('comics')->where('title', 'One Piece')->value('id');
        $chapterId  = Str::uuid();

        DB::table('chapters')->insert([
            'id'             => $chapterId,
            'comic_id'       => $onePieceId,
            'chapter_number' => 1.00,
            'title'          => 'Romance Dawn',
            'created_by'     => $adminId,
            'updated_by'     => $adminId,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        // ---------------------------------------------------
        // 5. SEEDER CHAPTER PAGES
        // ---------------------------------------------------
        DB::table('chapter_pages')->insert([
            [
                'id'           => Str::uuid(),
                'chapter_id'   => $chapterId,
                'page_number'  => 1,
                'image_url'    => 'default-page-1.jpg',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => Str::uuid(),
                'chapter_id'   => $chapterId,
                'page_number'  => 2,
                'image_url'    => 'default-page-2.jpg',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ]);

        // ---------------------------------------------------
        // 6. SEEDER INTERAKSI MEMBER
        // ---------------------------------------------------
        DB::table('favorites')->insert([
            'id'         => Str::uuid(),
            'user_id'    => $memberId,
            'comic_id'   => $onePieceId,
            'created_at' => $now,
        ]);

        DB::table('comments')->insert([
            'id'         => Str::uuid(),
            'user_id'    => $memberId,
            'chapter_id' => $chapterId,
            'content'    => 'Wah gila sih ini seru banget komiknya bang!',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('reading_histories')->insert([
            'id'           => Str::uuid(),
            'user_id'      => $memberId,
            'comic_id'     => $onePieceId,
            'chapter_id'   => $chapterId,
            'last_read_at' => $now,
            'created_at'   => $now,
        ]);
    }
}