<template>
  <header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">
      
      <!-- Kiri: Logo -->
      <router-link to="/" class="flex items-center">
        <img src="/logokroma.png" alt="Kroma" class="h-7" />
      </router-link>

      <!-- Kanan: Search & Profile -->
      <div class="flex items-center gap-4">
        <button class="text-gray-500 hover:text-gray-900">
          <Search class="w-5 h-5" />
        </button>
        
        <!-- Jika Login -->
        <div v-if="user" class="flex items-center gap-3">
          <router-link to="/library" class="text-sm font-medium text-gray-600 hover:text-[#7C3AED]">Library</router-link>
          <div class="w-8 h-8 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center overflow-hidden cursor-pointer">
            <span class="text-xs font-bold text-gray-500">{{ user.name.charAt(0) }}</span>
          </div>
        </div>

        <!-- Jika Belum Login -->
        <div v-else class="flex gap-2">
          <router-link to="/login" class="text-sm font-medium text-gray-600 hover:text-gray-900 px-3 py-2">Sign In</router-link>
          <router-link to="/register" class="text-sm font-medium bg-[#7C3AED] text-white px-4 py-2 rounded-md hover:bg-[#6D28D9] transition">Sign Up</router-link>
        </div>
      </div>

    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Search } from 'lucide-vue-next'

const user = ref(null)

onMounted(() => {
  const userData = localStorage.getItem('kroma_user')
  if (userData) {
    user.value = JSON.parse(userData)
  }
})
</script>