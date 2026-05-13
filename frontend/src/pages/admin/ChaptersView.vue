<template>
  <AdminLayout title="Kelola Episode">

    <AlertToast
      :show="alert.show"
      :type="alert.type"
      :message="alert.message"
      @close="alert.show = false"
    />

    <ConfirmModal
      :show="showDeleteModal"
      title="Hapus Episode?"
      :message="`Apakah Anda yakin ingin menghapus Episode ${chapterToDelete?.chapter_number} '${chapterToDelete?.title}'? Semua halaman di dalamnya akan terhapus permanen.`"
      confirmText="Ya, Hapus"
      @cancel="showDeleteModal = false"
      @confirm="executeDelete"
    />

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
          <router-link to="/admin/comics" class="hover:text-[#7C3AED] transition-colors">Manajemen Komik</router-link>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <span class="text-gray-900 font-medium truncate max-w-[200px]">{{ comicTitle || 'Memuat...' }}</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Kelola Episode</h1>
        <p class="text-gray-500 text-sm mt-1">Tambah, edit, dan hapus chapter komik ini.</p>
      </div>
      <Button @click="openModal('create')" class="rounded-lg bg-[#7C3AED] hover:bg-[#6D28D9] text-white shadow-sm h-11 px-5 shrink-0">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Episode Baru
      </Button>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
            <tr>
              <th class="px-6 py-4 font-semibold w-24">No. Eps</th>
              <th class="px-6 py-4 font-semibold">Judul Episode</th>
              <th class="px-6 py-4 font-semibold">Jumlah Halaman</th>
              <th class="px-6 py-4 font-semibold">Dibuat Pada</th>
              <th class="px-6 py-4 font-semibold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="isLoading" class="bg-white">
              <td colspan="5" class="px-6 py-10 text-center text-gray-500 animate-pulse">Memuat data episode...</td>
            </tr>
            <tr v-else-if="chapters.length === 0" class="bg-white">
              <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                <div class="flex flex-col items-center gap-2">
                  <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                  <span>Belum ada episode. Klik "Tambah Episode Baru" untuk memulai.</span>
                </div>
              </td>
            </tr>

            <tr v-else v-for="ch in chapters" :key="ch.id" class="hover:bg-gray-50/80 transition-colors bg-white">
              <!-- Nomor Episode -->
              <td class="px-6 py-4">
                <span class="font-bold text-[#7C3AED] text-base">Eps {{ ch.chapter_number }}</span>
              </td>
              <!-- Judul -->
             <td class="px-6 py-4 font-medium text-gray-900">
  <span v-if="ch.title">{{ ch.title }}</span>
  <em v-else class="text-gray-400 font-normal">Tanpa Judul</em>
</td>
              <!-- Jumlah Halaman -->
              <td class="px-6 py-4 text-gray-500">
                {{ ch.pages_count ?? '-' }} halaman
              </td>
              <!-- Tanggal -->
              <td class="px-6 py-4 text-gray-500 text-xs">
                {{ formatDate(ch.created_at) }}
              </td>
              <!-- Aksi -->
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center justify-end gap-2">
                  <!-- Kelola Halaman -->
                  <button
                    @click="goToPages(ch)"
                    title="Kelola Halaman"
                    class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-[#7C3AED]/10 hover:border-[#7C3AED]/30 hover:text-[#7C3AED] flex items-center justify-center text-gray-500 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  </button>
                  <!-- Edit -->
                  <button
                    @click="openModal('edit', ch)"
                    title="Edit Episode"
                    class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 flex items-center justify-center text-gray-500 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>
                  <!-- Hapus -->
                  <button
                    @click="confirmDelete(ch)"
                    title="Hapus Episode"
                    class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-red-50 hover:border-red-200 hover:text-red-600 flex items-center justify-center text-gray-500 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL FORM EPISODE (Create & Edit) -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showFormModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">

          <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">{{ modalMode === 'create' ? 'Tambah Episode Baru' : 'Edit Data Episode' }}</h3>
            <button @click="showFormModal = false" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>

          <form @submit.prevent="submitForm" class="p-6 space-y-5">

            <div class="space-y-1.5">
              <label class="text-sm font-semibold text-gray-700">Nomor Episode <span class="text-red-500">*</span></label>
              <input
                v-model.number="form.chapter_number"
                type="number"
                step="0.1"
                min="0"
                placeholder="Contoh: 1, 1.5, 2..."
                class="flex h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:ring-2 focus:ring-[#7C3AED]/50 outline-none transition-shadow"
                required
              />
              <p class="text-xs text-gray-400">Gunakan desimal (misal: 1.5) untuk episode sisipan.</p>
            </div>

            <div class="space-y-1.5">
              <label class="text-sm font-semibold text-gray-700">
                Judul Episode
                <span class="text-gray-400 font-normal">(Opsional)</span>
              </label>
              <input
                v-model="form.title"
                type="text"
                placeholder="Contoh: Awal Mula Perjalanan..."
                class="flex h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:ring-2 focus:ring-[#7C3AED]/50 outline-none transition-shadow"
              />
            </div>

            <div class="flex justify-end gap-3 pt-2">
              <Button type="button" variant="outline" @click="showFormModal = false" class="rounded-lg border-gray-300 h-11 px-5">Batal</Button>
              <Button type="submit" :disabled="isSaving" class="rounded-lg bg-[#7C3AED] hover:bg-[#6D28D9] text-white h-11 px-6 shadow-sm">
                {{ isSaving ? 'Menyimpan...' : (modalMode === 'create' ? 'Tambah Episode' : 'Simpan Perubahan') }}
              </Button>
            </div>

          </form>
        </div>
      </div>
    </Transition>

  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import AdminLayout from '../../components/layout/AdminLayout.vue'
import Button from '../../components/ui/Button.vue'
import AlertToast from '../../components/ui/AlertToast.vue'
import ConfirmModal from '../../components/ui/ConfirmModal.vue'

const router = useRouter()
const route = useRoute()

// Ambil comicId dari URL param
const comicId = route.params.comicId
const token = localStorage.getItem('kroma_token')

// States
const chapters = ref([])
const comicTitle = ref('')
const isLoading = ref(true)

const alert = reactive({ show: false, type: 'success', message: '' })
const showAlert = (type, message) => {
  alert.type = type
  alert.message = message
  alert.show = true
  setTimeout(() => { alert.show = false }, 4000)
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(dateString))
}

