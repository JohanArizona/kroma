<template>
  <header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">

      <!-- Kiri: Logo -->
      <router-link to="/" class="flex items-center">
        <img src="/logokroma.png" alt="Kroma" class="h-7" />
      </router-link>

      <!-- Kanan: Search & Aksi -->
      <div class="flex items-center gap-3">

        <!-- Search Bar -->
        <div class="relative hidden md:block">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari komik, penulis..."
            class="w-64 pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/40 focus:border-[#7C3AED] transition"
            @input="searchComics"
          />

          <!-- Hasil Pencarian -->
          <div
            v-if="searchResults.length"
            class="absolute top-[calc(100%+8px)] left-0 w-full bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden"
          >
            <div
              v-for="comic in searchResults"
              :key="comic.id"
              @click="bukaKomik(comic.id)"
              class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition"
            >
              <img :src="getCoverUrl(comic.cover_url)" class="w-10 h-14 object-cover rounded-md shrink-0" />
              <div class="min-w-0">
                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ comic.title }}</h3>
                <p class="text-xs text-gray-500 truncate">{{ comic.author }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sudah Login -->
        <template v-if="user">

          <!-- Pemisah -->
          <div class="w-px h-5 bg-gray-200"></div>

          <!-- Tombol Koleksi (Opsi A) -->
          <router-link
            to="/library"
            class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 hover:text-[#7C3AED] hover:border-[#7C3AED]/30 transition"
            active-class="text-[#7C3AED] border-[#7C3AED]/30 bg-[#7C3AED]/5"
          >
            <BookMarked class="w-4 h-4 text-[#7C3AED]" />
            Koleksi
          </router-link>

          <!-- Avatar Chip (Opsi B) -->
          <div class="relative" ref="profileRef">
            <button
              @click="isProfileOpen = !isProfileOpen"
              class="flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-full border border-gray-200 bg-white hover:bg-gray-50 transition"
            >
              <div class="w-7 h-7 rounded-full bg-[#7C3AED] flex items-center justify-center text-white text-xs font-bold shrink-0">
                {{ user.name.charAt(0).toUpperCase() }}
              </div>
              <span class="text-sm font-medium text-gray-700 max-w-[80px] truncate">{{ user.name }}</span>
              <ChevronDown
                class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                :class="{ 'rotate-180': isProfileOpen }"
              />
            </button>

            <!-- Dropdown -->
            <Transition
              enter-active-class="transition ease-out duration-150"
              enter-from-class="opacity-0 translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition ease-in duration-100"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 translate-y-1"
            >
              <div
                v-if="isProfileOpen"
                class="absolute right-0 top-[calc(100%+8px)] w-52 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50"
              >
                <!-- Info -->
                <div class="px-4 py-3 border-b border-gray-100">
                  <p class="text-sm font-semibold text-gray-900 truncate">{{ user.name }}</p>
                  <p class="text-xs text-gray-500 mt-0.5 truncate">{{ user.email || 'Pengguna Kroma' }}</p>
                </div>

                <!-- Menu -->
                <div class="p-1.5 space-y-0.5">
                  <router-link
                    to="/profile"
                    @click="isProfileOpen = false"
                    class="flex items-center gap-2.5 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition"
                  >
                    <UserCircle class="w-4 h-4 text-gray-400" />
                    Profil Saya
                  </router-link>

                  <button
                    @click="handleLogout"
                    class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition"
                  >
                    <LogOut class="w-4 h-4" />
                    Keluar
                  </button>
                </div>
              </div>
            </Transition>
          </div>

        </template>

        <!-- Belum Login -->
        <template v-else>
          <router-link
            to="/login"
            class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition"
          >
            Masuk
          </router-link>
          <router-link
            to="/register"
            class="px-4 py-2 rounded-lg bg-[#7C3AED] text-white text-sm font-medium hover:bg-[#6D28D9] transition"
          >
            Daftar
          </router-link>
        </template>

      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { Search, BookMarked, ChevronDown, UserCircle, LogOut } from 'lucide-vue-next'

const router = useRouter()
const user = ref(null)
const searchQuery = ref('')
const searchResults = ref([])
const isProfileOpen = ref(false)
const profileRef = ref(null)

const muatUser = () => {
  const userData = localStorage.getItem('kroma_user')
  user.value = userData ? JSON.parse(userData) : null
}

const handleClickOutside = (e) => {
  if (profileRef.value && !profileRef.value.contains(e.target)) {
    isProfileOpen.value = false
  }
}

onMounted(() => {
  muatUser()
  window.addEventListener('user-updated', muatUser)
  document.addEventListener('mousedown', handleClickOutside)
})

onBeforeUnmount(() => {
  window.removeEventListener('user-updated', muatUser)
  document.removeEventListener('mousedown', handleClickOutside)
})

const searchComics = async () => {
  if (!searchQuery.value.trim()) {
    searchResults.value = []
    return
  }
  try {
    const res = await fetch(`http://localhost:8000/api/v1/search?query=${encodeURIComponent(searchQuery.value)}`)
    const data = await res.json()
    searchResults.value = data.data
  } catch (error) {
    console.error('Pencarian gagal:', error)
  }
}

const bukaKomik = (comicId) => {
  router.push({ name: 'comic.detail', params: { comicId } })
  searchResults.value = []
  searchQuery.value = ''
}

const handleLogout = () => {
  localStorage.removeItem('kroma_token')
  localStorage.removeItem('kroma_user')
  user.value = null
  isProfileOpen.value = false
  router.push('/login')
}

const getCoverUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `http://localhost:8000/storage/${path}`
}
</script>