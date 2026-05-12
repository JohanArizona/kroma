<template>
  <AdminLayout title="Dashboard">
    
    <div class="flex items-center justify-between mb-8">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Ringkasan Sistem</h2>
        <p class="text-gray-500 text-sm mt-1">Selamat datang kembali. Berikut adalah status platform Kroma saat ini.</p>
      </div>
    </div>

    <!-- Alert Error (Jika ada masalah saat ambil data) -->
    <div v-if="errorMessage" class="p-4 bg-red-50 text-red-600 rounded-lg border border-red-200 text-sm mb-6 flex items-center gap-2">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      {{ errorMessage }}
    </div>

    <!-- KARTU STATISTIK (Data dari /dashboard/stats) -->
    <div v-if="isLoadingStats" class="text-gray-500 text-sm mb-6 animate-pulse">Memuat statistik...</div>
    <div v-else class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
      
      <!-- Card 1: Total Users -->
      <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <span class="text-sm font-medium text-gray-500">Total Pengguna</span>
          <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
            <Users class="w-5 h-5" />
          </div>
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ stats.total_users }}</div>
      </div>

      <!-- Card 2: Total Comics -->
      <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <span class="text-sm font-medium text-gray-500">Master Komik</span>
          <div class="w-10 h-10 rounded-lg bg-[#7C3AED]/10 flex items-center justify-center text-[#7C3AED]">
            <BookOpen class="w-5 h-5" />
          </div>
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ stats.total_comics }}</div>
      </div>

      <!-- Card 3: Total Chapters -->
      <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <span class="text-sm font-medium text-gray-500">Total Episode</span>
          <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
            <FileStack class="w-5 h-5" />
          </div>
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ stats.total_chapters }}</div>
      </div>

    </div>

    <!-- TABEL KOMIK TERPOPULER (Data dari /discovery/popular) -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
      <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-bold text-gray-900">Komik Terpopuler</h3>
        <router-link to="/admin/comics" class="text-sm text-[#7C3AED] hover:underline font-medium">Lihat Semua Komik</router-link>
      </div>
      
      <div class="divide-y divide-gray-50">
        <div v-if="isLoadingPopular" class="p-8 text-sm text-gray-500 text-center animate-pulse">Memuat daftar komik...</div>
        <div v-else-if="popularComics.length === 0" class="p-8 text-sm text-gray-500 text-center">Belum ada komik yang ditambahkan.</div>
        
        <!-- List Komik -->
        <div v-else v-for="(comic, index) in popularComics" :key="comic.id" class="px-6 py-4 flex items-center gap-5 hover:bg-gray-50/50 transition">
          <span class="w-6 text-sm font-bold text-gray-400 text-center">{{ index + 1 }}</span>
          
          <div class="w-12 h-16 rounded-md overflow-hidden border border-gray-200 bg-gray-100 shrink-0">
            <ImageWithFallback :src="getCoverUrl(comic.cover_url)" :alt="comic.title" class="w-full h-full object-cover" />
          </div>
          
          <div class="flex-1 min-w-0">
            <div class="font-semibold text-gray-900 text-sm truncate mb-0.5">{{ comic.title }}</div>
            <div class="text-xs text-gray-500 truncate">{{ comic.author }} · <span class="capitalize">{{ comic.status }}</span></div>
          </div>
          
          <div class="text-right shrink-0">
            <div class="font-bold text-gray-900 text-sm flex items-center justify-end gap-1.5">
              <svg class="w-4 h-4 text-red-500 fill-red-500" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              {{ comic.favorites_count || 0 }}
            </div>
            <div class="text-[11px] text-gray-500 mt-0.5">Disimpan</div>
          </div>
        </div>

      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Users, BookOpen, FileStack } from 'lucide-vue-next'
import AdminLayout from '../../components/layout/AdminLayout.vue'
import ImageWithFallback from '../../components/ui/ImageWithFallback.vue'

const router = useRouter()

// States untuk API Dashboard Stats
const stats = ref({ total_users: 0, total_comics: 0, total_chapters: 0 })
const isLoadingStats = ref(true)

// States untuk API Popular Comics
const popularComics = ref([])
const isLoadingPopular = ref(true)

const errorMessage = ref('')

// Helper untuk render gambar komik dari Laravel Storage
const getCoverUrl = (path) => {
  if (!path) return ''
  // Kalau path-nya sudah URL lengkap, return langsung
  if (path.startsWith('http')) return path
  // Kalau berupa path lokal, tambahkan base url
  return `http://localhost:8000/storage/${path}`
}

// Fetch 1: Mengambil Data Statistik Global
const fetchStats = async (token) => {
  try {
    const res = await fetch('http://localhost:8000/api/v1/dashboard/stats', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message)
    stats.value = data.data
  } catch (error) {
    errorMessage.value = error.message
    if (error.message.includes('Unauthenticated') || error.message.includes('Akses ditolak')) {
      localStorage.removeItem('kroma_token')
      router.push('/login')
    }
  } finally {
    isLoadingStats.value = false
  }
}

// Fetch 2: Mengambil Komik Terpopuler
const fetchPopularComics = async () => {
  try {
    // Endpoint popular bersifat public (tidak butuh Bearer token)
    const res = await fetch('http://localhost:8000/api/v1/discovery/popular', {
      headers: { 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (res.ok) {
      // Tampilkan maksimal 5 teratas di dashboard
      popularComics.value = data.data.slice(0, 5)
    }
  } catch (error) {
    console.error('Gagal memuat komik terpopuler', error)
  } finally {
    isLoadingPopular.value = false
  }
}

onMounted(() => {
  const token = localStorage.getItem('kroma_token')
  if (!token) {
    router.push('/login')
    return
  }
  
  // Panggil kedua API secara paralel
  fetchStats(token)
  fetchPopularComics()
})
</script>