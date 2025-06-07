<template>
    <div class="flex justify-center mt-8 gap-2 mb-10">
        <button
            class="w-8 h-8 rounded-full flex items-center justify-center bg-accent2 text-type1 text-xs"
            :disabled="currentPage === 1"
            @click="goToPreviousPage"
        >
            <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                <path d="m639-80 71-71-329-329 329-329-71-71-400 400L639-80Z"/>
            </svg>
        </button>
        
        <button
            v-for="p in totalPages"
            :key="p"
            class="w-8 h-8 rounded-full flex items-center justify-center text-xs"
            :class="currentPage === p ? 'bg-accent2 text-type1' : 'border border-accent2 text-accent2 bg-type1'"
            @click="goToPage(p)"
        >{{ p }}</button>
        <button
            class="w-8 h-8 rounded-full flex items-center justify-center bg-accent2 text-type1 text-xs"
            :disabled="currentPage === totalPages"
            @click="goToNextPage"
        >
            <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
            </svg>
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    currentPage: {
        type: Number,
        required: true
    },
    lastPage: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['page-changed']);

const totalPages = computed(() => {
    if (!props.lastPage) return [];
    return Array.from({ length: props.lastPage }, (_, i) => i + 1);
});

const goToPage = (page) => {
    if (page !== props.currentPage) {
        emit('page-changed', page);
    }
};

const goToPreviousPage = () => {
    if (props.currentPage > 1) {
        emit('page-changed', props.currentPage - 1);
    }
};

const goToNextPage = () => {
    if (props.currentPage < props.lastPage) {
        emit('page-changed', props.currentPage + 1);
    }
};
</script>
