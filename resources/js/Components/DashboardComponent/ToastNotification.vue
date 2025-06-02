<script setup>
import { ref, onMounted, watch, computed, onBeforeUnmount } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'info', // success, error, warning, info
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    message: {
        type: String,
        required: true
    },
    duration: {
        type: Number,
        default: 5000 // 5 seconds by default
    },
    show: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['close']);
const visible = ref(props.show);
const delayedVisible = ref(false); // New ref for delayed visibility
const progressWidth = ref(100);
let timeout = null;
let interval = null;

// Get appropriate icon based on notification type
const icon = computed(() => {
    switch (props.type) {
        case 'success':
            // Using the existing success icon
            return {
                svg: '<path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>',
                viewBox: '0 -960 960 960',
                fill: 'currentColor',
                stroke: 'none'
            };
        case 'error':
            // Using the new error icon
            return {
                svg: '<path d="M330-120 120-330v-300l210-210h300l210 210v300L630-120H330Zm36-190 114-114 114 114 56-56-114-114 114-114-56-56-114 114-114-114-56 56 114 114-114 114 56 56Zm-2 110h232l164-164v-232L596-760H364L200-596v232l164 164Zm116-280Z"/>',
                viewBox: '0 -960 960 960',
                fill: 'currentColor',
                stroke: 'none'
            };
        case 'warning':
            // Using the new warning icon
            return {
                svg: '<path d="m40-120 440-760 440 760H40Zm138-80h604L480-720 178-200Zm302-40q17 0 28.5-11.5T520-280q0-17-11.5-28.5T480-320q-17 0-28.5 11.5T440-280q0 17 11.5 28.5T480-240Zm-40-120h80v-200h-80v200Zm40-100Z"/>',
                viewBox: '0 -960 960 960',
                fill: 'currentColor',
                stroke: 'none'
            };
        case 'info':
        default:
            // Using the new info icon
            return {
                svg: '<path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>',
                viewBox: 'http://www.w3.org/2000/svg',
                fill: 'currentColor',
                stroke: 'none'
            };
    }
});

// Get appropriate icon color based on notification type
const iconColor = computed(() => {
    switch (props.type) {
        case 'success':
            return 'text-greenLabelText'; // Using greenLabelText color from tailwind config
        case 'error':
            return 'text-redLabelText'; // Using redLabelText color from tailwind config
        case 'warning':
            return 'text-yellowLabelText'; // Using yellowLabelText color from tailwind config
        case 'info':
        default:
            return 'text-blueLabelText'; // Using blueLabelText color from tailwind config
    }
});

// Get appropriate styles based on notification type
const styles = computed(() => {
    const baseStyle = 'relative flex items-center p-2 sm:p-3 md:p-3 lg:p-4 xl:p-4 2xl:p-4 3xl:p-4 4xl:p-5 mb-2 sm:mb-3 md:mb-3 lg:mb-4 xl:mb-4 2xl:mb-4 3xl:mb-4 4xl:mb-5 rounded-[7px] sm:rounded-[8px] md:rounded-[9px] lg:rounded-[10px] xl:rounded-[11px] 2xl:rounded-[12px] 3xl:rounded-[12px] 4xl:rounded-[13px] shadow-lg sm:shadow-lg md:shadow-xl lg:shadow-xl xl:shadow-xl 2xl:shadow-xl 3xl:shadow-xl 4xl:shadow-xl border';
    switch (props.type) {
        case 'success':
            return `${baseStyle} text-selectedText bg-selectedBG border-selectedText`;
        case 'error':
            return `${baseStyle} text-selectedText bg-selectedBG border-selectedText`;
        case 'warning':
            return `${baseStyle} text-selectedText bg-selectedBG border-selectedText`;
        case 'info':
        default:
            return `${baseStyle} text-selectedText bg-selectedBG border-selectedText`;
    }
});

// Get appropriate progress bar color
const progressColor = computed(() => {
    switch (props.type) {
        case 'success': return 'bg-selectedText';
        case 'error': return 'bg-selectedText';
        case 'warning': return 'bg-selectedText';
        case 'info':
        default: return 'bg-selectedText';
    }
});

// Handle close action
const closeNotification = () => {
    visible.value = false;
    clearTimers();
    emit('close');
};

// Set up auto-dismiss timer
const setupTimer = () => {
    if (props.duration > 0) {
        // Clear any existing timers
        clearTimers();

        // Set the progress bar interval
        const intervalStep = 10; // Update every 10ms
        const decrementPerStep = (intervalStep / props.duration) * 100;

        interval = setInterval(() => {
            progressWidth.value = Math.max(0, progressWidth.value - decrementPerStep);
        }, intervalStep);

        // Set the timeout to close the notification
        timeout = setTimeout(() => {
            closeNotification();
        }, props.duration);
    }
};

// Clear timers to prevent memory leaks
const clearTimers = () => {
    if (timeout) clearTimeout(timeout);
    if (interval) clearInterval(interval);
};

// Reset progress bar on mouse enter
const handleMouseEnter = () => {
    clearTimers();
    progressWidth.value = 100;
};

// Restart timer on mouse leave
const handleMouseLeave = () => {
    if (props.duration > 0) {
        setupTimer();
    }
};

