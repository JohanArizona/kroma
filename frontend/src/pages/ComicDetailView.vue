<template>
  <div class="min-h-screen bg-gray-50 font-sans">
    <Navbar />

    <!-- Loading state -->
    <div v-if="isLoading" class="max-w-4xl mx-auto px-6 py-12 animate-pulse">
      <div class="h-56 bg-gray-200 rounded-2xl mb-6"></div>
      <div class="h-8 bg-gray-200 rounded w-1/2 mb-3"></div>
      <div class="h-4 bg-gray-200 rounded w-1/3 mb-6"></div>
      <div class="space-y-3">
        <div v-for="n in 5" :key="n" class="h-14 bg-gray-200 rounded-xl"></div>
      </div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="max-w-4xl mx-auto px-6 py-12 text-center">
      <p class="text-red-500 text-sm mb-4">{{ error }}</p>
      <button @click="router.back()" class="text-[#7C3AED] text-sm font-medium hover:underline">← Kembali</button>
    </div>

    <!-- Content -->
    <div v-else-if="comic">

      <!-- Banner / Hero Section -->
      <div class="relative h-56 md:h-72 overflow-hidden bg-gradient-to-br from-[#7C3AED] to-[#4F46E5]">
        <!-- Banner image kalau ada -->
        <img
          v-if="comic.banner_url"
          :src="getMediaUrl(comic.banner_url)"
          class="w-full h-full object-cover"
          alt="banner"
        />
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Back button -->
        <button
          @click="router.back()"
          class="absolute top-4 left-4 z-10 flex items-center gap-1.5 text-white/80 hover:text-white text-sm font-medium transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          Kembali
        </button>
      </div>

      <div class="max-w-4xl mx-auto px-6">

        <!-- Cover + Info Card -->
        <div class="flex gap-5 -mt-16 relative z-10 mb-6">
          <!-- Cover -->
          <div class="w-28 h-40 md:w-36 md:h-52 rounded-xl overflow-hidden border-4 border-white shadow-xl shrink-0 bg-gray-200">
            <img
              v-if="comic.cover_url"
              :src="getMediaUrl(comic.cover_url)"
              :alt="comic.title"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Info -->
          <div class="flex-1 pt-20 md:pt-24">
            <div class="flex flex-wrap gap-1.5 mb-2">
              <span
                v-for="g in comic.genres"
                :key="g.id"
                class="text-xs px-2 py-0.5 rounded-full bg-[#7C3AED]/10 text-[#7C3AED] font-medium"
              >{{ g.name }}</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-0.5">{{ comic.title }}</h1>
            <p class="text-sm text-gray-500 mb-2">{{ comic.author }}</p>
            <span :class="[
              'text-xs px-2.5 py-1 rounded-md font-semibold uppercase tracking-wide border',
              comic.status === 'ongoing'
                ? 'bg-[#7C3AED]/10 text-[#7C3AED] border-[#7C3AED]/20'
                : 'bg-green-50 text-green-700 border-green-200'
            ]">{{ comic.status }}</span>
          </div>
        </div>

        <!-- Favorite Button + Synopsis -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-gray-900">Sinopsis</h2>
            <button
              v-if="user"
              @click="toggleFavorite"
              :disabled="favLoading"
              class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold border transition-all"
              :class="isFavorite
                ? 'bg-red-50 text-red-500 border-red-200 hover:bg-red-100'
                : 'bg-[#7C3AED]/10 text-[#7C3AED] border-[#7C3AED]/20 hover:bg-[#7C3AED]/20'"
            >
              <svg class="w-4 h-4" :fill="isFavorite ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
              </svg>
              {{ favLoading ? '...' : (isFavorite ? 'Hapus Favorit' : 'Tambah Favorit') }}
            </button>
          </div>
          <p class="text-sm text-gray-600 leading-relaxed">{{ comic.synopsis || 'Tidak ada sinopsis.' }}</p>
        </div>

        <!-- Chapters List -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
          <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-900">
              Daftar Episode
              <span class="text-gray-400 font-normal text-sm ml-1">({{ chapters.length }} episode)</span>
            </h2>
          </div>

          <div v-if="isLoadingChapters" class="p-5 space-y-3">
            <div v-for="n in 4" :key="n" class="h-14 bg-gray-100 rounded-lg animate-pulse"></div>
          </div>

          <div v-else-if="chapters.length === 0" class="p-8 text-center text-gray-400 text-sm">
            Belum ada episode yang tersedia.
          </div>

          <div v-else class="divide-y divide-gray-50">
            <button
              v-for="ch in chapters"
              :key="ch.id"
              @click="readChapter(ch)"
              class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition-colors text-left"
            >
              <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-lg bg-[#7C3AED]/10 text-[#7C3AED] font-bold text-sm flex items-center justify-center shrink-0">
                  {{ parseFloat(ch.chapter_number) }}
                </span>
                <div>
                  <p class="text-sm font-semibold text-gray-900">
                    {{ ch.title || `Episode ${parseFloat(ch.chapter_number)}` }}
                  </p>
                  <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(ch.created_at) }}</p>
                </div>
              </div>
              <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import Navbar from '../components/layout/Navbar.vue'

