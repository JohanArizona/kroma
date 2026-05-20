<template>
  <div class="min-h-screen bg-[#f8f8fb] p-10">

    <div class="max-w-4xl mx-auto">

      <!-- COMIC -->
      <div class="bg-white rounded-3xl p-8 shadow-sm mb-10">

        <h1 class="text-5xl font-bold">
          Shadow Monarch
        </h1>

        <p class="text-gray-500 mt-3">
          by Johan Arizona
        </p>

        <p class="mt-6 text-gray-700 leading-relaxed">
          A legendary hunter awakens a mysterious power...
        </p>

      </div>

      <!-- COMMENT SECTION -->
      <div class="bg-white rounded-3xl p-8 shadow-sm">

        <h2 class="text-3xl font-bold mb-8">
          Comments
        </h2>

        <!-- ADD COMMENT -->
        <div class="mb-10">

          <textarea
            v-model="newComment"
            placeholder="Write your comment..."
            class="w-full border rounded-2xl p-4 h-32 outline-none focus:ring-2 focus:ring-purple-500"
          ></textarea>

          <button
            @click="addComment"
            class="mt-4 bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl"
          >
            Send Comment
          </button>

        </div>

        <!-- COMMENT LIST -->
        <div class="space-y-5">

          <div
            v-for="comment in comments"
            :key="comment.id"
            class="border rounded-2xl p-5"
          >

            <div class="flex justify-between items-center mb-2">

              <h3 class="font-bold">
                {{ comment.user?.name }}
              </h3>

              <div class="space-x-3">

                <button
                  @click="startEdit(comment)"
                  class="text-blue-500"
                >
                  Edit
                </button>

                <button
                  @click="deleteComment(comment.id)"
                  class="text-red-500"
                >
                  Delete
                </button>

              </div>

            </div>

            <!-- EDIT MODE -->
            <div v-if="editingId === comment.id">

              <textarea
                v-model="editContent"
                class="w-full border rounded-xl p-3"
              ></textarea>

              <button
                @click="updateComment(comment.id)"
                class="mt-3 bg-green-600 text-white px-4 py-2 rounded-lg"
              >
                Save
              </button>

            </div>

            <!-- NORMAL -->
            <p
              v-else
              class="text-gray-700"
            >
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

const comments = ref([])

const newComment = ref('')

const editingId = ref(null)

const editContent = ref('')

const chapterId = 1

// READ COMMENTS
const fetchComments = async () => {

  try {

    const res = await fetch(
      `http://127.0.0.1:8000/api/v1/chapters/${chapterId}/comments`
    )

    const data = await res.json()

    comments.value = data.data

  } catch (error) {

    console.error(error)

  }

}

// CREATE COMMENT
const addComment = async () => {

  try {

    const token = localStorage.getItem('kroma_token')

    await fetch(
      `http://127.0.0.1:8000/api/v1/chapters/${chapterId}/comments`,
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

// START EDIT
const startEdit = (comment) => {

  editingId.value = comment.id

  editContent.value = comment.content

}

// UPDATE COMMENT
const updateComment = async (id) => {

  try {

    const token = localStorage.getItem('kroma_token')

    await fetch(
      `http://127.0.0.1:8000/api/v1/comments/${id}`,
      {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`
        },
        body: JSON.stringify({
          content: editContent.value
        })
      }
    )

    editingId.value = null

    fetchComments()

  } catch (error) {

    console.error(error)

  }

}

// DELETE COMMENT
const deleteComment = async (id) => {

  try {

    const token = localStorage.getItem('kroma_token')

    await fetch(
      `http://127.0.0.1:8000/api/v1/comments/${id}`,
      {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    fetchComments()

  } catch (error) {

    console.error(error)

  }

}

onMounted(() => {

  fetchComments()

})
</script>