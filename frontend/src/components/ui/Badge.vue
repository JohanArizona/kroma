<template>
  <span 
    :class="[
      'inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold transition-colors',
      variantClasses,
      $attrs.class
    ]"
  >
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'default' // default, outline, purple
  }
})

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'outline':
      return 'border border-gray-200 text-gray-600 bg-transparent'
    case 'purple':
      return 'bg-[#7C3AED]/10 text-[#7C3AED] border-0'
    default:
      return 'bg-[#7C3AED] text-white border-0'
  }
})
</script>