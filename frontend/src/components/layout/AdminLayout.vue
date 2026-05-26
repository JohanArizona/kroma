<template>
  <div class="flex min-h-screen bg-[#F9FAFB] font-sans">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed h-full z-40">
      <div class="h-16 flex items-center px-6 border-b border-gray-200">
        <img src="/logokroma.png" alt="Kroma Logo" class="h-7" />
      </div>
      
      <nav class="flex-1 p-4 space-y-1">
        <router-link 
          to="/admin/dashboard" 
          class="flex items-center gap-3 px-3 py-2.5 rounded-md font-medium text-sm transition-colors"
          active-class="bg-[#7C3AED]/10 text-[#7C3AED]"
          exact-active-class="bg-[#7C3AED]/10 text-[#7C3AED]"
          :class="[$route.path.includes('/dashboard') ? '' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900']"
        >
          <LayoutDashboard class="w-5 h-5" />
          Dashboard
        </router-link>
        
        <router-link 
          to="/admin/comics" 
          class="flex items-center gap-3 px-3 py-2.5 rounded-md font-medium text-sm transition-colors"
          active-class="bg-[#7C3AED]/10 text-[#7C3AED]"
          :class="[$route.path.includes('/comics') ? '' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900']"
        >
          <Library class="w-5 h-5" />
          Master Comics
        </router-link>
      </nav>

      <div class="p-4 border-t border-gray-200">
        <button 
          @click="handleLogout"
          class="flex items-center gap-3 px-3 py-2.5 w-full rounded-md text-red-600 hover:bg-red-50 font-medium text-sm transition-colors"
        >
          <LogOut class="w-5 h-5" />
          Keluar
        </button>
      </div>
    </aside>

    <div class="flex-1 flex flex-col ml-64">
      
      <!-- Topbar -->
      <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-30">
        <h1 class="text-xl font-bold text-gray-900 capitalize">{{ title }}</h1>
        
        <!-- Profile dengan Dropdown -->
        <div class="relative" ref="profileRef">
          <button
            @click="isProfileOpen = !isProfileOpen"
            class="flex items-center gap-3 hover:bg-gray-50 rounded-lg px-2 py-1.5 transition-colors"
          >
            <div class="text-right">
              <p class="text-sm font-bold text-gray-900">{{ adminUser?.name || 'Admin' }}</p>
              <p class="text-xs text-gray-500 capitalize">{{ adminUser?.role || 'Administrator' }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-gray-200 border border-gray-300 overflow-hidden flex items-center justify-center">
              <span class="text-gray-500 font-bold">{{ adminUser?.name?.charAt(0) || 'A' }}</span>
            </div>
            <ChevronDown 
              class="w-4 h-4 text-gray-400 transition-transform duration-200"
              :class="{ 'rotate-180': isProfileOpen }"
            />
          </button>

          <!-- Dropdown Menu -->
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
              <!-- Info singkat -->
              <div class="px-4 py-3 border-b border-gray-100">
                <p class="text-sm font-semibold text-gray-900">{{ adminUser?.name || 'Admin' }}</p>
                <p class="text-xs text-gray-500 mt-0.5 capitalize">{{ adminUser?.role || 'Administrator' }}</p>
              </div>

              <!-- Tombol Logout -->
              <div class="p-1.5">
                <button
                  @click="handleLogout"
                  class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                >
                  <LogOut class="w-4 h-4" />
                  Keluar
                </button>
              </div>
            </div>
          </Transition>
        </div>

      </header>

      <main class="p-8">
        <slot />
      </main>
      
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { LayoutDashboard, Library, LogOut, ChevronDown } from 'lucide-vue-next'

defineProps({
  title: { type: String, default: 'Admin Panel' }
})

const router = useRouter()
const adminUser = ref(null)
const isProfileOpen = ref(false)
const profileRef = ref(null)

onMounted(() => {
  const userStr = localStorage.getItem('kroma_user')
  if (userStr) adminUser.value = JSON.parse(userStr)

  document.addEventListener('mousedown', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})

// Tutup dropdown kalau klik di luar area profile
const handleClickOutside = (e) => {
  if (profileRef.value && !profileRef.value.contains(e.target)) {
    isProfileOpen.value = false
  }
}

const handleLogout = () => {
  localStorage.removeItem('kroma_token')
  localStorage.removeItem('kroma_user')
  router.push('/login')
}
</script>