// Navigasi ke halaman kelola pages
const goToPages = (chapter) => {
  router.push({ name: 'admin.chapter.pages', params: { chapterId: chapter.id } })
}

// === FETCH DATA ===
const fetchChapters = async () => {
  isLoading.value = true
  try {
    const res = await fetch(`http://localhost:8000/api/v1/comics/${comicId}/chapters`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (res.ok) {
      chapters.value = data.data
      // Ambil judul komik dari data pertama jika ada, atau dari comic detail
      if (data.comic_title) comicTitle.value = data.comic_title
    } else {
      throw new Error(data.message || 'Gagal memuat data')
    }
  } catch (error) {
    showAlert('error', error.message)
  } finally {
    isLoading.value = false
  }
}

// Fetch judul komik untuk breadcrumb (opsional, dari endpoint GET comics)
const fetchComicTitle = async () => {
  try {
    const res = await fetch(`http://localhost:8000/api/v1/comics`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (res.ok) {
      const comic = data.data.find(c => c.id === comicId)
      if (comic) comicTitle.value = comic.title
    }
  } catch (e) {
    // Breadcrumb tidak kritis, gagal pun tidak masalah
  }
}

// === FORM LOGIC ===
const showFormModal = ref(false)
const modalMode = ref('create')
const isSaving = ref(false)
const activeChapterId = ref(null)

const form = reactive({ chapter_number: '', title: '' })

const openModal = (mode, chapterData = null) => {
  modalMode.value = mode
  if (mode === 'edit' && chapterData) {
    activeChapterId.value = chapterData.id
    form.chapter_number = chapterData.chapter_number
    form.title = chapterData.title || ''
  } else {
    activeChapterId.value = null
    form.chapter_number = ''
    form.title = ''
  }
  showFormModal.value = true
}

const submitForm = async () => {
  isSaving.value = true
  try {
    if (modalMode.value === 'create') {
      // POST /comics/{comic_id}/chapters
      const res = await fetch(`http://localhost:8000/api/v1/comics/${comicId}/chapters`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          chapter_number: form.chapter_number,
          title: form.title || null
        })
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data.message || 'Gagal menambahkan episode')
      showAlert('success', `Episode ${form.chapter_number} berhasil ditambahkan!`)

    } else {
      // PATCH /chapters/{id}
      const res = await fetch(`http://localhost:8000/api/v1/chapters/${activeChapterId.value}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          chapter_number: form.chapter_number,
          title: form.title || null
        })
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data.message || 'Gagal memperbarui episode')
      showAlert('success', 'Data episode berhasil diperbarui!')
    }

    showFormModal.value = false
    await fetchChapters()

  } catch (error) {
    showAlert('error', error.message)
  } finally {
    isSaving.value = false
  }
}

// === DELETE LOGIC ===
const showDeleteModal = ref(false)
const chapterToDelete = ref(null)

const confirmDelete = (chapter) => {
  chapterToDelete.value = chapter
  showDeleteModal.value = true
}

const executeDelete = async () => {
  showDeleteModal.value = false
  try {
    const res = await fetch(`http://localhost:8000/api/v1/chapters/${chapterToDelete.value.id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    if (!res.ok) {
      const data = await res.json()
      throw new Error(data.message || 'Gagal menghapus episode')
    }
    showAlert('success', 'Episode beserta seluruh halamannya berhasil dihapus!')
    await fetchChapters()
  } catch (error) {
    showAlert('error', error.message)
  }
}

onMounted(() => {
  if (!token) {
    router.push('/login')
    return
  }
  fetchComicTitle()
  fetchChapters()
})
</script>