<script setup>
import { computed, ref } from 'vue';

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
        default: 5, // Changed to 5 to show 3 first pages + ellipsis + 2 last pages when appropriate
    },
    totalItems: {
        type: Number,
        default: 0
    },
    itemsPerPage: {
        type: Number,
        default: 25
    }
});

const emit = defineEmits(['page-changed']);

// Calculate the range of pages to display
const pages = computed(() => {
    // For small number of pages, show all
    if (props.totalPages <= props.maxVisibleButtons) {
        return Array.from({ length: props.totalPages }, (_, i) => i + 1);
    }

    // For larger number of pages, show 3 at the beginning, ellipsis, and 2 at the end
    if (props.currentPage <= 3) {
        // Near the beginning
        return [1, 2, 3, 4, props.totalPages];
    } else if (props.currentPage >= props.totalPages - 2) {
        // Near the end
        return [1, props.totalPages - 3, props.totalPages - 2, props.totalPages - 1, props.totalPages];
    } else {
        // In the middle
        return [1, props.currentPage - 1, props.currentPage, props.currentPage + 1, props.totalPages];
    }
});

const changePage = (page) => {
    if (page === props.currentPage || page < 1 || page > props.totalPages) {
        return;
    }
    emit('page-changed', page);
};

const isFirstPage = computed(() => props.currentPage === 1);
const isLastPage = computed(() => props.currentPage === props.totalPages);

// Add function to check if we need ellipsis before or after
const showLeftEllipsis = computed(() => {
    return props.currentPage > 3;
});

const showRightEllipsis = computed(() => {
    return props.currentPage < props.totalPages - 2 && props.totalPages > 5;
});

// Add state for manual page input
const manualPage = ref('');

// Add function to handle manual page navigation
const goToManualPage = () => {
    const page = parseInt(manualPage.value);
    if (!isNaN(page) && page >= 1 && page <= props.totalPages) {
        changePage(page);
    }
    manualPage.value = '';
};

// Calculate current items range
const currentItemsRange = computed(() => {
    const start = (props.currentPage - 1) * props.itemsPerPage + 1;
    const end = Math.min(props.currentPage * props.itemsPerPage, props.totalItems);
    return {
        start: props.totalItems > 0 ? start : 0,
        end: props.totalItems > 0 ? end : 0
    };
});
</script>

<template>
    <div class="pagination-container">
        <!-- Pagination controls container -->
        <div class="pagination-controls">
            <!-- First page button with TopTableBG - hidden on small screens -->
            <button 
                class="pagination-button navigation-button first-last-button"
                :disabled="isFirstPage"
                @click="changePage(1)"
                :class="{ 'disabled': isFirstPage }"
                title="First page"
            >
                &laquo;
            </button>

            <!-- Previous button with TopTableBG -->
            <button 
                class="pagination-button navigation-button"
                :disabled="isFirstPage"
                @click="changePage(currentPage - 1)"
                :class="{ 'disabled': isFirstPage }"
                title="Previous page"
            >
                &lsaquo;
            </button>
            
            <!-- Page numbers with menuBG for inactive -->
            <template v-for="page in pages" :key="page">
                <!-- Left ellipsis if needed -->
                <span v-if="page === pages[1] && showLeftEllipsis" class="pagination-ellipsis">...</span>
                
                <!-- Page number button with menuBG when inactive -->
                <button 
                    class="pagination-button page-number-button"
                    :class="{ 
                        'active': page === currentPage,
                        'bg-selectedBG !text-selectedText !border-selectedText': page === currentPage
                    }"
                    @click="changePage(page)"
                >
                    {{ page }}
                </button>
                
                <!-- Right ellipsis if needed -->
                <span v-if="page === pages[pages.length - 2] && showRightEllipsis" class="pagination-ellipsis">...</span>
            </template>
            
            <!-- Next button with TopTableBG -->
            <button 
                class="pagination-button navigation-button"
                :disabled="isLastPage"
                @click="changePage(currentPage + 1)"
                :class="{ 'disabled': isLastPage }"
                title="Next page"
            >
                &rsaquo;
            </button>

            <!-- Last page button with TopTableBG - hidden on small screens -->
            <button 
                class="pagination-button navigation-button first-last-button"
                :disabled="isLastPage"
                @click="changePage(totalPages)"
                :class="{ 'disabled': isLastPage }"
                title="Last page"
            >
                &raquo;
            </button>
            
            <!-- Manual page input with spacing - moved inline with other buttons -->
            <div class="manual-page-input-container">
                <input
                    type="text"
                    class="manual-page-input"
                    v-model="manualPage"
                    placeholder="#"
                    @keyup.enter="goToManualPage"
                    title="Go to page"
                    aria-label="Go to page"
                />
                
                <button
                    class="go-button hover:bg-selectedBG hover:text-selectedText hover:border-selectedText"
                    @click="goToManualPage"
                    title="Go to page"
                >
                    <span class="inline-flex items-center">GO <span class="ml-1">&gt;</span></span>
                </button>
            </div>
        </div>
        
        <!-- Items count display with corrected calculation -->
        <div class="items-count" v-if="totalItems > 0">
            Menampilkan item {{ currentItemsRange.start }}-{{ currentItemsRange.end }} dari {{ totalItems }}
        </div>
    </div>
