<template>
  <header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">
      
      <!-- Kiri: Logo -->
      <router-link to="/" class="flex items-center">
        <img src="/logokroma.png" alt="Kroma" class="h-7" />
      </router-link>

      <!-- Kanan: Search & Profile -->
      <div class="flex items-center gap-4">

        <!-- SEARCH BAR -->
        <div class="relative hidden md:block">

          <Search
            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"
          />

          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari komik, genre, author..."
            class="w-72 pl-10 pr-4 py-2 rounded-full border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#7C3AED]"
            @input="searchComics"
          />

          <!-- SEARCH RESULT -->
          <div
            v-if="searchResults.length"
            class="absolute top-12 left-0 w-full bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden"
          >

            <div
v-for="comic in searchResults"
:key="comic.id"

@click="openComic(comic.id)"

class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition"
>

              <img
                :src="getCoverUrl(comic.cover_url)"
                class="w-10 h-14 object-cover rounded-md"
              />

              <div>

                <h3 class="text-sm font-semibold text-gray-900">
                  {{ comic.title }}
                </h3>

                <p class="text-xs text-gray-500">
                  {{ comic.author }}
                </p>

              </div>

            </div>

          </div>

        </div>
        
        <!-- Jika Login -->
<div v-if="user" class="flex items-center gap-3">

  <router-link
    to="/library"
    class="text-sm font-medium text-gray-600 hover:text-[#7C3AED]"
  >
    Library
  </router-link>

  <div
    @click="openProfile"
    class="w-8 h-8 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center overflow-hidden cursor-pointer hover:ring-2 hover:ring-[#7C3AED]/30 transition"
  >

    <span class="text-xs font-bold text-gray-500">
      {{ user.name.charAt(0) }}
    </span>

  </div>

</div>

        <!-- Jika Belum Login -->
        <div v-else class="flex gap-2">

          <router-link
            to="/login"
            class="text-sm font-medium text-gray-600 hover:text-gray-900 px-3 py-2"
          >
            Sign In
          </router-link>

          <router-link
            to="/register"
            class="text-sm font-medium bg-[#7C3AED] text-white px-4 py-2 rounded-md hover:bg-[#6D28D9] transition"
          >
            Sign Up
          </router-link>

        </div>

      </div>

    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Search } from 'lucide-vue-next'


const user = ref(null)
const router = useRouter()

const searchQuery = ref('')
const searchResults = ref([])


onMounted(() => {

const loadUser=()=>{

const userData=
localStorage.getItem(
'kroma_user'
)

if(userData){

user.value=
JSON.parse(
userData
)

}

}

// load pertama
loadUser()


// update realtime
window.addEventListener(
'user-updated',
loadUser

)

const openProfile = () => {

router.push(
'/profile'
)

}

})

const searchComics = async () => {

  if (!searchQuery.value.trim()) {
    searchResults.value = []
    return
  }

  try {

    const res = await fetch(
      `http://localhost:8000/api/v1/search?query=${encodeURIComponent(searchQuery.value)}`
    )

    const data = await res.json()

    searchResults.value = data.data

  } catch (error) {

    console.error('Search gagal:', error)

  }

}

const openComic = (comicId) => {

router.push({
name:'comic.detail',
params:{
comicId
}
})

// tutup dropdown
searchResults.value=[]

// kosongkan input
searchQuery.value=''

}

const openProfile = () => {

router.push(
'/profile'
)
}

const getCoverUrl = (path) => {

  if (!path) return ''

  if (path.startsWith('http')) {
    return path
  }

  return `http://localhost:8000/storage/${path}`

}
</script>