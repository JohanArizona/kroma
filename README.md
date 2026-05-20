
```
kroma
├─ backend
│  ├─ .editorconfig
│  ├─ app
│  │  ├─ Http
│  │  │  ├─ Controllers
│  │  │  │  ├─ AuthController.php
│  │  │  │  ├─ ChapterController.php
│  │  │  │  ├─ ChapterPageController.php
│  │  │  │  ├─ ComicController.php
│  │  │  │  ├─ CommentController.php
│  │  │  │  ├─ Controller.php
│  │  │  │  ├─ DashboardController.php
│  │  │  │  ├─ DiscoveryController.php
│  │  │  │  ├─ LibraryController.php
│  │  │  │  ├─ ProfileController.php
│  │  │  │  └─ SearchController.php
│  │  │  └─ Middleware
│  │  │     └─ IsAdmin.php
│  │  ├─ Models
│  │  │  ├─ Chapter.php
│  │  │  ├─ ChapterPage.php
│  │  │  ├─ Comic.php
│  │  │  ├─ Comment.php
│  │  │  ├─ Favorite.php
│  │  │  ├─ Genre.php
│  │  │  ├─ ReadingHistory.php
│  │  │  └─ User.php
│  │  └─ Providers
│  │     └─ AppServiceProvider.php
│  ├─ artisan
│  ├─ bootstrap
│  │  ├─ app.php
│  │  ├─ cache
│  │  │  ├─ packages.php
│  │  │  └─ services.php
│  │  └─ providers.php
│  ├─ composer.json
│  ├─ composer.lock
│  ├─ config
│  │  ├─ app.php
│  │  ├─ auth.php
│  │  ├─ cache.php
│  │  ├─ cors.php
│  │  ├─ database.php
│  │  ├─ filesystems.php
│  │  ├─ jwt.php
│  │  ├─ logging.php
│  │  ├─ mail.php
│  │  ├─ queue.php
│  │  ├─ sanctum.php
│  │  ├─ services.php
│  │  └─ session.php
│  ├─ database
│  │  ├─ database.sqlite
│  │  ├─ factories
│  │  │  └─ UserFactory.php
│  │  ├─ migrations
│  │  │  ├─ 0001_01_01_000000_create_users_table.php
│  │  │  ├─ 0001_01_01_000001_create_cache_table.php
│  │  │  ├─ 0001_01_01_000002_create_jobs_table.php
│  │  │  ├─ 2026_04_30_045905_create_personal_access_tokens_table.php
│  │  │  ├─ 2026_04_30_065134_create_comics_table.php
│  │  │  ├─ 2026_04_30_065134_create_genres_table.php
│  │  │  ├─ 2026_04_30_065240_create_comic_genre_table.php
│  │  │  ├─ 2026_04_30_141745_create_chapters_table.php
│  │  │  ├─ 2026_04_30_142001_create_chapter_pages_table.php
│  │  │  ├─ 2026_04_30_142210_create_favorites_table.php
│  │  │  ├─ 2026_04_30_142241_create_reading_histories_table.php
│  │  │  └─ 2026_04_30_142301_create_comments_table.php
│  │  └─ seeders
│  │     └─ DatabaseSeeder.php
│  ├─ package.json
│  ├─ phpunit.xml
│  ├─ public
│  │  ├─ .htaccess
│  │  ├─ favicon.ico
│  │  ├─ index.php
│  │  ├─ robots.txt
│  │  └─ storage
│  │     ├─ avatars
│  │     └─ comics
│  │        ├─ banners
│  │        │  ├─ 1JtJsibcmJsDEn5X3oFF1WqzrehMo82G0OpfKeYD.png
│  │        │  ├─ rD9Iyg3JdAKaWS0e5uxKqy1ODIT2OGp97POTyKyG.png
│  │        │  └─ Yi9fNcAE2AbUfM3OBO8KDkDftc7FdmNtIuDmHXok.png
│  │        └─ covers
│  │           ├─ cpwVD1AGulx8kRP8S193iYPgqFIh7sl5VhzwkmXD.png
│  │           ├─ iTKkOKaTYNBmMP0mJdlaKoASGd4IPMwgasE1n9om.png
│  │           └─ oq0SGDUVmFDm0wypLxMEIsh7f5Jq1HyUAEIfhD3u.png
│  ├─ README.md
│  ├─ resources
│  │  ├─ css
│  │  │  └─ app.css
│  │  ├─ js
│  │  │  ├─ app.js
│  │  │  └─ bootstrap.js
│  │  └─ views
│  │     └─ welcome.blade.php
│  ├─ routes
│  │  ├─ api.php
│  │  ├─ console.php
│  │  └─ web.php
│  ├─ storage
│  │  ├─ app
│  │  │  ├─ private
│  │  │  └─ public
│  │  │     ├─ avatars
│  │  │     └─ comics
│  │  │        ├─ banners
│  │  │        │  ├─ 1JtJsibcmJsDEn5X3oFF1WqzrehMo82G0OpfKeYD.png
│  │  │        │  ├─ rD9Iyg3JdAKaWS0e5uxKqy1ODIT2OGp97POTyKyG.png
│  │  │        │  └─ Yi9fNcAE2AbUfM3OBO8KDkDftc7FdmNtIuDmHXok.png
│  │  │        └─ covers
│  │  │           ├─ cpwVD1AGulx8kRP8S193iYPgqFIh7sl5VhzwkmXD.png
│  │  │           ├─ iTKkOKaTYNBmMP0mJdlaKoASGd4IPMwgasE1n9om.png
│  │  │           └─ oq0SGDUVmFDm0wypLxMEIsh7f5Jq1HyUAEIfhD3u.png
│  │  ├─ framework
│  │  │  ├─ cache
│  │  │  │  └─ data
│  │  │  ├─ sessions
│  │  │  ├─ testing
│  │  │  └─ views
│  │  │     ├─ 0198b7bc5123667f8c7d075a5f653e0e.php
│  │  │     ├─ 05e111479b34b68624bfee0be68bf620.php
│  │  │     ├─ 104e7be732f02d10837a5e266dc2b91c.php
│  │  │     ├─ 10fd26ed52f9ab07ffcb360b77859ba1.php
│  │  │     ├─ 2528ac3f6c65137a035a4b4ff72c200b.php
│  │  │     ├─ 260fdb11cf976c17592775b043b291fd.php
│  │  │     ├─ 26f8fc1bbc98903465d301ed03e50e79.php
│  │  │     ├─ 2a055b61afe314954f490039436244c0.php
│  │  │     ├─ 3a6c19156989d83d1b322cc4efb70781.php
│  │  │     ├─ 3dafc466c5b496fd36f0a144b35cf852.php
│  │  │     ├─ 5d2bccff0c9ca0434234b8240bb8702f.php
│  │  │     ├─ 5d857ecce891fe0d2fc62049a8155e5f.php
│  │  │     ├─ 5e113d6368995a7476cf37f7f550eef4.php
│  │  │     ├─ 5fe95e29dfa97b42f9235781967539f4.php
│  │  │     ├─ 601981a2bd4e8b430c3357e15090cb2e.php
│  │  │     ├─ 62eee9956db60e2ff2967803aca1536d.php
│  │  │     ├─ 6480c8f44cffa5b8f711578c6cee693e.php
│  │  │     ├─ 767bcf46d15cfbafb9eac53f2384d0eb.php
│  │  │     ├─ 7710130519b6eb6a569eed2c4a0a0c42.php
│  │  │     ├─ 776f15036b406664bdc4973a733854fd.php
│  │  │     ├─ 88f1daac91ccad6dbccd269b43f9e0e0.php
│  │  │     ├─ 8c8207e73bf418c402d754f149c2359c.php
│  │  │     ├─ 8e63bc20e173a3e183f05c375c7802bc.php
│  │  │     ├─ 8f9164a5f20661fceb696b392c94adb9.php
│  │  │     ├─ 91260c8668c3ca6f984b940b25fe706e.php
│  │  │     ├─ 93dea78e695ecaafb86789b4962ff6e8.php
│  │  │     ├─ 9bbaa04c2b38350b25009205a8227427.php
│  │  │     ├─ a87066c440b98a1368e482220e143d68.php
│  │  │     ├─ aa171f05077dc7740212a5fca2ffd0bc.php
│  │  │     ├─ ae0912971f32d387fa31ddc5681ff616.php
│  │  │     ├─ b1f83efad8f25d988a25e0e4b6c1b5b8.php
│  │  │     ├─ b35d0c9a4d40fbcb9e8c5003e1d55e00.php
│  │  │     ├─ d438914b50fa35f752f7f7a8a11a679d.php
│  │  │     ├─ db2901ab5f6af112d165204b293d942a.php
│  │  │     ├─ e242aa4e929ada28f474396518accb6f.php
│  │  │     ├─ e351c8c92d1de4e2bd2cf39c50d4e77a.php
│  │  │     ├─ e3dcf7185dbf19a1338d6f35319e9ad7.php
│  │  │     ├─ e7c5793ab16a6a4c7dbe1eb9772cc998.php
│  │  │     ├─ e7caaa6dfbc37837220686c90e891931.php
│  │  │     ├─ f15ca09a1c8db9e68fb54afdfd4b1eb7.php
│  │  │     ├─ f8886a37465c1c2e82031fb92e85f36e.php
│  │  │     └─ f9bf390423e827d18af76bb0d3f1e4bc.php
│  │  └─ logs
│  │     └─ laravel.log
│  ├─ tests
│  │  ├─ Feature
│  │  │  └─ ExampleTest.php
│  │  ├─ TestCase.php
│  │  └─ Unit
│  │     └─ ExampleTest.php
│  └─ vite.config.js
└─ frontend
   ├─ env.d.ts
   ├─ index.html
   ├─ package-lock.json
   ├─ package.json
   ├─ postcss.config.js
   ├─ public
   │  ├─ favicon.ico
   │  └─ logokroma.png
   ├─ README.md
   ├─ src
   │  ├─ App.vue
   │  ├─ assets
   │  │  └─ main.css
   │  ├─ components
   │  │  ├─ home
   │  │  ├─ layout
   │  │  └─ ui
   │  ├─ composables
   │  ├─ main.ts
   │  ├─ pages
   │  │  ├─ HomeView.vue
   │  │  ├─ LoginView.vue
   │  │  └─ RegisterView.vue
   │  └─ router
   │     └─ index.ts
   ├─ tailwind.config.ts
   ├─ tsconfig.app.json
   ├─ tsconfig.json
   ├─ tsconfig.node.json
   └─ vite.config.ts

```