<template>
  <div class="min-h-screen bg-gray-50 font-sans">
    <Navbar />

    <!-- Toast Notification -->
    <Transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="toastVisible"
        class="fixed top-20 right-6 z-50 flex items-center p-4 w-full max-w-sm bg-white rounded-xl shadow-xl border border-gray-100"
      >
        <div
          :class="[
            'inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg',
            toastType === 'success' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100'
          ]"
        >
          <svg v-if="toastType === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="ml-3 text-sm font-semibold text-gray-700">{{ toastMessage }}</div>
        <button
          @click="toastVisible = false"
          class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 items-center justify-center transition-colors"
        >
          <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
          </svg>
        </button>
      </div>
    </Transition>

    <main class="max-w-7xl mx-auto px-6 py-8">

      <!-- Hero Banner -->
      <section class="mb-10 rounded-2xl overflow-hidden bg-gradient-to-br from-[#7C3AED] to-[#4F46E5] relative">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
          <div class="absolute top-4 right-20 w-40 h-56 rounded-xl bg-white rotate-6"></div>
          <div class="absolute top-8 right-36 w-40 h-56 rounded-xl bg-white -rotate-3"></div>
          <div class="absolute -bottom-4 right-10 w-32 h-44 rounded-xl bg-white rotate-12"></div>
        </div>

        <div class="relative z-10 px-10 py-12 max-w-lg">
          <span class="inline-block text-xs font-semibold bg-white/20 text-white px-3 py-1 rounded-full mb-4 tracking-wide">
            ✦ Platform Komik Digital
          </span>
          <h1 class="text-3xl font-bold text-white leading-snug mb-3">
            Ribuan cerita.<br />Satu tempat nyaman.
          </h1>
          <p class="text-white/75 text-sm leading-relaxed mb-6">
            Temukan webtoon & komik terbaik pilihan editor. Dari action, romance, hingga fantasy — semua ada di sini.
          </p>

          <!-- Belum login -->
          <div v-if="!user" class="flex gap-3">
            <router-link to="/register"
              class="px-5 py-2.5 bg-white text-[#7C3AED] text-sm font-semibold rounded-lg hover:bg-gray-100 transition">
              Mulai Membaca
            </router-link>
          </div>

          <!-- Sudah login -->
          <div v-else class="flex gap-3">
            <button
              @click="scrollToGenre"
              class="px-5 py-2.5 bg-white text-[#7C3AED] text-sm font-semibold rounded-lg hover:bg-gray-100 transition">
              Jelajahi Sekarang
            </button>
            <button
              @click="logout"
              class="px-5 py-2.5 bg-white/15 text-white text-sm font-semibold rounded-lg hover:bg-white/25 transition border border-white/20">
              Keluar
            </button>
          </div>
        </div>
      </section>

      <!-- Popular Right Now -->
      <section class="mb-10">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            Popular Right Now
          </h2>
        </div>

        <div v-if="isLoadingPopular" class="flex gap-4 overflow-hidden">
          <div v-for="n in 6" :key="n" class="shrink-0 w-44 animate-pulse">
            <div class="aspect-[2/3] rounded-md bg-gray-200 mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-1"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
          </div>
        </div>

        <div v-else-if="errorPopular" class="p-6 text-sm text-red-500 bg-red-50 rounded-xl border border-red-100">
          {{ errorPopular }}
        </div>

        <div v-else class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
          <div
            v-for="(comic, index) in popularComics"
            :key="comic.id"
            class="comic-wrapper shrink-0 relative"
          >
            <ComicCard
              :comic="mapComic(comic)"
              :rank="index + 1"
              @click="handleComicClick(comic)"
            />

            <!--
              Heart button: opacity-0 by default, muncul saat hover via CSS.
              Jika sudah difavoritkan → paksa opacity-100 + bg merah.
            -->
            <button
              v-if="user"
              @click.stop="toggleFavorite(comic)"
              :disabled="loadingFavorites.has(comic.id)"
              :title="favoriteIds.has(comic.id) ? 'Hapus dari favorit' : 'Tambah ke favorit'"
              class="heart-btn absolute top-2 right-2 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-black/50 backdrop-blur-sm transition-all duration-200"
              :class="{ 'opacity-100 !bg-red-500': favoriteIds.has(comic.id) }"
            >
              <svg v-if="loadingFavorites.has(comic.id)" class="w-4 h-4 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
              </svg>
              <svg v-else-if="favoriteIds.has(comic.id)" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
              </svg>
              <svg v-else class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
              </svg>
            </button>
          </div>
        </div>
      </section>

      <!-- Genre Filter — id dipakai oleh scrollToGenre() -->
      <section id="genre-section">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            Jelajahi Genre
          </h2>
        </div>

        <div v-if="isLoadingGenres" class="flex gap-2 mb-6 flex-wrap">
          <div v-for="n in 7" :key="n" class="h-8 w-20 rounded-full bg-gray-200 animate-pulse"></div>
        </div>

        <div v-else class="flex gap-2 mb-6 flex-wrap">
          <button @click="selectGenre(null)" :class="[
            'px-4 py-1.5 rounded-full text-sm font-medium transition-colors border',
            selectedGenre === null
              ? 'bg-[#7C3AED] text-white border-[#7C3AED]'
              : 'bg-white text-gray-600 border-gray-200 hover:border-[#7C3AED] hover:text-[#7C3AED]'
          ]">
            All
          </button>
          <button
            v-for="genre in genres"
            :key="genre.id"
            @click="selectGenre(genre.name)"
            :class="[
              'px-4 py-1.5 rounded-full text-sm font-medium transition-colors border',
              selectedGenre === genre.name
                ? 'bg-[#7C3AED] text-white border-[#7C3AED]'
                : 'bg-white text-gray-600 border-gray-200 hover:border-[#7C3AED] hover:text-[#7C3AED]'
            ]"
          >
            {{ genre.name }}
          </button>
        </div>

        <!-- Skeleton: loading genre spesifik atau fresh load tab "All" -->
        <div
          v-if="isLoadingGenreComics || (selectedGenre === null && isLoadingAll && allComics.length === 0)"
          class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
        >
          <div v-for="n in 6" :key="n" class="animate-pulse">
            <div class="aspect-[2/3] rounded-md bg-gray-200 mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-1"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
          </div>
        </div>

        <div
          v-else-if="selectedGenre !== null && !isLoadingGenreComics && genreComics.length === 0"
          class="p-10 text-center text-gray-400 text-sm bg-white rounded-xl border border-gray-100"
        >
          Belum ada komik untuk genre
          <span class="font-semibold text-gray-600">{{ selectedGenre }}</span>.
        </div>

        <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <div
            v-for="comic in selectedGenre === null ? allComics : genreComics"
            :key="comic.id"
            class="comic-wrapper relative"
          >
            <ComicCard
              :comic="mapComic(comic)"
              @click="handleComicClick(comic)"
            />

            <button
              v-if="user"
              @click.stop="toggleFavorite(comic)"
              :disabled="loadingFavorites.has(comic.id)"
              :title="favoriteIds.has(comic.id) ? 'Hapus dari favorit' : 'Tambah ke favorit'"
              class="heart-btn absolute top-2 right-2 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-black/50 backdrop-blur-sm transition-all duration-200"
              :class="{ 'opacity-100 !bg-red-500': favoriteIds.has(comic.id) }"
            >
              <svg v-if="loadingFavorites.has(comic.id)" class="w-4 h-4 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
              </svg>
              <svg v-else-if="favoriteIds.has(comic.id)" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
              </svg>
              <svg v-else class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Load More: hanya di tab "All", hanya jika masih ada halaman -->
        <div v-if="selectedGenre === null && currentPage < lastPage" class="mt-6 text-center">
          <button
            @click="loadMore"
            :disabled="isLoadingAll"
            class="px-6 py-2.5 bg-[#7C3AED] text-white text-sm font-semibold rounded-lg hover:bg-[#6D28D9] disabled:opacity-50 transition"
          >
            {{ isLoadingAll ? 'Memuat...' : 'Load More' }}
          </button>
        </div>
      </section>

    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Navbar from '../components/layout/Navbar.vue'
