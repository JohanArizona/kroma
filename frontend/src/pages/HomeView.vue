import { Search } from 'lucide-vue-next'
<template>
  <div class="min-h-screen bg-[#f8f8fb]">

    <!-- NAVBAR -->
    <nav class="flex items-center justify-between px-10 py-4 bg-white border-b">

      <h1 class="text-3xl font-bold text-purple-600">
        Kroma
      </h1>

      <div class="relative">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
        :size="18"
        />

        <input
        v-model="searchQuery"
        @input="searchComics"
        type="text"
        placeholder="Search comics, authors, genres..."
        class="w-[400px] px-10 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-purple-500"
      />
      
      </div>
      

    </nav>

    <!-- CONTENT -->
    <div class="max-w-6xl mx-auto py-10 px-6">

      <!-- FEATURED -->
      <div class="bg-white rounded-3xl p-10 mb-10 shadow-sm">

        <span class="bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-sm">
          Featured this week
        </span>

        <h1 class="text-5xl font-bold mt-5 mb-4">
          Shadow Monarch — Season 3
        </h1>

        <p class="text-gray-500 text-lg">
          When the world’s weakest hunter awakens an ancient power...
        </p>

      </div>

      <!-- DEFAULT COMICS -->
      <div v-if="searchResults.length === 0">

        <h2 class="text-3xl font-bold mb-6">
          Popular Right Now
        </h2>

        <div class="grid grid-cols-3 gap-6">

          <div class="bg-white rounded-2xl p-5 shadow-sm">
            <h3 class="text-2xl font-bold">
              Shadow Monarch
            </h3>

            <p class="text-gray-500 mt-2">
              Fantasy Action
            </p>
          </div>

          <div class="bg-white rounded-2xl p-5 shadow-sm">
            <h3 class="text-2xl font-bold">
              Ironclad Hearts
            </h3>

            <p class="text-gray-500 mt-2">
              Romance Drama
            </p>
          </div>

          <div class="bg-white rounded-2xl p-5 shadow-sm">
            <h3 class="text-2xl font-bold">
              Northern Wolves
            </h3>

            <p class="text-gray-500 mt-2">
              Adventure
            </p>
          </div>

        </div>

      </div>

      <!-- SEARCH RESULT -->
      <div v-else>

        <h2 class="text-3xl font-bold mb-6">
          Search Results
        </h2>

        <div class="grid grid-cols-2 gap-6">

          <div
            v-for="comic in searchResults"
            :key="comic.id"
            @click="openComic(comic.id)"
            class="bg-white rounded-2xl p-5 shadow-sm cursor-pointer hover:shadow-xl transition"
          >

            <h3 class="text-2xl font-bold">
              {{ comic.title }}
            </h3>

            <p class="text-gray-500 mt-2">
              Author: {{ comic.author }}
            </p>

            <p class="mt-4 text-gray-700">
              {{ comic.description }}
            </p>

          </div>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { Search } from 'lucide-vue-next'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const searchQuery = ref('')
const searchResults = ref([])

const searchComics = async () => {

  if (searchQuery.value.length < 2) {
    searchResults.value = []
    return
  }

  try {

    const res = await fetch(
      `http://127.0.0.1:8000/api/v1/search?query=${encodeURIComponent(searchQuery.value)}`
    )

    const data = await res.json()

    searchResults.value = data.data

  } catch (error) {

    console.error(error)

  }
}

const openComic = (id) => {

  router.push(`/comic/${id}`)

}
</script>