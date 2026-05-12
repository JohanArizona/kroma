<template>
  <div class="min-h-screen bg-gray-50 font-sans pb-12 relative">
    <Navbar />
    
    <AlertToast 
      :show="alert.show" 
      :type="alert.type" 
      :message="alert.message" 
      @close="alert.show = false" 
    />

    <ConfirmModal 
      :show="showDeleteModal"
      title="Hapus Foto Profil?"
      message="Apakah Anda yakin ingin menghapus foto profil ini? Aksi ini tidak dapat dibatalkan dan foto Anda akan kembali ke inisial nama."
      confirmText="Hapus Foto"
      @cancel="showDeleteModal = false"
      @confirm="executeDeleteAvatar"
    />

    <main class="max-w-2xl mx-auto px-6 py-10">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
          <p class="text-gray-500 mt-1">Kelola foto profil dan nama tampilan Anda di Kroma.</p>
        </div>
      </div>

      <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-8">
          
          <div class="flex flex-col md:flex-row gap-10 items-start">
            
            <div class="flex flex-col items-center gap-3 shrink-0">
              
              <div class="relative">
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-md bg-[#7C3AED]/10 flex items-center justify-center">
                  <img 
                    v-if="hasCustomAvatar"
                    :src="fullAvatarUrl" 
                    alt="User Avatar" 
                    class="w-full h-full object-cover"
                    @error="handleImageError"
                  />
                  <span v-else class="text-4xl font-bold text-[#7C3AED] tracking-wider">
                    {{ userInitials }}
                  </span>
                </div>
                
                <button 
                  @click="triggerFileInput"
                  :disabled="isUploading"
                  class="absolute bottom-1 right-1 w-9 h-9 rounded-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white flex items-center justify-center border-[3px] border-white shadow-sm transition-transform hover:scale-105 disabled:opacity-50 disabled:hover:scale-100"
                  title="Ubah Foto"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
              </div>

              <input 
                type="file" 
                ref="fileInput" 
                class="hidden" 
                accept="image/jpeg, image/png, image/webp" 
                @change="uploadAvatar"
              />

              <button 
                v-if="hasCustomAvatar"
                @click="showDeleteModal = true"
                :disabled="isDeleting"
                class="text-sm font-semibold text-red-500 hover:text-red-700 disabled:opacity-50 transition-colors"
              >
                {{ isDeleting ? 'Menghapus...' : 'Hapus Foto' }}
              </button>
              <p v-if="isUploading" class="text-xs font-medium text-[#7C3AED]">Mengunggah...</p>
            </div>

            <div class="flex-1 w-full pt-2">
              <form @submit.prevent="updateProfileName">
                <div class="space-y-5">
                  
                  <div class="space-y-1.5">
                    <label for="name" class="text-sm font-semibold text-gray-900">Nama Tampilan</label>
                    <p class="text-xs text-gray-500 mb-3">Nama ini akan terlihat oleh pengguna lain saat Anda berkomentar.</p>
                    <input 
                      id="name"
                      v-model="form.name" 
                      type="text"
                      placeholder="Masukkan nama Anda"
                      class="flex h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-base shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/50 focus:border-[#7C3AED]"
                      required
                    />
                  </div>

                  <div class="pt-2 flex items-center justify-end gap-3">
                    <button 
                      type="button" 
                      @click="form.name = user.name"
                      class="text-sm font-medium text-gray-600 hover:text-gray-900 px-4 py-2 transition-colors"
                      v-show="form.name !== user.name"
                    >
                      Batal
                    </button>
                    <Button type="submit" :disabled="isSaving || form.name === user.name" class="rounded-lg px-6 h-11 shadow-sm">
                      {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </Button>
                  </div>
                  
                </div>
              </form>
            </div>

          </div>
        </div>
      </section>

      <!-- Tombol Logout -->
      <div class="mt-6 flex justify-end">
        <Button 
          variant="outline" 
          @click="handleLogout" 
          class="rounded-lg border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 hover:text-red-700 h-10 px-5 transition-colors shadow-sm"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
          Logout
        </Button>
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Navbar from '../components/layout/Navbar.vue'
import Button from '../components/ui/Button.vue'
import AlertToast from '../components/ui/AlertToast.vue'
import ConfirmModal from '../components/ui/ConfirmModal.vue'

const router = useRouter()
const fileInput = ref(null)

// States Data User
const user = ref({ name: '', avatar_url: 'default-avatar.png' })
const form = reactive({ name: '' })

// Modal & Alert States
const showDeleteModal = ref(false)
const alert = reactive({
  show: false,
  type: 'success',
  message: ''
})

// Loading states
const isUploading = ref(false)
const isDeleting = ref(false)
const isSaving = ref(false)

// Menampilkan Toast
const showAlert = (type, message) => {
  alert.type = type
  alert.message = message
  alert.show = true
  setTimeout(() => {
    alert.show = false
  }, 4000)
}

// Computed
const hasCustomAvatar = computed(() => {
  return user.value.avatar_url && user.value.avatar_url !== 'default-avatar.png'
})

const userInitials = computed(() => {
  if (!user.value.name) return 'U'
  const names = user.value.name.trim().split(' ')
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase()
  }
  return names[0].substring(0, 2).toUpperCase()
})

const fullAvatarUrl = computed(() => {
  if (!hasCustomAvatar.value) return ''
  return `http://localhost:8000/storage/${user.value.avatar_url}`
})

const handleImageError = () => {
  user.value.avatar_url = 'default-avatar.png'
}

// 1. GET PROFILE
const fetchProfile = async () => {
  const token = localStorage.getItem('kroma_token')
  if (!token) {
    router.push('/login')
    return
  }

  try {
    const res = await fetch('http://localhost:8000/api/v1/profile/me', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await res.json()
    
    if (res.ok) {
      user.value = data.data
      form.name = data.data.name
      localStorage.setItem('kroma_user', JSON.stringify(data.data))
    } else {
      throw new Error('Sesi tidak valid')
    }
  } catch (error) {
    console.error(error)
    handleLogout()
  }
}

// 2. UPDATE PROFILE NAME
const updateProfileName = async () => {
  if (form.name === user.value.name) return
  
  isSaving.value = true
  alert.show = false
  const token = localStorage.getItem('kroma_token')

  try {
    const res = await fetch('http://localhost:8000/api/v1/profile/name', {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ name: form.name })
    })
    const data = await res.json()

    if (!res.ok) throw new Error(data.message)
    
    showAlert('success', 'Nama berhasil diperbarui!')
    await fetchProfile()
  } catch (error) {
    showAlert('error', error.message)
  } finally {
    isSaving.value = false
  }
}