import ComicCard from '../components/ui/ComicCard.vue'

const router = useRouter()

// User dibaca dari localStorage — null berarti guest
const user = ref(null)
try {
  const raw = localStorage.getItem('kroma_user')
  if (raw) user.value = JSON.parse(raw)
} catch { }

// Token disimpan terpisah di 'kroma_token', bukan di dalam user object
const getAuthHeaders = () => ({
  'Accept': 'application/json',
  'Content-Type': 'application/json',
  'Authorization': `Bearer ${localStorage.getItem('kroma_token') ?? ''}`
})

// Toast state
const toastVisible = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
let toastTimer = null

const triggerToast = (message, type = 'success') => {
  if (toastTimer) clearTimeout(toastTimer)
  toastMessage.value = message
  toastType.value = type
  toastVisible.value = true
  toastTimer = setTimeout(() => { toastVisible.value = false }, 3000)
}

// Popular comics state
const popularComics = ref([])
const isLoadingPopular = ref(true)
const errorPopular = ref('')

// Genres state
const genres = ref([])
const isLoadingGenres = ref(true)

// Genre filter state
const selectedGenre = ref(null)
const genreComics = ref([])
const isLoadingGenreComics = ref(false)

// All comics (paginated)
const allComics = ref([])
const isLoadingAll = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)

// favoriteIds: Set untuk lookup O(1) di template
// loadingFavorites: Set comic_id yang sedang proses add/remove
// Keduanya di-reassign (bukan .add/.delete) agar Vue reaktif
const favoriteIds = ref(new Set())
const loadingFavorites = ref(new Set())

const getCoverUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `http://localhost:8000/storage/${path}`
}

const mapComic = (comic) => ({
  ...comic,
  cover: getCoverUrl(comic.cover_url),
})

const scrollToGenre = () => {
  document.getElementById('genre-section')?.scrollIntoView({ behavior: 'smooth' })
}

