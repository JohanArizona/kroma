<template>
  <div class="min-h-screen bg-[#111] font-sans">

    <!-- Header Reader -->
    <div class="sticky top-0 z-20 bg-black/80 backdrop-blur-sm border-b border-white/10">
      <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between">
        <button
          @click="router.back()"
          class="flex items-center gap-1.5 text-white/70 hover:text-white text-sm font-medium transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          Kembali
        </button>

        <div class="text-center">
          <p class="text-white text-sm font-semibold leading-tight">
            Episode {{ chapterNumber }}{{ chapterTitle ? ` — ${chapterTitle}` : '' }}
          </p>
          <p class="text-white/50 text-xs">{{ pages.length }} halaman</p>
        </div>

        <!-- Placeholder kanan supaya judul tetap tengah -->
        <div class="w-16"></div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="max-w-2xl mx-auto px-4 py-12 space-y-3">
      <div v-for="n in 5" :key="n" class="w-full bg-gray-800 animate-pulse rounded" :style="`height: ${300 + n * 40}px`"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="max-w-2xl mx-auto px-4 py-16 text-center">
      <p class="text-red-400 text-sm mb-4">{{ error }}</p>
      <button @click="router.back()" class="text-[#7C3AED] text-sm hover:underline">← Kembali ke detail komik</button>
    </div>

    <!-- Kosong -->
<div v-else-if="pages.length === 0" class="max-w-2xl mx-auto">

  <div class="px-4 py-16 text-center">
    <p class="text-gray-400 text-sm">
      Episode ini belum memiliki halaman.
    </p>

    <button
      @click="router.back()"
      class="mt-3 text-[#7C3AED] text-sm hover:underline"
    >
      ← Kembali
    </button>
  </div>

  <!-- KOMENTAR -->
  <div class="bg-[#181818] p-5">

    <h2 class="text-white text-lg font-bold mb-4">
      Komentar ({{ comments.length }})
    </h2>

    <div v-if="user" class="mb-5">

      <textarea
        v-model="commentInput"
        rows="3"
        placeholder="Tulis komentar..."
        class="w-full rounded-lg bg-[#222] text-white p-3 border border-white/10"
      ></textarea>

      <button
        @click="submitComment"
        :disabled="commentLoading"
        class="mt-3 px-5 py-2 bg-[#7C3AED] rounded-lg text-white"
      >
        {{ commentLoading ? 'Mengirim...' : 'Kirim' }}
      </button>

    </div>

    <div v-else class="text-gray-400 mb-5">
      Login untuk berkomentar
    </div>

    <div class="space-y-3">

  <div
    v-for="c in comments"
    :key="c.id"
    class="bg-[#222] rounded-lg p-3"
  >

    <div class="flex justify-between items-start">

      <div class="flex-1">

        <p class="text-[#A78BFA] text-sm font-semibold">
          {{ c.user?.name }}
        </p>

        <div v-if="editingId === c.id">

          <textarea
            v-model="editContent"
            class="mt-2 w-full rounded bg-[#333] text-white p-2"
          ></textarea>

          <button
            @click="updateComment(c.id)"
            class="mt-2 text-green-400 text-sm"
          >
            Simpan
          </button>

        </div>

        <p
          v-else
          class="text-white text-sm mt-1"
        >
          {{ c.content }}
        </p>

      </div>

      <div
        v-if="c.user_id === user?.id"
        class="ml-4 flex gap-2"
      >

        <button
          @click="startEdit(c)"
          class="text-[#A78BFA] text-sm"
        >
          Edit
        </button>

        <button
          @click="deleteComment(c.id)"
          class="text-red-400 text-sm"
        >
          Hapus
        </button>

      </div>

    </div>

  </div>

  <div
    v-if="comments.length === 0"
    class="text-gray-500"
  >
    Belum ada komentar
  </div>

</div>

  </div>

</div>

    <!-- Pages — vertical scroll webtoon style -->
    <div v-else class="max-w-2xl mx-auto">
      <img
        v-for="page in pages"
        :key="page.page_number"
        :src="getMediaUrl(page.image_url)"
        :alt="`Halaman ${page.page_number}`"
        class="w-full block"
        loading="lazy"
      />

      <!-- End of chapter -->
       <div
class="bg-[#181818] p-5"
>

<h2
class="text-white text-lg font-bold mb-4"
>

Komentar
({{ comments.length }})

</h2>

<div
v-if="user"
class="mb-5"
>

<textarea

v-model="commentInput"