// Watch for changes to the show prop
watch(() => props.show, (newVal) => {
    visible.value = newVal;
    if (newVal) {
        // Add a small delay before showing the component to ensure animation is visible
        delayedVisible.value = false;
        setTimeout(() => {
            delayedVisible.value = true;
        }, 50); // 50ms delay is usually sufficient
        setupTimer();
    } else {
        delayedVisible.value = false;
        clearTimers();
    }
});

// Set up timers when component is mounted
onMounted(() => {
    if (visible.value && props.duration > 0) {
        // Add initial delay on mount as well
        setTimeout(() => {
            delayedVisible.value = true;
        }, 50);
        setupTimer();
    }
});

// Clean up timers when component is unmounted
onBeforeUnmount(() => {
    clearTimers();
});
</script>

<template>
    <Transition 
        enter-active-class="transform transition-all duration-500 ease-out"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100" 
        leave-active-class="transform transition-all duration-300 ease-in"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
    >
        <div 
            v-if="visible && delayedVisible" 
            :class="styles" 
            role="alert" 
            @mouseenter="handleMouseEnter" 
            @mouseleave="handleMouseLeave"
            class="max-w-[85%] sm:max-w-[90%] md:max-w-[92%] lg:max-w-[93%] xl:max-w-[94%] 2xl:max-w-[95%] 3xl:max-w-[95%] 4xl:max-w-[95%]"
        >
            <!-- Icon - responsive sizing -->
            <svg 
                class="flex-shrink-0 w-4 h-4 sm:w-5 sm:h-5 md:w-5 md:h-5 lg:w-6 lg:h-6 xl:w-6 xl:h-6 2xl:w-7 2xl:h-7 3xl:w-7 3xl:h-7 4xl:w-8 4xl:h-8 mr-1 sm:mr-2 md:mr-2 lg:mr-2 xl:mr-3 2xl:mr-3 3xl:mr-3 4xl:mr-4" 
                aria-hidden="true" 
                xmlns="http://www.w3.org/2000/svg" 
                :fill="icon.fill" 
                :stroke="icon.stroke" 
                :viewBox="icon.viewBox" 
                :class="iconColor"
                v-html="icon.svg"
            ></svg>
            
            <!-- Text - responsive sizing -->
            <div class="ms-1 sm:ms-2 md:ms-2 lg:ms-2 xl:ms-2 2xl:ms-3 3xl:ms-3 4xl:ms-3 text-xs sm:text-sm md:text-sm lg:text-sm xl:text-sm 2xl:text-base 3xl:text-base 4xl:text-base font-medium">
                {{ message }}
            </div>
            
            <!-- Close button - responsive sizing -->
            <button 
                type="button"
                class="ms-auto -mx-0.5 sm:-mx-1 md:-mx-1 lg:-mx-1 xl:-mx-1 2xl:-mx-1.5 3xl:-mx-1.5 4xl:-mx-1.5 -my-0.5 sm:-my-1 md:-my-1 lg:-my-1 xl:-my-1 2xl:-my-1.5 3xl:-my-1.5 4xl:-my-1.5 rounded-lg focus:ring-2 p-1 sm:p-1 md:p-1 lg:p-1.5 xl:p-1.5 2xl:p-1.5 3xl:p-2 4xl:p-2 inline-flex items-center justify-center h-6 w-6 sm:h-7 sm:w-7 md:h-7 md:w-7 lg:h-8 lg:w-8 xl:h-8 xl:w-8 2xl:h-9 2xl:w-9 3xl:h-9 3xl:w-9 4xl:h-10 4xl:w-10 hover:bg-gray-100"
                @click="closeNotification" 
                aria-label="Close"
            >
                <span class="sr-only">Close</span>
                <svg 
                    class="w-2.5 h-2.5 sm:w-3 sm:h-3 md:w-3 md:h-3 lg:w-3 lg:h-3 xl:w-3.5 xl:h-3.5 2xl:w-3.5 2xl:h-3.5 3xl:w-4 3xl:h-4 4xl:w-4 4xl:h-4" 
                    aria-hidden="true" 
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none"
                    viewBox="0 0 14 14"
                >
                    <path 
                        stroke="currentColor" 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" 
                    />
                </svg>
            </button>
            
            <!-- Progress bar -->
            <div class="absolute bottom-0 mx-[2px] sm:mx-[2px] md:mx-[2px] lg:mx-[2.5px] xl:mx-[2.5px] 2xl:mx-[3px] 3xl:mx-[3px] 4xl:mx-[3px] left-0 right-0 h-[2px] sm:h-[2.5px] md:h-[2.5px] lg:h-[3px] xl:h-[3px] 2xl:h-[3.5px] 3xl:h-[3.5px] 4xl:h-[4px] overflow-hidden rounded-b-[7px] sm:rounded-b-[8px] md:rounded-b-[9px] lg:rounded-b-[10px] xl:rounded-b-[11px] 2xl:rounded-b-[12px] 3xl:rounded-b-[12px] 4xl:rounded-b-[13px]">
                <div 
                    :class="progressColor" 
                    :style="{ width: `${progressWidth}%` }"
                    class="h-full transition-all duration-100"
                ></div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Ensure animations work properly with more specific styling */
.translate-x-full {
    transform: translateX(100%);
}
.translate-x-0 {
    transform: translateX(0);
}

/* Add a slight delay to make animation more noticeable */
.transition-all {
    transition-property: all;
}
</style>
