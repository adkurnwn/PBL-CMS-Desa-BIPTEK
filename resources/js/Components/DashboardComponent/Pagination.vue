<script setup>
import { computed } from 'vue';

const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    totalPages: {
        type: Number,
        required: true,
    },
    maxVisibleButtons: {
        type: Number,
        default: 3,
    },
});

const emit = defineEmits(['page-changed']);

// Calculate the range of pages to display
const pages = computed(() => {
    if (props.totalPages <= props.maxVisibleButtons) {
        return Array.from({ length: props.totalPages }, (_, i) => i + 1);
    }

    const halfVisibleButtons = Math.floor(props.maxVisibleButtons / 2);
    
    // Start page calculation
    let startPage = props.currentPage - halfVisibleButtons;
    if (startPage < 1) {
        startPage = 1;
    }
    
    // End page calculation
    let endPage = startPage + props.maxVisibleButtons - 1;
    if (endPage > props.totalPages) {
        endPage = props.totalPages;
        startPage = Math.max(1, endPage - props.maxVisibleButtons + 1);
    }

    return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
});

const changePage = (page) => {
    if (page === props.currentPage || page < 1 || page > props.totalPages) {
        return;
    }
    emit('page-changed', page);
};

const isFirstPage = computed(() => props.currentPage === 1);
const isLastPage = computed(() => props.currentPage === props.totalPages);
</script>

<template>
    <div class="pagination-container">
        <div class="pagination">
            <!-- Previous button -->
            <button 
                class="pagination-button" 
                :disabled="isFirstPage"
                @click="changePage(currentPage - 1)"
                :class="{ 'disabled': isFirstPage }"
            >
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            
            <!-- First page button if not visible in range -->
            <button 
                v-if="!pages.includes(1)" 
                class="pagination-button"
                @click="changePage(1)"
            >
                1
            </button>
            
            <!-- Ellipsis if needed -->
            <span v-if="!pages.includes(1) && !pages.includes(2)" class="pagination-ellipsis">...</span>
            
            <!-- Page numbers -->
            <button 
                v-for="page in pages" 
                :key="page"
                class="pagination-button"
                :class="{ 'active': page === currentPage }"
                @click="changePage(page)"
            >
                {{ page }}
            </button>
            
            <!-- Ellipsis if needed -->
            <span v-if="!pages.includes(totalPages - 1) && !pages.includes(totalPages)" class="pagination-ellipsis">...</span>
            
            <!-- Last page button if not visible in range -->
            <button 
                v-if="totalPages > 1 && !pages.includes(totalPages)" 
                class="pagination-button"
                @click="changePage(totalPages)"
            >
                {{ totalPages }}
            </button>
            
            <!-- Next button -->
            <button 
                class="pagination-button" 
                :disabled="isLastPage"
                @click="changePage(currentPage + 1)"
                :class="{ 'disabled': isLastPage }"
            >
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<style>
.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 98px;
    width: 100%;
}

.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 407px;
    height: 100%;
    gap: 8px;
}

.pagination-button {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    border-radius: 8px;
    background-color: white;
    color: #6B7280;
    font-size: 16px;
    font-weight: 500;
    border: 1px solid #E5E7EB;
    cursor: pointer;
    transition: all 0.2s;
}

.pagination-button:hover:not(.disabled):not(.active) {
    background-color: #F9FAFB;
    border-color: #D1D5DB;
}

.pagination-button.active {
    background-color: #1C64F2;
    color: white;
    border-color: #1C64F2;
}

.pagination-button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-ellipsis {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    color: #6B7280;
}
</style>
