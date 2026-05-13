<template>
  <AdminLayout title="Kelola Halaman">

    <AlertToast
      :show="alert.show"
      :type="alert.type"
      :message="alert.message"
      @close="alert.show = false"
    />

    <ConfirmModal
      :show="showDeleteConfirm"
      title="Hapus Semua Halaman?"
      message="Apakah Anda yakin ingin menghapus SEMUA halaman pada episode ini? Aksi ini tidak bisa dibatalkan."
      confirmText="Ya, Hapus Semua"
      @cancel="showDeleteConfirm = false"
      @confirm="executeDeleteAll"
    />

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
          <router-link to="/admin/comics" class="hover:text-[#7C3AED] transition-colors">Manajemen Komik</router-link>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <router-link :to="`/admin/comics/${comicId}/chapters`" class="hover:text-[#7C3AED] transition-colors truncate max-w-[150px]">Kelola Episode</router-link>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <span class="text-gray-900 font-medium">Halaman Episode {{ chapterNumber }}</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Kelola Halaman Komik</h1>
        <p class="text-gray-500 text-sm mt-1">Upload, lihat, dan atur urutan halaman episode ini.</p>
      </div>
    </div>

    <!-- Panel Upload -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6">
      <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        Upload Halaman Baru
      </h2>

      <!-- Drop Zone -->
      <div
        class="border-2 border-dashed rounded-xl p-8 text-center transition-colors cursor-pointer"
        :class="isDragging ? 'border-[#7C3AED] bg-[#7C3AED]/5' : 'border-gray-300 hover:border-[#7C3AED] hover:bg-gray-50'"
        @click="$refs.pageInput.click()"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
      >
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        <p class="text-sm font-medium text-gray-700 mb-1">Klik atau seret gambar ke sini</p>
        <p class="text-xs text-gray-400">Format: JPEG, PNG, WebP · Maks. 2MB per file · Bisa pilih banyak sekaligus</p>
        <input
          ref="pageInput"
          type="file"
          multiple
          accept="image/jpeg,image/png,image/webp"
          @change="handleFileSelect"
          class="hidden"
        />
      </div>

      <!-- Preview Antrian Upload -->
      <div v-if="uploadQueue.length > 0" class="mt-4 space-y-2">
        <p class="text-sm font-semibold text-gray-700">{{ uploadQueue.length }} file siap diunggah:</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
          <div
            v-for="(item, idx) in uploadQueue"
            :key="idx"
            class="relative aspect-[2/3] rounded-lg overflow-hidden border border-gray-200 bg-gray-100 group"
          >
            <img :src="item.preview" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-black/40 flex items-end p-1.5">
              <span class="text-white text-xs font-bold">{{ idx + 1 }}</span>
            </div>
            <button
              @click.stop="removeFromQueue(idx)"
              class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
            >×</button>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
          <Button type="button" variant="outline" @click="clearQueue" class="rounded-lg border-gray-300 h-10 px-4 text-sm">Bersihkan</Button>
          <Button @click="submitUpload" :disabled="isUploading" class="rounded-lg bg-[#7C3AED] hover:bg-[#6D28D9] text-white h-10 px-5 text-sm shadow-sm">
            {{ isUploading ? `Mengunggah...` : `Upload ${uploadQueue.length} Halaman` }}
          </Button>
        </div>
      </div>
    </div>

    <!-- Panel Daftar Halaman -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
      <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900">
          Halaman Tersimpan
          <span class="text-gray-400 font-normal text-sm ml-1">({{ pages.length }} halaman)</span>
        </h2>
        <div class="flex items-center gap-2">
          <span v-if="isReordering" class="text-xs text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1 rounded-md font-medium">Mode Susun Ulang Aktif</span>
          <Button
            v-if="pages.length > 1"
            @click="toggleReorder"
            :class="isReordering ? 'bg-amber-500 hover:bg-amber-600 text-white border-transparent' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
            class="rounded-lg border h-9 px-4 text-xs font-medium transition-colors"
          >
            {{ isReordering ? 'Simpan Urutan' : 'Susun Ulang' }}
          </Button>
          <Button
            v-if="pages.length > 0"
            @click="showDeleteConfirm = true"
            class="rounded-lg border border-red-200 text-red-500 hover:bg-red-50 h-9 px-4 text-xs font-medium"
          >
            Hapus Semua
          </Button>
        </div>
      </div>

      <div class="p-6">
        <div v-if="isLoading" class="text-center text-gray-500 text-sm py-8 animate-pulse">Memuat halaman...</div>

        <div v-else-if="pages.length === 0" class="text-center text-gray-500 text-sm py-8">
          <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          Belum ada halaman. Upload gambar di atas untuk memulai.
        </div>

        <div v-else class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
          <div
            v-for="(page, idx) in pages"
            :key="page.id"
            class="relative aspect-[2/3] rounded-lg overflow-hidden border-2 transition-all group"
            :class="isReordering ? 'border-amber-300 cursor-grab active:cursor-grabbing' : 'border-gray-200'"
            :draggable="isReordering"
            @dragstart="onDragStart(idx)"
            @dragover.prevent="onDragOver(idx)"
            @dragend="onDragEnd"
          >
            <ImageWithFallback
              :src="getMediaUrl(page.image_url)"
              :alt="`Halaman ${page.page_number}`"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-2">
              <span class="text-white text-xs font-bold">{{ page.page_number }}</span>
            </div>
            <div v-if="isReordering" class="absolute top-1.5 right-1.5 w-5 h-5 bg-amber-400 rounded text-white flex items-center justify-center opacity-80">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-6 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-6 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
            </div>
          </div>
        </div>

      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import AdminLayout from '../../components/layout/AdminLayout.vue'
