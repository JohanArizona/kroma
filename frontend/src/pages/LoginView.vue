<template>
  <div class="min-h-screen bg-white grid lg:grid-cols-2">
    
    <!-- Kolom Kiri: Form Login -->
    <div class="flex items-center justify-center p-8">
      <div class="w-full max-w-md">
        
        <!-- Logo -->
        <div class="mb-10">
          <img src="/logokroma.png" alt="Logo Kroma" class="h-10" />
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Selamat Datang Kembali
        </h1>

        <p class="text-gray-500 mb-8">
          Masuk untuk melanjutkan perjalanan membaca Anda.
        </p>
        
        <!-- Form Login -->
        <form class="space-y-5" @submit.prevent="handleLogin">
          
          <!-- Alert Error -->
          <div v-if="errorMessage" class="p-3 text-sm text-red-600 bg-red-50 rounded-md border border-red-200">
            {{ errorMessage }}
          </div>

          <!-- Email -->
          <div class="space-y-2">
            <label for="email" class="text-sm font-medium text-gray-700">Email</label>
            <input 
              v-model="form.email" 
              id="email" 
              type="email" 
              placeholder="anda@email.com" 
              class="flex h-11 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/50 focus:border-[#7C3AED]"
              required
            />
          </div>
          
          <!-- Password -->
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <label for="password" class="text-sm font-medium text-gray-700">Password</label>
              <button type="button" class="text-sm font-medium text-[#7C3AED] hover:underline">Lupa Password?</button>
            </div>
            <input 
              v-model="form.password" 
              id="password" 
              type="password" 
              placeholder="••••••••" 
              class="flex h-11 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/50 focus:border-[#7C3AED]"
              required
            />
          </div>
          
          <!-- Remember -->
          <div class="flex items-center gap-2">
            <input type="checkbox" id="remember" class="rounded border-gray-300 text-[#7C3AED] focus:ring-[#7C3AED]" />
            <label for="remember" class="text-sm text-gray-600 cursor-pointer">Ingat saya selama 30 hari</label>
          </div>
          
          <!-- Button Login Menggunakan Komponen UI -->
          <Button type="submit" :disabled="isLoading" class="w-full h-11">
            {{ isLoading ? 'Sedang Masuk...' : 'Masuk' }}
          </Button>
          
          <!-- Divider -->
          <div class="flex items-center gap-3 text-xs text-gray-400">
            <div class="flex-1 h-px bg-gray-200"></div> 
            ATAU 
            <div class="flex-1 h-px bg-gray-200"></div>
          </div>
          
          <!-- Google Button Menggunakan Komponen UI -->
          <Button type="button" variant="outline" class="w-full h-11">
            Lanjutkan dengan Google
          </Button>
        </form>
        
        <!-- Footer -->
        <p class="mt-8 text-sm text-gray-500 text-center">
          Belum punya akun?
          <router-link to="/register" class="text-[#7C3AED] font-medium hover:underline">
            Daftar
          </router-link>
        </p>
      </div>
    </div>

    <!-- Kolom Kanan: Dekorasi Grid -->
    <div class="hidden lg:flex bg-gray-50 border-l border-gray-200 items-center justify-center p-12 relative overflow-hidden">
      <div class="relative z-10 max-w-md">
        <h2 class="text-4xl font-bold text-gray-900 leading-tight mb-4">
          Ribuan cerita.<br />Satu tempat nyaman.
        </h2>
        <p class="text-gray-500">
          Webtoon premium pilihan editor dan disukai pembaca di seluruh dunia.
        </p>
      </div>
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
// Import komponen Button buatan kita
import Button from '../components/ui/Button.vue'

const router = useRouter()

const form = reactive({
  email: '',
  password: ''
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

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch('http://localhost:8000/api/v1/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: form.email,
        password: form.password
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Login gagal. Periksa kembali akun Anda.')
    }

    // Simpan token & user ke localStorage
    localStorage.setItem('kroma_token', data.data.access_token)
    localStorage.setItem('kroma_user', JSON.stringify(data.data.user))

    // 🔥 LOGIKA REDIRECT CERDAS BERDASARKAN ROLE 🔥
    if (data.data.user.role === 'admin') {
      router.replace('/admin/dashboard')
    } else {
      router.replace('/')
    }

  } catch (error) {
    errorMessage.value = error.message
  } finally {
    isLoading.value = false
  }
}
</script>