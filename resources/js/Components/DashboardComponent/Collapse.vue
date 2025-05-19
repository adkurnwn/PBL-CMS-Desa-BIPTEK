<script setup>
// Simple component for the collapse button
const props = defineProps({
  collapsed: {
    type: Boolean,
    default: false
  }
});

// Emit click event to parent
const emit = defineEmits(['click']);

const handleClick = () => {
  emit('click');
};
</script>

<template>
  <button 
    @click="handleClick"
    class="h-12 w-[29px] flex items-center justify-center bg-menuBG hover:bg-selectedBG 
           text-selectedText hover:text-selectedText rounded-[5px] cursor-pointer transition-colors duration-200" 
    type="button" 
    aria-label="Toggle sidebar"
  >
    <svg 
      xmlns="http://www.w3.org/2000/svg" 
      width="10" 
      height="20" 
      viewBox="0 0 10 20" 
      fill="none"
      class="transform transition-transform duration-200"
      :class="{ 'rotate-180': collapsed }"
    >
      <path d="M5.55556 15.5556V4.44444L0 10L5.55556 15.5556ZM7.77778 20H10V0H7.77778V20Z" fill="currentColor"/>
    </svg>
  </button>
</template>