const logout = () => {
  localStorage.removeItem('kroma_token')
  localStorage.removeItem('kroma_user')
  router.push('/login')
}

const fetchPopular = async () => {
  try {
    const res = await fetch('http://localhost:8000/api/v1/discovery/popular', {
      headers: { 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Gagal memuat komik populer.')
    popularComics.value = data.data
  } catch (error) {
    errorPopular.value = error.message
  } finally {
    isLoadingPopular.value = false
  }
}

const fetchGenres = async () => {
  try {
    const res = await fetch('http://localhost:8000/api/v1/discovery/genres', {
      headers: { 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message)
    genres.value = data.data
  } catch (error) {
    console.error('Gagal memuat genres:', error.message)
  } finally {
    isLoadingGenres.value = false
  }
}

// page=1 → replace allComics (fresh/reset), page>1 → append (load more)
const fetchAllComics = async (page = 1) => {
  isLoadingAll.value = true
  try {
    const res = await fetch(
      `http://localhost:8000/api/v1/discovery/all?page=${page}`,
      { headers: { 'Accept': 'application/json' } }
    )
    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Gagal memuat komik.')

    if (page === 1) {
      allComics.value = data.data
    } else {
      allComics.value.push(...data.data)
    }

    lastPage.value = data.meta.last_page
  } catch (error) {
    console.error('Gagal memuat semua komik:', error.message)
  } finally {
    isLoadingAll.value = false
  }
}

const fetchByGenre = async (genreName) => {
  isLoadingGenreComics.value = true
  genreComics.value = []
  try {
    const res = await fetch(
      `http://localhost:8000/api/v1/discovery/genres/${encodeURIComponent(genreName)}`,
      { headers: { 'Accept': 'application/json' } }
    )
    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Gagal memuat komik.')
    genreComics.value = data.data
  } catch (error) {
    console.error('Gagal memuat komik genre:', error.message)
  } finally {
    isLoadingGenreComics.value = false
  }
}

const fetchFavorites = async () => {
  if (!user.value) return
  try {
    const res = await fetch('http://localhost:8000/api/v1/library/favorites', {
      headers: getAuthHeaders()
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message)
    favoriteIds.value = new Set(data.data.map(fav => fav.comic.id))
  } catch (error) {
    console.error('Gagal memuat favorit:', error.message)
  }
}

const toggleFavorite = async (comic) => {
  if (!user.value) return

  const comicId = comic.id
  const isAlreadyFav = favoriteIds.value.has(comicId)

  // Tandai sedang loading
  loadingFavorites.value = new Set([...loadingFavorites.value, comicId])

  // Optimistic update: ubah UI dulu, rollback jika API gagal
  const optimisticSet = new Set(favoriteIds.value)
  if (isAlreadyFav) {
    optimisticSet.delete(comicId)
  } else {
    optimisticSet.add(comicId)
  }
  favoriteIds.value = optimisticSet

  try {
    let res
    if (isAlreadyFav) {
      res = await fetch(
        `http://localhost:8000/api/v1/library/favorites/${comicId}`,
        { method: 'DELETE', headers: getAuthHeaders() }
      )
    } else {
      res = await fetch('http://localhost:8000/api/v1/library/favorites', {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ comic_id: comicId })
      })
    }

    const data = await res.json()

    // 409 = sudah ada di favorit (race condition), anggap sukses
    if (!res.ok && res.status !== 409) {
      throw new Error(data.message || 'Gagal mengubah favorit.')
    }

    triggerToast(
      isAlreadyFav
        ? `"${comic.title}" dihapus dari favorit.`
        : `"${comic.title}" ditambahkan ke favorit!`,
      'success'
    )
  } catch (error) {
    // Rollback jika gagal
    const rollbackSet = new Set(favoriteIds.value)
    if (isAlreadyFav) {
      rollbackSet.add(comicId)
    } else {
      rollbackSet.delete(comicId)
    }
    favoriteIds.value = rollbackSet

    triggerToast(error.message || 'Gagal mengubah favorit.', 'error')
    console.error('Toggle favorit gagal:', error.message)
  } finally {
    const remainingLoading = new Set(loadingFavorites.value)
    remainingLoading.delete(comicId)
    loadingFavorites.value = remainingLoading
  }
}

const selectGenre = (genreName) => {
  selectedGenre.value = genreName
  currentPage.value = 1

  if (genreName === null) {
    fetchAllComics(1)
    return
  }
  fetchByGenre(genreName)
}

const loadMore = () => {
  currentPage.value++
  fetchAllComics(currentPage.value)
}

const handleComicClick = (comic) => {
  router.push(`/comics/${comic.id}`)
}

onMounted(() => {
  fetchPopular()
  fetchGenres()
  fetchAllComics(1)
  fetchFavorites()
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Heart button tersembunyi by default, muncul saat parent di-hover.
   Jika sudah favorit, template menambahkan opacity-100 via class binding. */
.heart-btn {
  opacity: 0;
}

.comic-wrapper:hover .heart-btn {
  opacity: 1;
}
</style>