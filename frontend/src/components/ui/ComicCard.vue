<template>
  <button @click="$emit('click')" class="shrink-0 w-44 text-left group">
    <div class="relative aspect-[2/3] rounded-md overflow-hidden border border-gray-200 mb-2 bg-gray-100">
      <ImageWithFallback 
        :src="comic.cover" 
        :alt="comic.title" 
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
      />
      <!-- Tampilkan Rank jika props rank dikirim -->
      <div v-if="rank" class="absolute top-2 left-2 w-7 h-7 rounded-md bg-white/95 text-gray-900 font-bold text-sm flex items-center justify-center shadow-sm">
        {{ rank }}
      </div>
    </div>
    <h3 class="font-semibold text-gray-900 truncate">{{ comic.title }}</h3>
    <p class="text-xs text-gray-500 truncate">{{ comic.author }}</p>
  </button>
</template>

<script setup>
import ImageWithFallback from './ImageWithFallback.vue'

defineProps({
  comic: { type: Object, required: true },
  rank: { type: [Number, String], default: null }
})

defineEmits(['click'])
</script>