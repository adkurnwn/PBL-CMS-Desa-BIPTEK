<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Search...'
    },
    modelValue: {
        type: String,
        default: ''
    },
    debounce: {
        type: Number,
        default: 300
    }
});

const emit = defineEmits(['update:modelValue', 'search']);

const searchQuery = ref(props.modelValue);
let debounceTimeout = null;

watch(searchQuery, (newValue) => {
    // Clear previous timeout
    if (debounceTimeout) clearTimeout(debounceTimeout);

    // Set new timeout
    debounceTimeout = setTimeout(() => {
        emit('update:modelValue', newValue);
        emit('search', newValue);
    }, props.debounce);
});

watch(() => props.modelValue, (newValue) => {
    searchQuery.value = newValue;
});
</script>

<template>
    <div class="search-container" style="width: 450px; height: 44px;">
        <div class="relative flex items-center w-full h-full gap-[10px]">
            <!-- Search icon -->
            <div class="absolute left-4 text-searchIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M19.6 21L13.3 14.7C12.8 15.1 12.225 15.4167 11.575 15.65C10.925 15.8833 10.2333 16 9.5 16C7.68333 16 6.14583 15.3708 4.8875 14.1125C3.62917 12.8542 3 11.3167 3 9.5C3 7.68333 3.62917 6.14583 4.8875 4.8875C6.14583 3.62917 7.68333 3 9.5 3C11.3167 3 12.8542 3.62917 14.1125 4.8875C15.3708 6.14583 16 7.68333 16 9.5C16 10.2333 15.8833 10.925 15.65 11.575C15.4167 12.225 15.1 12.8 14.7 13.3L21 19.6L19.6 21ZM9.5 14C10.75 14 11.8125 13.5625 12.6875 12.6875C13.5625 11.8125 14 10.75 14 9.5C14 8.25 13.5625 7.1875 12.6875 6.3125C11.8125 5.4375 10.75 5 9.5 5C8.25 5 7.1875 5.4375 6.3125 6.3125C5.4375 7.1875 5 8.25 5 9.5C5 10.75 5.4375 11.8125 6.3125 12.6875C7.1875 13.5625 8.25 14 9.5 14Z"
                        fill="#A0AEC0" />
                </svg>
            </div>

            <!-- Search input with updated styling and proper padding for clear button -->
            <input 
                type="text" 
                v-model="searchQuery" 
                :placeholder="placeholder"
                class="w-full h-full pl-12 pr-10 py-2 rounded-[15px] bg-transparent text-TableText border border-searchBorder focus:outline-none focus:ring-1 focus:ring-selectedText focus:border-selectedText transition-all duration-200 placeholder:text-searchIcon text-ellipsis" 
            />

            <!-- Clear button with z-index to ensure it stays on top -->
            <div 
                v-if="searchQuery" 
                @click="searchQuery = ''"
                class="absolute right-4 cursor-pointer text-searchIcon hover:text-gray-600 z-10 bg-white bg-opacity-50"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>
        </div>
    </div>
</template>