rows="3"

placeholder="Tulis komentar..."

class="w-full rounded-lg bg-[#222] text-white p-3 border border-white/10"

></textarea>

<button

@click="submitComment"

:disabled="commentLoading"

class="mt-3 px-5 py-2 bg-[#7C3AED] rounded-lg text-white"

>

{{ commentLoading
? 'Mengirim...'
: 'Kirim' }}

</button>

</div>

<div
v-else
class="text-gray-400 mb-5"
>

Login untuk berkomentar

</div>

<div
class="space-y-3"
>

<div

v-for="c in comments"

:key="c.id"

class="bg-[#222] rounded-lg p-3"

>

<p
class="text-[#A78BFA] text-sm font-semibold"
>

{{ c.user?.name }}

</p>

<p
class="text-white text-sm mt-1"
>

{{ c.content }}

</p>

</div>

<div
v-if="comments.length===0"

class="text-gray-500"

>

Belum ada komentar

</div>

</div>

</div>
      <div class="py-10 text-center border-t border-white/10">
        <p class="text-gray-400 text-sm mb-4">— Selesai —</p>
        <button
          @click="router.back()"
          class="px-6 py-2.5 bg-[#7C3AED] text-white text-sm font-semibold rounded-lg hover:bg-[#6D28D9] transition"
        >
          ← Kembali ke Daftar Episode
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const chapterId = route.params.chapterId
const chapterNumber = route.query.chapterNumber || '?'
const chapterTitle = route.query.chapterTitle || ''
const token = localStorage.getItem('kroma_token')

const pages = ref([])
const isLoading = ref(true)
const error = ref('')
const comments = ref([])
const commentInput = ref('')
const commentLoading = ref(false)
const editingId = ref(null)
const editContent = ref('')


const user = JSON.parse(
localStorage.getItem('kroma_user') || 'null'
)

const getMediaUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `http://localhost:8000/storage/${path}`
}

const fetchPages = async () => {
  try {
    const res = await fetch(`http://localhost:8000/api/v1/chapters/${chapterId}/pages`, {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token ?? ''}`
      }
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message || 'Gagal memuat halaman')
    pages.value = (data.data || []).sort((a, b) => a.page_number - b.page_number)
  } catch (e) {
    error.value = e.message
  } finally {
    isLoading.value = false
  }
}

const authHeaders = () => ({
Accept:'application/json',
Authorization:`Bearer ${token ?? ''}`
})

const fetchComments = async () => {

try{

const res=
await fetch(
`http://localhost:8000/api/v1/chapters/${chapterId}/comments`,
{
headers:authHeaders()
}
)

const data=
await res.json()

if(res.ok){

comments.value=
data.data || []

}

}
catch(err){

console.log(
'Komentar gagal dimuat',
err
)

}

}

const submitComment = async () => {

if(
!commentInput.value.trim()
)return

commentLoading.value=true

try{

const res=
await fetch(
`http://localhost:8000/api/v1/chapters/${chapterId}/comments`,
{

method:'POST',

headers:{
...authHeaders(),
'Content-Type':'application/json'
},

body:JSON.stringify({

content:
commentInput.value

})

}

)

const data=
await res.json()

if(!res.ok)
throw new Error(
data.message
)

commentInput.value=''

await fetchComments()

}
catch(err){

alert(
err.message
)

}
finally{

commentLoading.value=false

}

}

const startEdit = (comment) => {

  editingId.value = comment.id
  editContent.value = comment.content

}

const updateComment = async (id) => {

  try {

    const res = await fetch(
      `http://localhost:8000/api/v1/comments/${id}`,
      {
        method: 'PATCH',
        headers: {
          ...authHeaders(),
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          content: editContent.value
        })
      }
    )

    const data = await res.json()

    if (!res.ok) {
      throw new Error(data.message)
    }

    editingId.value = null
    await fetchComments()

  } catch (err) {

    alert(err.message)

  }

}

const deleteComment = async (id) => {

  if (!confirm('Hapus komentar ini?')) return

  try {

    const res = await fetch(
      `http://localhost:8000/api/v1/comments/${id}`,
      {
        method: 'DELETE',
        headers: authHeaders()
      }
    )

    const data = await res.json()

    if (!res.ok) {
      throw new Error(data.message)
    }

    await fetchComments()

  } catch (err) {

    alert(err.message)

  }

}


onMounted(async()=>{

await fetchPages()

await fetchComments()

window.scrollTo(0,0)

})
</script>