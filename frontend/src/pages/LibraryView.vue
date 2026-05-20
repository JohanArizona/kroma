<template>
  <div class="min-h-screen bg-gray-50 font-sans">
    <Navbar />

    <main class="max-w-7xl mx-auto px-6 py-8">

      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Library</h1>
        <p class="text-sm text-gray-500 mt-1">{{ totalFavorites }} comics in your collection</p>
      </div>

      <!-- Tabs -->
      <div class="flex gap-1 mb-6 border-b border-gray-200">
        <button
          class="px-4 py-2.5 text-sm font-semibold text-[#7C3AED] border-b-2 border-[#7C3AED] -mb-px transition-colors"
        >
          Favorites
        </button>
      </div>

      <!-- Loading State -->
      <div
        v-if="isLoading"
        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
      >
        <div v-for="n in 6" :key="n" class="animate-pulse">
          <div class="aspect-[2/3] rounded-md bg-gray-200 mb-2"></div>
          <div class="h-4 bg-gray-200 rounded w-3/4 mb-1"></div>
          <div class="h-3 bg-gray-200 rounded w-1/2"></div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="favorites.length === 0"
        class="flex flex-col items-center justify-center py-24 text-center"
      >
        <div class="w-16 h-16 rounded-full bg-[#7C3AED] flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="white" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
          </svg>
        </div>
        <h3 class="text-base font-semibold text-gray-700 mb-1">Belum ada favorit</h3>
        <p class="text-sm text-gray-400 mb-6">Tambahkan komik ke favorit dari halaman utama.</p>
        <router-link
          to="/"
          class="px-5 py-2.5 bg-[#7C3AED] text-white text-sm font-semibold rounded-lg hover:bg-[#6D28D9] transition"
        >
          Jelajahi Komik
        </router-link>
      </div>

      <!-- Error State -->
      <div
        v-else-if="error"
        class="p-6 text-sm text-red-500 bg-red-50 rounded-xl border border-red-100"
      >
        {{ error }}
      </div>

      <!-- Comics Grid -->
      <div
        v-else
        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
      >
        <div
          v-for="item in favorites"
          :key="item.favorite_id"
          class="relative group"
        >
          <div class="relative aspect-[2/3] rounded-md overflow-hidden border border-gray-200 mb-2 bg-gray-100">
            <img
              :src="getCoverUrl(item.comic?.cover_url)"
              :alt="item.comic?.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              @error="handleImgError"
            />

            <!-- Overlay hapus: muncul saat hover -->
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
              <button
                @click="confirmRemove(item)"
                class="px-3 py-1.5 bg-white text-red-600 text-xs font-semibold rounded-lg hover:bg-red-50 transition-colors shadow"
              >
                Hapus
              </button>
            </div>
          </div>

          <h3 class="font-semibold text-gray-900 text-sm truncate">{{ item.comic?.title }}</h3>
          <p class="text-xs text-gray-400 truncate">
            Ditambahkan {{ formatDate(item.added_at) }}
          </p>
        </div>
      </div>

    </main>

    <!-- Toast Notification -->
    <AlertToast
      :show="alert.show"
      :type="alert.type"
      :message="alert.message"
      @close="alert.show = false"
    />

    <!-- Confirm Modal: muncul saat confirmRemove() dipanggil -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Hapus dari Favorit?"
      :message="`Hapus '${selectedItem?.comic?.title}' dari daftar favorit kamu?`"
      confirmText="Hapus"
      cancelText="Batal"
      @confirm="removeFavorite"
      @cancel="showDeleteModal = false"
    />

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Navbar from '../components/layout/Navbar.vue'
import AlertToast from '../components/ui/AlertToast.vue'
import ConfirmModal from '../components/ui/ConfirmModal.vue'

const router = useRouter()

const favorites = ref([])
const isLoading = ref(true)
const error = ref('')

const showDeleteModal = ref(false)
const selectedItem = ref(null)

const alert = ref({ show: false, type: 'success', message: '' })

// Reaktif otomatis setiap favorites[] berubah
const totalFavorites = computed(() => favorites.value.length)

const getToken = () => localStorage.getItem('kroma_token')

const getCoverUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `http://localhost:8000/storage/${path}`
}

const handleImgError = (e) => {
  e.target.src = ''
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const showAlert = (type, message) => {
  alert.value = { show: true, type, message }
  setTimeout(() => { alert.value.show = false }, 3000)
}

const fetchFavorites = async () => {
  const token = getToken()
  if (!token) {
    router.push('/login')
    return
  }

  try {
    const res = await fetch('http://localhost:8000/api/v1/library/favorites', {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    })

    const data = await res.json()

    if (!res.ok) {
      if (res.status === 401) {
        router.push('/login')
        return
      }
      throw new Error(data.message || 'Gagal memuat daftar favorit.')
    }

    favorites.value = data.data
  } catch (err) {
    error.value = err.message
  } finally {
    isLoading.value = false
  }
}

const confirmRemove = (item) => {
  selectedItem.value = item
  showDeleteModal.value = true
}

const removeFavorite = async () => {
  showDeleteModal.value = false
  const token = getToken()
  const comicId = selectedItem.value?.comic?.id

  if (!token || !comicId) return

  try {
    const res = await fetch(
      `http://localhost:8000/api/v1/library/favorites/${comicId}`,
      {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        }
      }
    )

    const data = await res.json()

    if (!res.ok) throw new Error(data.message || 'Gagal menghapus favorit.')

    // Filter lokal — tidak perlu refetch ke server
    favorites.value = favorites.value.filter(
      (f) => f.comic?.id !== comicId
    )

    showAlert('success', 'Komik dihapus dari favorit.')
  } catch (err) {
    showAlert('error', err.message)
  }
}

onMounted(() => {
  fetchFavorites()
})
</script>