</template>

<style>
.pagination-container {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    height: auto;
    max-width: 407px;
    width: 100%;
    padding-top: 5.33px;
    padding-bottom: 5.33px;
    margin: 0 auto;
    gap: 6.67px;
}

.pagination-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3.33px;
    width: 100%;
    flex-wrap: wrap;
}

.pagination-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 16.67px;
    height: 16.67px;
    border-radius: 2.67px;
    font-size: 9.33px;
    font-weight: 500;
    border: 0.67px solid #E5E7EB;
    cursor: pointer;
    transition: all 0.2s;
    padding: 0;
    margin: 0;
    color: theme('colors.TableText');
}

/* Navigation buttons (first, prev, next, last) */
.navigation-button {
    background-color: theme('colors.TopTableBG');
}

/* Number buttons */
.page-number-button {
    background-color: theme('colors.menuBG');
}

.pagination-button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-ellipsis {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 16.67px;
    height: 16.67px;
    color: #6B7280;
    font-size: 9.33px;
}

.manual-page-input-container {
    display: flex;
    align-items: center;
    gap: 3.33px;
    margin-left: 3.33px; /* Add margin to separate from pagination buttons */
}

.manual-page-input {
    width: 16.67px;
    height: 16.67px;
    border-radius: 2.67px;
    border: 0.67px solid #E5E7EB;
    text-align: center;
    font-size: 9.33px;
    color: #6B7280;
    padding: 0;
}

.manual-page-input:focus {
    outline: none;
    border-color: #1C64F2;
}

.go-button {
    height: 16.67px;
    padding: 0 5.33px;
    border-radius: 2.67px;
    background-color: white;
    color: #6B7280;
    font-size: 7.33px;
    font-weight: 500;
    border: 0.67px solid #E5E7EB;
    cursor: pointer;
    transition: all 0.2s;
}

.go-button:hover {
    background-color: theme('colors.selectedBG');
    color: theme('colors.selectedText');
}

.items-count {
    font-size: 8px;
    color: #6B7280;
    text-align: center;
    width: 100%;
}

/* Add responsive sizes using media queries */
@media (min-width: 640px) { /* sm */
    .pagination-button {
        width: 18px;
        height: 18px;
        font-size: 10px;
        border-radius: 3px;
    }
    .pagination-ellipsis {
        width: 18px;
        height: 18px;
        font-size: 10px;
    }
    .manual-page-input {
        width: 18px;
        height: 18px;
        font-size: 10px;
    }
    .go-button {
        height: 18px;
        font-size: 8px;
    }
    .items-count {
        font-size: 9px;
    }
}

