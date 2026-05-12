<template>
  <div class="min-h-screen flex items-center justify-center bg-white">
    <div class="text-center">
      <h1 class="text-3xl font-bold text-gray-900 mb-4">Welcome to Kroma</h1>
      <p class="text-gray-500 mb-8">Dashboard akan di-update segera</p>
      <button 
        @click="handleLogout"
        class="inline-flex h-11 items-center justify-center rounded-md bg-[#7C3AED] hover:bg-[#6D28D9] text-white text-sm font-medium transition-colors px-6"
      >
        Logout
      </button>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const handleLogout = () => {
  localStorage.removeItem('kroma_token')
  localStorage.removeItem('kroma_user')
  router.push('/login')
}
</script>
