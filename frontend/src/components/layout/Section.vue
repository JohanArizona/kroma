<template>
  <section class="mb-10">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
        <slot name="icon" />
        {{ title }}
      </h2>
      <button class="text-sm text-[#7C3AED] hover:underline flex items-center gap-1 font-medium">
        See all 
        <ChevronRight class="w-4 h-4" />
      </button>
    </div>
    
    <!-- Konten di dalamnya (Card komik dll) -->
    <slot />
  </section>
</template>

<script setup>
import { ChevronRight } from 'lucide-vue-next'

defineProps({
  title: { type: String, required: true }
})
</script>