@media (min-width: 768px) { /* md */
    .pagination-button {
        width: 20px;
        height: 20px;
        font-size: 11px;
        border-radius: 3px;
    }
    .pagination-ellipsis {
        width: 20px;
        height: 20px;
        font-size: 11px;
    }
    .manual-page-input {
        width: 20px;
        height: 20px;
        font-size: 11px;
    }
    .go-button {
        height: 20px;
        font-size: 9px;
    }
    .items-count {
        font-size: 10px;
    }
}

@media (min-width: 1024px) { /* lg */
    .pagination-button {
        width: 22px;
        height: 22px;
        font-size: 12px;
        border-radius: 3.5px;
    }
    .pagination-ellipsis {
        width: 22px;
        height: 22px;
        font-size: 12px;
    }
    .manual-page-input {
        width: 22px;
        height: 22px;
        font-size: 12px;
    }
    .go-button {
        height: 22px;
        font-size: 10px;
    }
    .items-count {
        font-size: 11px;
    }
}

@media (min-width: 1280px) { /* xl */
    .pagination-container {
        padding-top: 8px;
        padding-bottom: 8px;
        gap: 6.67px;
    }
    .pagination-controls {
        gap: 3.33px;
    }
    .pagination-button {
        width: 16.67px;
        height: 16.67px;
        font-size: 9.33px;
        border-radius: 2.67px;
        border-width: 0.67px;
    }
    .pagination-ellipsis {
        width: 16.67px;
        height: 16.67px;
        font-size: 9.33px;
    }
    .manual-page-input {
        width: 16.67px;
        height: 16.67px;
        font-size: 9.33px;
        border-radius: 2.67px;
        border-width: 0.67px;
    }
    .manual-page-input-container {
        gap: 3.33px;
    }
    .go-button {
        height: 16.67px;
        font-size: 7.33px;
        padding: 0 5.33px;
        border-radius: 2.67px;
        border-width: 0.67px;
    }
    .items-count {
        font-size: 8px;
    }
}

@media (min-width: 1536px) { /* 2xl */
    .pagination-button {
        width: 20px;
        height: 20px;
        font-size: 11px;
        border-radius: 3px;
    }
    .pagination-ellipsis {
        width: 20px;
        height: 20px;
        font-size: 11px;
    }
    .manual-page-input {
        width: 20px;
        height: 20px;
        font-size: 11px;
    }
    .go-button {
        height: 20px;
        font-size: 9px;
    }
    .items-count {
        font-size: 10px;
    }
}

@media (min-width: 1920px) { /* 3xl */
    .pagination-button {
        width: 22px;
        height: 22px;
        font-size: 12px;
        border-radius: 3.5px;
    }
    .pagination-ellipsis {
        width: 22px;
        height: 22px;
        font-size: 12px;
    }
    .manual-page-input {
        width: 22px;
        height: 22px;
        font-size: 12px;
    }
    .go-button {
        height: 22px;
        font-size: 10px;
    }
    .items-count {
        font-size: 11px;
    }
}

@media (min-width: 2560px) { /* 4xl */
    .pagination-button {
        width: 25px;
        height: 25px;
        font-size: 14px;
        border-radius: 4px;
        border-width: 1px;
    }
    .pagination-ellipsis {
        width: 25px;
        height: 25px;
        font-size: 14px;
    }
    .manual-page-input {
        width: 25px;
        height: 25px;
        font-size: 14px;
        border-radius: 4px;
        border-width: 1px;
    }
    .go-button {
        height: 25px;
        font-size: 11px;
        padding: 0 8px;
        border-radius: 4px;
        border-width: 1px;
    }
    .items-count {
        font-size: 12px;
    }
    .pagination-container {
        padding-top: 12px;
        padding-bottom: 12px;
        gap: 10px;
    }
    .pagination-controls, .manual-page-input-container {
        gap: 5px;
    }
}

/* Media query adjustments */
@media (max-width: 480px) {
    .first-last-button {
        display: none; /* Hide first/last buttons on small screens */
    }
    
    .pagination-controls {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .manual-page-input-container {
        margin-top: 3.33px; /* Add margin top for small screens */
        margin-left: 0;     /* Reset left margin for small screens */
    }
}
</style>