// 3. UPLOAD AVATAR
const triggerFileInput = () => {
  fileInput.value.click()
}

const uploadAvatar = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  alert.show = false
  isUploading.value = true
  const token = localStorage.getItem('kroma_token')
  
  const formData = new FormData()
  formData.append('_method', 'PATCH')
  formData.append('avatar', file)

  try {
    const res = await fetch('http://localhost:8000/api/v1/profile/avatar', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      },
      body: formData
    })
    const data = await res.json()

    if (!res.ok) throw new Error(data.message)

    showAlert('success', 'Foto profil berhasil diunggah!')
    await fetchProfile()
  } catch (error) {
    showAlert('error', error.message || 'Gagal mengunggah gambar. Pastikan ukurannya di bawah 2MB.')
  } finally {
    isUploading.value = false
    event.target.value = '' 
  }
}

// 4. DELETE AVATAR (Dieksekusi dari Modal)
const executeDeleteAvatar = async () => {
  showDeleteModal.value = false
  
  alert.show = false
  isDeleting.value = true
  const token = localStorage.getItem('kroma_token')

  try {
    const res = await fetch('http://localhost:8000/api/v1/profile/avatar', {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    const data = await res.json()

    if (!res.ok) throw new Error(data.message)

    showAlert('success', 'Foto profil berhasil dihapus!')
    await fetchProfile() 
  } catch (error) {
    showAlert('error', error.message)
  } finally {
    isDeleting.value = false
  }
}

// 5. LOGOUT
const handleLogout = () => {
  localStorage.removeItem('kroma_token')
  localStorage.removeItem('kroma_user')
  router.push('/login')
}

onMounted(() => {
  fetchProfile()
})
</script>