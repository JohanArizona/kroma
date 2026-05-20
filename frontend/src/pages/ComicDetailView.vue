<template>
  <div class="min-h-screen bg-gray-50 p-8">

    <div class="max-w-5xl mx-auto">

      <!-- Comic Detail -->
      <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">

        <h1 class="text-3xl font-bold mb-2">
          {{ comic.title }}
        </h1>

        <p class="text-gray-500 mb-4">
          Author: {{ comic.author }}
        </p>

        <p class="text-gray-700">
          {{ comic.description }}
        </p>

      </div>

      <!-- Comment Section -->
      <div class="bg-white rounded-2xl shadow-sm p-6">

        <h2 class="text-2xl font-bold mb-6">
          Comments
        </h2>

        <!-- Add Comment -->
        <div class="mb-6">

          <textarea
            v-model="newComment"
            placeholder="Tulis komentar..."
            class="w-full border rounded-xl p-4 mb-4"
          ></textarea>

          <button
            @click="addComment"
            class="px-5 py-2 rounded-lg bg-[#7C3AED] text-white"
          >
            Kirim Komentar
          </button>

        </div>

        <!-- Comment List -->
        <div class="space-y-4">

          <div
            v-for="comment in comments"
            :key="comment.id"
            class="border rounded-xl p-4"
          >

            <div class="font-semibold mb-1">
              {{ comment.user?.name }}
            </div>

            <p class="text-gray-700">
              {{ comment.content }}
            </p>

          </div>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const comic = ref({
  title: 'Comic Dummy',
  author: 'Unknown',
  description: 'Deskripsi comic'
})

const comments = ref([])

const newComment = ref('')

const fetchComments = async () => {

  try {

    const res = await fetch(
      'http://localhost:8000/api/v1/chapters/1/comments'
    )

    const data = await res.json()

    comments.value = data.data

  } catch (error) {

    console.error(error)

  }
}

const addComment = async () => {

  try {

    const token = localStorage.getItem('kroma_token')

    await fetch(
      'http://localhost:8000/api/v1/chapters/1/comments',
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`
        },
        body: JSON.stringify({
          content: newComment.value
        })
      }
    )

    newComment.value = ''

    fetchComments()

  } catch (error) {

    console.error(error)

  }
}

onMounted(() => {
  fetchComments()
})
</script>