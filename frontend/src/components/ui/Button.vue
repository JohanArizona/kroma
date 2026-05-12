<template>
  <button 
    :class="[
      'inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors disabled:opacity-50',
      variantClasses,
      $attrs.class
    ]"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary' // primary, outline
  }
})

const variantClasses = computed(() => {
  if (props.variant === 'outline') {
    return 'border border-gray-200 text-gray-700 hover:bg-gray-50 bg-transparent'
  }
  return 'bg-[#7C3AED] hover:bg-[#6D28D9] text-white'
})
</script>