import Button from '../../components/ui/Button.vue'
import AlertToast from '../../components/ui/AlertToast.vue'
import ConfirmModal from '../../components/ui/ConfirmModal.vue'
import ImageWithFallback from '../../components/ui/ImageWithFallback.vue'

const router = useRouter()
const route = useRoute()

const chapterId = route.params.chapterId
const token = localStorage.getItem('kroma_token')

const comicId = route.query.comicId || ''
const chapterNumber = ref(route.query.chapterNumber || '?')

const pages = ref([])
const isLoading = ref(true)

// Alert dengan durasi lebih lama untuk error agar sempat dibaca
const alert = reactive({ show: false, type: 'success', message: '' })
const showAlert = (type, message) => {
  alert.type = type
  alert.message = message
  alert.show = true
  // Error tampil 10 detik, sukses 4 detik
  setTimeout(() => { alert.show = false }, type === 'error' ? 10000 : 4000)
}

const getMediaUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `http://localhost:8000/storage/${path}`
}

// === FETCH PAGES ===
const fetchPages = async () => {
  isLoading.value = true
  try {
    const res = await fetch(`http://localhost:8000/api/v1/chapters/${chapterId}/pages`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (res.ok) {
      pages.value = (data.data || []).sort((a, b) => a.page_number - b.page_number)
      console.log('[fetchPages] Halaman dimuat:', pages.value.length, 'item')
      console.log('[fetchPages] Sample ID pertama:', pages.value[0]?.id, '| tipe:', typeof pages.value[0]?.id)
    } else {
      throw new Error(data.message || 'Gagal memuat halaman')
    }
  } catch (error) {
    showAlert('error', error.message)
  } finally {
    isLoading.value = false
  }
}

// === UPLOAD LOGIC ===
const uploadQueue = ref([])
const isUploading = ref(false)
const isDragging = ref(false)

const processFiles = (fileList) => {
  const newFiles = Array.from(fileList).filter(f => f.type.startsWith('image/'))
  newFiles.forEach(file => {
    uploadQueue.value.push({ file, preview: URL.createObjectURL(file) })
  })
}

const handleFileSelect = (e) => processFiles(e.target.files)
const handleDrop = (e) => {
  isDragging.value = false
  processFiles(e.dataTransfer.files)
}

const removeFromQueue = (idx) => {
  URL.revokeObjectURL(uploadQueue.value[idx].preview)
  uploadQueue.value.splice(idx, 1)
}

const clearQueue = () => {
  uploadQueue.value.forEach(item => URL.revokeObjectURL(item.preview))
  uploadQueue.value = []
}

const submitUpload = async () => {
  if (uploadQueue.value.length === 0) return
  isUploading.value = true
  try {
    const formData = new FormData()
    uploadQueue.value.forEach(item => {
      formData.append('pages[]', item.file)
    })
    const res = await fetch(`http://localhost:8000/api/v1/chapters/${chapterId}/pages`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      },
      body: formData
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Gagal mengunggah halaman')
    showAlert('success', `${uploadQueue.value.length} halaman berhasil diunggah!`)
    clearQueue()
    await fetchPages()
  } catch (error) {
    showAlert('error', error.message)
  } finally {
    isUploading.value = false
  }
}

// === REORDER LOGIC ===
const isReordering = ref(false)
const dragFromIdx = ref(null)

const toggleReorder = async () => {
  if (isReordering.value) {
    await saveReorder()
  }
  isReordering.value = !isReordering.value
}

const onDragStart = (idx) => { dragFromIdx.value = idx }
const onDragOver = (idx) => {
  if (dragFromIdx.value === null || dragFromIdx.value === idx) return
  const arr = [...pages.value]
  const [moved] = arr.splice(dragFromIdx.value, 1)
  arr.splice(idx, 0, moved)
  pages.value = arr.map((p, i) => ({ ...p, page_number: i + 1 }))
  dragFromIdx.value = idx
}
const onDragEnd = () => { dragFromIdx.value = null }

const saveReorder = async () => {
  try {
    const payload = {
      pages: pages.value.map(p => ({
        id: p.id,
        page_number: p.page_number
      }))
    }

    // === DEBUG — lihat di Console browser ===
    console.log('[Reorder] Total halaman:', payload.pages.length)
    console.log('[Reorder] Payload lengkap:', JSON.stringify(payload, null, 2))

    const res = await fetch(`http://localhost:8000/api/v1/chapters/${chapterId}/pages/reorder`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })

    const data = await res.json()

    // === DEBUG — lihat response backend ===
    console.log('[Reorder] HTTP Status:', res.status)
    console.log('[Reorder] Response backend:', JSON.stringify(data, null, 2))

    if (!res.ok) {
      throw new Error(`[${res.status}] ${data.message || JSON.stringify(data)}`)
    }

    showAlert('success', 'Urutan halaman berhasil disimpan!')

  } catch (error) {
    console.error('[Reorder] Error final:', error)
    showAlert('error', error.message)
    await fetchPages()
  }
}

// === DELETE ALL ===
const showDeleteConfirm = ref(false)

const executeDeleteAll = async () => {
  showDeleteConfirm.value = false
  showAlert('error', 'Fitur hapus semua halaman membutuhkan implementasi backend tambahan.')
}

onMounted(() => {
  if (!token) {
    router.push('/login')
    return
  }
  fetchPages()
})
</script>