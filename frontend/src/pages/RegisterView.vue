<template>
  <div class="min-h-screen bg-white grid lg:grid-cols-2">
    
    <!-- Kolom Kiri: Form Register -->
    <div class="flex items-center justify-center p-8">
      <div class="w-full max-w-md">
        
        <!-- Logo -->
        <div class="mb-10">
          <img src="/logokroma.png" alt="Logo Kroma" class="h-10" />
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Buat Akun Anda
        </h1>

        <p class="text-gray-500 mb-8">
          Bergabung dengan ribuan pembaca dan temukan cerita favorit Anda.
        </p>
        
        <!-- Form Register -->
        <form class="space-y-5" @submit.prevent="handleRegister">
          
          <!-- Alert Error -->
          <div
            v-if="errorMessage"
            class="p-3 text-sm text-red-600 bg-red-50 rounded-md"
          >
            {{ errorMessage }}
          </div>

          <!-- Nama -->
          <div class="space-y-2">
            <label
              for="name"
              class="text-sm font-medium text-gray-700"
            >
              Nama Lengkap
            </label>

            <input 
              v-model="form.name" 
              id="name" 
              type="text" 
              placeholder="Masukkan nama lengkap" 
              class="flex h-11 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-[#7C3AED]"
              required
            />
          </div>
          
          <!-- Email -->
          <div class="space-y-2">
            <label
              for="email"
              class="text-sm font-medium text-gray-700"
            >
              Email
            </label>

            <input 
              v-model="form.email" 
              id="email" 
              type="email" 
              placeholder="anda@email.com" 
              class="flex h-11 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-[#7C3AED]"
              required
            />
          </div>
          
          <!-- Password -->
          <div class="space-y-2">
            <label
              for="password"
              class="text-sm font-medium text-gray-700"
            >
              Password
            </label>

            <input 
              v-model="form.password" 
              id="password" 
              type="password" 
              placeholder="••••••••" 
              class="flex h-11 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-[#7C3AED]"
              required
            />
          </div>

          <!-- Konfirmasi Password -->
          <div class="space-y-2">
            <label
              for="password_confirmation"
              class="text-sm font-medium text-gray-700"
            >
              Konfirmasi Password
            </label>

            <input 
              v-model="form.password_confirmation" 
              id="password_confirmation" 
              type="password" 
              placeholder="••••••••" 
              class="flex h-11 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-[#7C3AED]"
              required
            />
          </div>
          
          <!-- Persetujuan -->
          <div class="flex items-center gap-2">
            <input
              type="checkbox"
              id="agree"
              v-model="form.agree"
              class="rounded border-gray-300 text-[#7C3AED] focus:ring-[#7C3AED]"
              required
            />

            <label
              for="agree"
              class="text-sm text-gray-600 cursor-pointer"
            >
              Saya menyetujui syarat dan ketentuan layanan
            </label>
          </div>
          
          <!-- Button Register -->
          <button 
            type="submit" 
            :disabled="isLoading"
            class="inline-flex w-full h-11 items-center justify-center rounded-md bg-[#7C3AED] hover:bg-[#6D28D9] text-white text-sm font-medium transition-colors disabled:opacity-50"
          >
            {{ isLoading ? 'Sedang Membuat Akun...' : 'Daftar' }}
          </button>
          
          <!-- Divider -->
          <div class="flex items-center gap-3 text-xs text-gray-400">
            <div class="flex-1 h-px bg-gray-200"></div> 
            ATAU 
            <div class="flex-1 h-px bg-gray-200"></div>
          </div>
          
          <!-- Google -->
          <button
            type="button"
            class="inline-flex w-full h-11 items-center justify-center rounded-md border border-gray-300 bg-transparent text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Lanjutkan dengan Google
          </button>
        </form>
        
        <!-- Footer -->
        <p class="mt-8 text-sm text-gray-500 text-center">
          Sudah punya akun?

          <router-link
            to="/login"
            class="text-[#7C3AED] font-medium hover:underline"
          >
            Masuk
          </router-link>
        </p>
      </div>
    </div>

    <!-- Kolom Kanan -->
    <div class="hidden lg:flex bg-gray-50 border-l border-gray-200 items-center justify-center p-12 relative overflow-hidden">
      
      <!-- Text -->
      <div class="relative z-10 max-w-md">
        <h2 class="text-4xl font-bold text-gray-900 leading-tight mb-4">
          Bergabung bersama<br />
          komunitas pembaca.
        </h2>

        <p class="text-gray-500">
          Temukan berbagai cerita menarik dan nikmati pengalaman membaca terbaik.
        </p>
      </div>
      
      <!-- Grid Background -->
      <div class="absolute inset-0 grid grid-cols-3 gap-3 p-8 opacity-30 rotate-6 scale-110 pointer-events-none">
        <div 
          v-for="(cover, index) in backgroundCovers" 
          :key="index" 
          class="aspect-[2/3] rounded-md bg-gray-300 shadow-sm bg-cover bg-center"
          :style="{ backgroundImage: `url(${cover})` }"
        ></div>
      </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  agree: false
})

const isLoading = ref(false)
const errorMessage = ref('')

const backgroundCovers = ref([
  'https://images.unsplash.com/photo-1613376023733-0a73315d9b06?w=400&h=600&fit=crop',
  'https://images.unsplash.com/photo-1578681994506-b8f463449011?w=400&h=600&fit=crop',
  'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?w=400&h=600&fit=crop',
  'https://images.unsplash.com/photo-1542779283-429940ce8336?w=400&h=600&fit=crop',
  'https://images.unsplash.com/photo-1608889825205-eebdb9fc5806?w=400&h=600&fit=crop',
  'https://images.unsplash.com/photo-1551244072-5d12893278ab?w=400&h=600&fit=crop',
  'https://images.unsplash.com/photo-1560942485-b2a11cc13456?w=400&h=600&fit=crop',
  'https://images.unsplash.com/photo-1580477667995-2b920816b511?w=400&h=600&fit=crop',
  'https://images.unsplash.com/photo-1613376023733-0a73315d9b06?w=400&h=600&fit=crop',
])

const handleRegister = async () => {

  if (form.password !== form.password_confirmation) {
    errorMessage.value = 'Konfirmasi password tidak cocok'
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {

    const response = await fetch(
      'http://localhost:8000/api/v1/auth/register',
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          name: form.name,
          email: form.email,
          password: form.password,
          password_confirmation: form.password_confirmation
        })
      }
    )

    const data = await response.json()

    if (!response.ok) {
      throw new Error(
        data.message ||
        'Registrasi gagal. Silakan coba lagi.'
      )
    }

    localStorage.setItem(
      'kroma_token',
      data.data.access_token
    )

    localStorage.setItem(
      'kroma_user',
      JSON.stringify(data.data.user)
    )

    router.push('/')

  } catch (error) {

    errorMessage.value = error.message

  } finally {

    isLoading.value = false
  }
}
</script>