const router = useRouter()
const route = useRoute()

const comicId = route.params.comicId
const token = localStorage.getItem('kroma_token')

const user = ref(null)
try {
  const raw = localStorage.getItem('kroma_user')
  if (raw) user.value = JSON.parse(raw)
} catch {}

const comic = ref(null)
const chapters = ref([])
const isLoading = ref(true)
const isLoadingChapters = ref(true)
const error = ref('')
const isFavorite = ref(false)
const favLoading = ref(false)

const getMediaUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `http://localhost:8000/storage/${path}`
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(dateString))
}

const authHeaders = () => ({
  'Accept': 'application/json',
  'Authorization': `Bearer ${token ?? ''}`
})

// Fetch comic detail
const fetchComic = async () => {
  try {
    const res = await fetch(`http://localhost:8000/api/v1/comics/${comicId}`, {
      headers: { 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Komik tidak ditemukan')
    comic.value = data.data
  } catch (e) {
    error.value = e.message
  } finally {
    isLoading.value = false
  }
}

// Fetch chapters
const fetchChapters = async () => {
  isLoadingChapters.value = true
  try {
    const res = await fetch(`http://localhost:8000/api/v1/comics/${comicId}/chapters`, {
      headers: authHeaders()
    })
    const data = await res.json()
    if (res.ok) {
      chapters.value = (data.data || []).sort((a, b) => a.chapter_number - b.chapter_number)
    }
  } catch (e) {
    console.error('Gagal memuat chapter:', e.message)
  } finally {
    isLoadingChapters.value = false
  }
}

// Cek apakah sudah favorit
const checkFavorite = async () => {
  if (!user.value) return
  try {
    const res = await fetch('http://localhost:8000/api/v1/library/favorites', {
      headers: authHeaders()
    })
    const data = await res.json()
    if (res.ok) {
      isFavorite.value = data.data.some(fav => fav.comic?.id === comicId)
    }
  } catch {}
}

// Toggle favorit
const toggleFavorite = async () => {
  if (!user.value) return
  favLoading.value = true
  try {
    if (isFavorite.value) {
      await fetch(`http://localhost:8000/api/v1/library/favorites/${comicId}`, {
        method: 'DELETE', headers: authHeaders()
      })
      isFavorite.value = false
    } else {
      await fetch('http://localhost:8000/api/v1/library/favorites', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json' },
        body: JSON.stringify({ comic_id: comicId })
      })
      isFavorite.value = true
    }
  } catch (e) {
    console.error('Toggle favorit gagal:', e.message)
  } finally {
    favLoading.value = false
  }
}

// Navigasi ke reader
const readChapter = (chapter) => {
  router.push({
    name: 'chapter.read',
    params: { comicId, chapterId: chapter.id },
    query: {
      chapterNumber: parseFloat(chapter.chapter_number),
      chapterTitle: chapter.title || ''
    }
  })
}

onMounted(() => {
  fetchComic()
  fetchChapters()
  checkFavorite()
})
</script>