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
        </div>

        <!-- Manual page input with spacing - moved below on small screens -->
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
    height: auto; /* Changed from fixed height to auto for responsiveness */
    max-width: 407px; /* Changed from fixed width to max-width */
    width: 100%; /* Make it fill available space */
    padding-top: 12px;
    padding-bottom: 12px;
    margin: 0 auto;
    gap: 10px;
}

.pagination-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    width: 100%;
    flex-wrap: wrap; /* Allow wrapping on small screens */
}

.pagination-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 25px;
    height: 25px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid #E5E7EB;
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
    width: 25px;
    height: 25px;
    color: #6B7280;
    font-size: 14px;
}

.manual-page-input-container {
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 5px 0; /* Add vertical margin */
}

.manual-page-input {
    width: 25px;
    height: 25px;
    border-radius: 4px;
    border: 1px solid #E5E7EB;
    text-align: center;
    font-size: 14px;
    color: #6B7280;
    padding: 0;
}

.manual-page-input:focus {
    outline: none;
    border-color: #1C64F2;
}

.go-button {
    height: 25px;
    padding: 0 08px;
    border-radius: 4px;
    background-color: white;
    color: #6B7280;
    font-size: 11px;
    font-weight: 500;
    border: 1px solid #E5E7EB;
    cursor: pointer;
    transition: all 0.2s;
}

.go-button:hover {
    background-color: theme('colors.selectedBG');
    color: theme('colors.selectedText');
}

.items-count {
    font-size: 12px;
    color: #6B7280;
    text-align: center;
    width: 100%;
}

/* Add important flag to ensure these styles take precedence */
.pagination-button.active {
    background-color: theme('colors.selectedBG') !important;
    color: theme('colors.selectedText') !important;
    border-color: theme('colors.selectedText') !important;
}

/* Add hover styles that don't apply to active or disabled buttons */
.pagination-button:not(.active):not(.disabled):hover {
    background-color: theme('colors.selectedBG');
    color: theme('colors.selectedText');
    border-color: theme('colors.selectedText');
}

/* Media query for small screens */
@media (max-width: 480px) {
    .first-last-button {
        display: none; /* Hide first/last buttons on small screens */
    }
    
    .manual-page-input-container {
        margin-left: 0; /* Remove left margin on small screens */
        width: 100%;
        justify-content: center;
    }
    
    .pagination-container {
        min-height: 120px; /* Ensure enough height for stacked elements */
    }
}
</style>
