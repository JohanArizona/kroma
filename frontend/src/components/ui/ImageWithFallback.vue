<template>
  <img 
    :src="currentSrc" 
    :alt="alt" 
    @error="handleError"
    :class="$attrs.class"
  />
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  src: { type: String, required: true },
  alt: { type: String, default: '' }
})

const currentSrc = ref(props.src)

// Kalau props src berubah, update currentSrc
watch(() => props.src, (newSrc) => {
  currentSrc.value = newSrc
})

const handleError = () => {
  // Ganti dengan gambar dummy abu-abu jika error
  currentSrc.value = 'https://images.unsplash.com/photo-1612036782180-6f0822045d23?w=400&q=80&blur=10'
}
</script>