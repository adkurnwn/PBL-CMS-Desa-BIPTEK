<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  height: {
    type: String,
    default: '100%'
  },
  width: {
    type: String,
    default: '100%'
  },
  thumbColor: {
    type: String,
    default: 'rgba(156, 163, 175, 0.5)' // Gray-400 with transparency
  },
  thumbHoverColor: {
    type: String,
    default: 'rgba(107, 114, 128, 0.7)' // Gray-500 with transparency
  },
  trackColor: {
    type: String,
    default: 'transparent'
  },
  autoHide: {
    type: Boolean,
    default: true
  },
  hideScrollbar: {
    type: Boolean,
    default: false
  },
  scrollbarWidth: {
    type: String,
    default: '8px'
  }
});

const scrollContainer = ref(null);
const isScrolling = ref(false);
const scrollTimeout = ref(null);

const handleScroll = () => {
  if (props.autoHide) {
    isScrolling.value = true;
    
    if (scrollTimeout.value) {
      clearTimeout(scrollTimeout.value);
    }
    
    scrollTimeout.value = setTimeout(() => {
      isScrolling.value = false;
    }, 1000);
  }
};

onMounted(() => {
  if (scrollContainer.value) {
    scrollContainer.value.addEventListener('scroll', handleScroll);
  }
});

onUnmounted(() => {
  if (scrollContainer.value) {
    scrollContainer.value.removeEventListener('scroll', handleScroll);
  }
  
  if (scrollTimeout.value) {
    clearTimeout(scrollTimeout.value);
  }
});
</script>

<template>
  <div 
    ref="scrollContainer"
    class="custom-scrollbar"
    :class="{ 'auto-hide': autoHide, 'is-scrolling': isScrolling }"
    :style="{ 
      height, 
      width,
      '--thumb-color': thumbColor, 
      '--thumb-hover-color': thumbHoverColor,
      '--track-color': trackColor,
      '--scrollbar-width': scrollbarWidth,
      '--scrollbar-display': hideScrollbar ? 'none' : 'block' 
    }"
  >
    <slot></slot>
  </div>
</template>

<style scoped>
.custom-scrollbar {
  overflow-y: auto;
  overflow-x: hidden;
  position: relative;
  scrollbar-width: thin; /* Firefox */
  scrollbar-color: var(--thumb-color) var(--track-color); /* Firefox */
}

.custom-scrollbar::-webkit-scrollbar {
  width: var(--scrollbar-width);
  display: var(--scrollbar-display);
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: var(--track-color);
  border-radius: 20px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: var(--thumb-color);
  border-radius: 20px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: var(--thumb-hover-color);
}

/* Auto-hide scrollbar until user interacts */
.custom-scrollbar.auto-hide::-webkit-scrollbar-thumb {
  background-color: transparent;
}

.custom-scrollbar.auto-hide.is-scrolling::-webkit-scrollbar-thumb,
.custom-scrollbar.auto-hide:hover::-webkit-scrollbar-thumb {
  background-color: var(--thumb-color);
}

/* Fix for Firefox to ensure scrollbar is visible when needed */
@supports (scrollbar-width: thin) {
  .custom-scrollbar.auto-hide {
    scrollbar-width: var(--scrollbar-display);
  }
  
  .custom-scrollbar.auto-hide:hover,
  .custom-scrollbar.auto-hide.is-scrolling {
    scrollbar-width: thin;
  }
}
</style>
