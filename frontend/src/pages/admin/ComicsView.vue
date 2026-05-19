<template>
  <AdminLayout title="Master Komik">
    
    <AlertToast 
      :show="alert.show" 
      :type="alert.type" 
      :message="alert.message" 
      @close="alert.show = false" 
    />

    <ConfirmModal 
      :show="showDeleteModal"
      title="Hapus Komik?"
      :message="`Apakah Anda yakin ingin menghapus komik '${comicToDelete?.title}'? Semua data terkait akan terhapus permanen.`"
      confirmText="Ya, Hapus"
      @cancel="showDeleteModal = false"
      @confirm="executeDelete"
    />

    <!-- Header & Tombol Tambah -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Komik</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola katalog utama, metadata, dan sampul komik.</p>
      </div>
      <Button @click="openModal('create')" class="rounded-lg bg-[#7C3AED] hover:bg-[#6D28D9] text-white shadow-sm h-11 px-5 shrink-0">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Komik Baru
      </Button>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
      
      <!-- Toolbar Pencarian -->
      <div class="p-4 border-b border-gray-100 flex items-center gap-3 bg-gray-50/50">
        <div class="relative flex-1 max-w-md">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input 
            v-model="searchQuery"
            type="text"
            placeholder="Cari berdasarkan judul atau penulis..." 
            class="w-full pl-9 h-10 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/50 transition-shadow"
          />
        </div>
      </div>

      <!-- Tabel Lengkap Sesuai Figma & JSON Backend -->
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
            <tr>
              <th class="px-6 py-4 font-semibold">Komik</th>
              <th class="px-6 py-4 font-semibold">Genre</th>
              <th class="px-6 py-4 font-semibold">Status</th>
              <th class="px-6 py-4 font-semibold">Terakhir Update</th>
              <th class="px-6 py-4 font-semibold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="isLoading" class="bg-white">
              <td colspan="5" class="px-6 py-10 text-center text-gray-500 animate-pulse">Memuat data komik...</td>
            </tr>
            <tr v-else-if="filteredComics.length === 0" class="bg-white">
              <td colspan="5" class="px-6 py-10 text-center text-gray-500">Tidak ada komik ditemukan.</td>
            </tr>
            
            <tr v-else v-for="c in filteredComics" :key="c.id" class="hover:bg-gray-50/80 transition-colors bg-white">
              <!-- Info Komik (Cover, Judul, Author) -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-14 rounded-md overflow-hidden border border-gray-200 shrink-0 bg-gray-100">
                    <ImageWithFallback :src="getMediaUrl(c.cover_url)" :alt="c.title" class="w-full h-full object-cover" />
                  </div>
                  <div>
                    <div class="font-bold text-gray-900 mb-0.5 text-base">{{ c.title }}</div>
                    <div class="text-xs text-gray-500">{{ c.author }}</div>
                  </div>
                </div>
              </td>
              <!-- Genre List -->
              <td class="px-6 py-4 text-gray-600">
                <div class="flex flex-wrap gap-1">
                  <span v-for="g in c.genres" :key="g.id" class="text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-600">
                    {{ g.name }}
                  </span>
                  <span v-if="!c.genres || c.genres.length === 0" class="text-xs text-gray-400">-</span>
                </div>
              </td>
              <!-- Status -->
              <td class="px-6 py-4">
                <span :class="[
                  'px-2.5 py-1 rounded-md text-xs font-semibold uppercase tracking-wide border',
                  c.status === 'ongoing' ? 'bg-[#7C3AED]/10 text-[#7C3AED] border-[#7C3AED]/20' : 
                  'bg-green-50 text-green-700 border-green-200'
                ]">
                  {{ c.status }}
                </span>
              </td>
              <!-- Tanggal Update -->
              <td class="px-6 py-4 text-gray-500 text-xs">
                {{ formatDate(c.updated_at) }}
              </td>
              <!-- Aksi / Tombol -->
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center justify-end gap-2">
                    <button title="Kelola Episode" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-[#7C3AED]/10 hover:border-[#7C3AED]/30 hover:text-[#7C3AED] flex items-center justify-center text-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    </button>
                  <button @click="openModal('edit', c)" title="Edit Komik" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 flex items-center justify-center text-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>
                  <button @click="confirmDelete(c)" title="Hapus Komik" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-red-50 hover:border-red-200 hover:text-red-600 flex items-center justify-center text-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL FORM KOMIK (Create & Edit) -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showFormModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl flex flex-col max-h-[90vh]">
          
          <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-white rounded-t-xl">
            <h3 class="text-xl font-bold text-gray-900">{{ modalMode === 'create' ? 'Tambah Komik Baru' : 'Edit Data Komik' }}</h3>
            <button @click="showFormModal = false" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          
          <form @submit.prevent="submitForm" class="flex flex-col overflow-hidden">
            <div class="p-6 overflow-y-auto space-y-6">
              
              <!-- PREVIEW GAMBAR GEDE -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cover Upload -->
                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Cover Komik <span class="text-red-500">*</span></label>
                  <div 
                    class="relative w-full aspect-[2/3] max-w-[200px] border-2 border-dashed rounded-xl overflow-hidden group cursor-pointer transition-colors"
                    :class="previews.cover ? 'border-transparent' : 'border-gray-300 hover:border-[#7C3AED] bg-gray-50'"
                    @click="$refs.coverInput.click()"
                  >
                    <img v-if="previews.cover" :src="previews.cover" class="w-full h-full object-cover" />
                    <div v-else class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 p-4 text-center">
                      <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                      <span class="text-sm font-medium">Klik untuk upload</span>
                      <span class="text-xs mt-1">Rasio 2:3 (Maks 2MB)</span>
                    </div>
                    <!-- Hover Edit -->
                    <div v-if="previews.cover" class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                      <span class="text-white text-sm font-medium">Ganti Cover</span>
                    </div>
                  </div>
                  <input ref="coverInput" type="file" @change="e => handleFile(e, 'cover')" accept="image/*" class="hidden" />
                </div>

                <!-- Banner Upload -->
                <div class="space-y-2">
                  <label class="text-sm font-semibold text-gray-700">Banner / Latar <span class="text-gray-400 font-normal">(Opsional)</span></label>
                  <div 
                    class="relative w-full aspect-video border-2 border-dashed rounded-xl overflow-hidden group cursor-pointer transition-colors"
                    :class="previews.banner ? 'border-transparent' : 'border-gray-300 hover:border-[#7C3AED] bg-gray-50'"
                    @click="$refs.bannerInput.click()"
                  >
                    <img v-if="previews.banner" :src="previews.banner" class="w-full h-full object-cover" />
                    <div v-else class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 p-4 text-center">
                      <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                      <span class="text-sm font-medium">Klik untuk upload</span>
                      <span class="text-xs mt-1">Landscape (Maks 4MB)</span>
                    </div>
                    <div v-if="previews.banner" class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                      <span class="text-white text-sm font-medium">Ganti Banner</span>
                    </div>
                  </div>
                  <input ref="bannerInput" type="file" @change="e => handleFile(e, 'banner')" accept="image/*" class="hidden" />
                </div>
              </div>

              <!-- Input Metadata -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5 md:col-span-2">
                  <label class="text-sm font-semibold text-gray-700">Judul Komik</label>
                  <input v-model="form.title" type="text" placeholder="Masukkan judul..." class="flex h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:ring-2 focus:ring-[#7C3AED]/50 outline-none transition-shadow" required />
                </div>
                
                <div class="space-y-1.5">
                  <label class="text-sm font-semibold text-gray-700">Penulis / Author</label>
                  <input v-model="form.author" type="text" placeholder="Nama kreator..." class="flex h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:ring-2 focus:ring-[#7C3AED]/50 outline-none transition-shadow" required />
                </div>
                
                <div class="space-y-1.5">
                  <label class="text-sm font-semibold text-gray-700">Status Publikasi</label>
                  <select v-model="form.status" class="flex h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:ring-2 focus:ring-[#7C3AED]/50 outline-none bg-white transition-shadow" required>
                    <option value="ongoing">Ongoing (Sedang Berjalan)</option>
                    <option value="completed">Completed (Tamat)</option>
                  </select>
                </div>
              </div>

              <!-- Pilihan Genre (Checkboxes) BUG FIXED -->
              <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700 block">Pilih Genre <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-2">
                  <label v-for="g in availableGenres" :key="g" class="cursor-pointer">
                    <input type="checkbox" :value="g" v-model="form.genres" class="peer hidden" />
                    <div class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 select-none transition-colors peer-checked:bg-[#7C3AED] peer-checked:text-white peer-checked:border-[#7C3AED] hover:bg-gray-50">
                      {{ g }}
                    </div>
                  </label>
                </div>
                <p v-if="form.genres.length === 0" class="text-xs text-red-500 mt-1">Minimal pilih 1 genre.</p>
              </div>

              <!-- Sinopsis -->
              <div class="space-y-1.5">
                <label class="text-sm font-semibold text-gray-700">Sinopsis</label>
                <textarea v-model="form.synopsis" placeholder="Tulis ringkasan cerita yang menarik..." class="w-full min-h-[120px] px-3 py-3 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#7C3AED]/50 outline-none transition-shadow" required></textarea>
              </div>

            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 rounded-b-xl shrink-0">
              <Button type="button" variant="outline" @click="showFormModal = false" class="rounded-lg border-gray-300 h-11 px-5">Batal</Button>
              <Button type="submit" :disabled="isSaving || form.genres.length === 0" class="rounded-lg bg-[#7C3AED] hover:bg-[#6D28D9] text-white h-11 px-6 shadow-sm">
                {{ isSaving ? 'Menyimpan...' : (modalMode === 'create' ? 'Tambah Komik' : 'Simpan Perubahan') }}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AdminLayout from '../../components/layout/AdminLayout.vue'
import Button from '../../components/ui/Button.vue'
import AlertToast from '../../components/ui/AlertToast.vue'
import ConfirmModal from '../../components/ui/ConfirmModal.vue'
import ImageWithFallback from '../../components/ui/ImageWithFallback.vue'

const router = useRouter()
const token = localStorage.getItem('kroma_token')

// Daftar Genre (Static List sesuai standar DB-mu)
const availableGenres = ['Action', 'Romance', 'Fantasy', 'Sci-Fi', 'Drama', 'Comedy', 'Slice of Life', 'Horror', 'Mystery']

// States Utama
const comics = ref([])
const isLoading = ref(true)
const searchQuery = ref('')

const alert = reactive({ show: false, type: 'success', message: '' })
const showAlert = (type, message) => {
  alert.type = type; alert.message = message; alert.show = true
  setTimeout(() => alert.show = false, 4000)
}

// Helpers
const getMediaUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `http://localhost:8000/storage/${path}`
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(date)
}

const filteredComics = computed(() => {
  if (!searchQuery.value) return comics.value
  const query = searchQuery.value.toLowerCase()
  return comics.value.filter(c => 
    c.title.toLowerCase().includes(query) || 
    c.author.toLowerCase().includes(query)
  )
})

// === FETCH DATA ===
const fetchComics = async () => {
  isLoading.value = true
  try {
    const res = await fetch(`http://localhost:8000/api/v1/search?query=${encodeURIComponent(searchQuery.value)}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (res.ok) comics.value = data.data
  } catch (error) {
    showAlert('error', 'Gagal memuat daftar komik.')
  } finally {
    isLoading.value = false
  }
}

// === FORM LOGIC ===
const showFormModal = ref(false)
const modalMode = ref('create')
const isSaving = ref(false)
const activeComicId = ref(null)

const form = reactive({ title: '', author: '', status: 'ongoing', synopsis: '', genres: [] })
const formFiles = reactive({ cover: null, banner: null })
const previews = reactive({ cover: null, banner: null })

const handleFile = (e, type) => {
  const file = e.target.files[0]
  if (!file) return
  formFiles[type] = file
  previews[type] = URL.createObjectURL(file) // Buat URL lokal untuk preview langsung
}

const openModal = (mode, comicData = null) => {
  modalMode.value = mode
  formFiles.cover = null
  formFiles.banner = null
  previews.cover = null
  previews.banner = null
  
  if (mode === 'edit' && comicData) {
    activeComicId.value = comicData.id
    form.title = comicData.title
    form.author = comicData.author
    form.status = comicData.status
    form.synopsis = comicData.synopsis
    // Map genre objects ke array of string
    form.genres = comicData.genres ? comicData.genres.map(g => g.name) : []
    
    // Set preview dari DB
    if (comicData.cover_url) previews.cover = getMediaUrl(comicData.cover_url)
    if (comicData.banner_url) previews.banner = getMediaUrl(comicData.banner_url)
  } else {
    activeComicId.value = null
    form.title = ''
    form.author = ''
    form.status = 'ongoing'
    form.synopsis = ''
    form.genres = []
  }
  showFormModal.value = true
}

const submitForm = async () => {
  isSaving.value = true
  
  try {
    if (modalMode.value === 'create') {
      if (!formFiles.cover) throw new Error("Cover komik wajib diunggah.")
      
      const formData = new FormData()
      formData.append('title', form.title)
      formData.append('author', form.author)
      formData.append('status', form.status)
      formData.append('synopsis', form.synopsis)
      
      // CARA NGIRIM ARRAY KE LARAVEL FORM DATA!
      form.genres.forEach(genre => {
        formData.append('genres[]', genre)
      })

      formData.append('cover', formFiles.cover)
      if (formFiles.banner) formData.append('banner', formFiles.banner)

      const res = await fetch('http://localhost:8000/api/v1/comics', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        body: formData
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data.message || 'Gagal menambahkan komik')
      showAlert('success', 'Komik berhasil ditambahkan!')
      
    } else {
      // Logic EDIT (PATCH Metadata)
      const resMeta = await fetch(`http://localhost:8000/api/v1/comics/${activeComicId.value}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        body: JSON.stringify(form)
      })
      if (!resMeta.ok) throw new Error('Gagal memperbarui metadata komik')

      // Logic EDIT (PATCH Media - Cuma kalau ganti gambar)
      if (formFiles.cover || formFiles.banner) {
        const mediaData = new FormData()
        mediaData.append('_method', 'PATCH')
        if (formFiles.cover) mediaData.append('cover', formFiles.cover)
        if (formFiles.banner) mediaData.append('banner', formFiles.banner)

        const resMedia = await fetch(`http://localhost:8000/api/v1/comics/${activeComicId.value}/media`, {
          method: 'POST',
          headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
          body: mediaData
        })
        if (!resMedia.ok) throw new Error('Metadata tersimpan, tapi gagal mengubah gambar.')
      }
      showAlert('success', 'Data komik berhasil diperbarui!')
    }
    
    showFormModal.value = false
    await fetchComics() // Tarik ulang data
    
  } catch (error) {
    showAlert('error', error.message)
  } finally {
    isSaving.value = false
  }
}

// === DELETE LOGIC ===
const showDeleteModal = ref(false)
const comicToDelete = ref(null)

const confirmDelete = (comic) => {
  comicToDelete.value = comic
  showDeleteModal.value = true
}

const executeDelete = async () => {
  showDeleteModal.value = false
  try {
    const res = await fetch(`http://localhost:8000/api/v1/comics/${comicToDelete.value.id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    if (!res.ok) throw new Error('Gagal menghapus komik')
    
    showAlert('success', 'Komik berhasil dihapus!')
    await fetchComics()
  } catch (error) {
    showAlert('error', error.message)
  }
}

onMounted(() => {
  if (!token) router.push('/login')
  else fetchComics()
})
</script>