<script setup>
import { ref, computed } from "vue";
import Pagination from "@/Components/DashboardComponent/Pagination.vue";

const props = defineProps({
    headers: {
        type: Array,
        required: true,
        // Expected format: [{ 
        //   label: 'Pengguna',           // Column label (also used as identifier)
        //   field: 'user',               // Data field name in items (optional, defaults to lowercase label)
        //   sortable: true,
        //   nowrap: true,
        //   cellClass: 'text-blue-500',  // CSS classes for cell
        //   headerClass: 'font-bold',    // CSS classes for header
        //   style: {                     // Inline style object for cell
        //     fontSize: '18px',
        //     fontWeight: 400,
        //     fontStyle: 'normal',
        //     lineHeight: 'normal'
        //   },
        //   headerStyle: {},             // Inline style object for header
        //   avatar: {                    // Optional avatar configuration
        //     field: 'avatarUrl',        // Field containing avatar URL
        //     size: 24,                  // Size in pixels
        //     gap: 12,                   // Gap between avatar and text
        //     fallback: 'initials'       // What to show if no image: 'initials' or fallbackUrl
        //   },
        //   width: '200px',              // Column width (CSS value: px, %, fr, etc.)
        //   minWidth: '150px',           // Minimum column width
        //   maxWidth: '300px'            // Maximum column width
        // }]
    },
    items: {
        type: Array,
        required: true,
    },
    emptyStateMessage: {
        type: String,
        default: "No data found."
    },
    // Add pagination props
    pagination: {
        type: Boolean,
        default: true
    },
    itemsPerPage: {
        type: Number,
        default: 25  // Updated default to 25 items per page
    }
});

// Helper to get field name for a header (fallback to lowercase label if not provided)
const getFieldName = (header) => {
    return header.field || header.label.toLowerCase();
};

// Add sort state tracking
const sortColumn = ref(null);
const sortDirection = ref('asc');

// Function to handle sorting
const sortBy = (column) => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column;
        sortDirection.value = 'asc';
    }
    
    // Here you would typically sort the data
    // For now we'll just track the state for the UI
};

// Helper to determine icon rotation class
const getSortIconClass = (column) => {
    if (sortColumn.value !== column) return '';
    return sortDirection.value === 'desc' ? 'rotate-180' : '';
};

// Convert style object to inline style string
const styleObjectToString = (styleObj) => {
    if (!styleObj) return '';
    return Object.entries(styleObj)
        .map(([key, value]) => {
            // Convert camelCase to kebab-case
            const kebabKey = key.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();
            return `${kebabKey}: ${value}`;
        })
        .join('; ');
};

// Compute sorted items
const sortedItems = computed(() => {
    if (!sortColumn.value) return props.items;
    
    return [...props.items].sort((a, b) => {
        const sortHeader = props.headers.find(h => h.label === sortColumn.value);
        if (!sortHeader) return 0;
        
        const fieldName = getFieldName(sortHeader);
        
        const aValue = a[fieldName];
        const bValue = b[fieldName];
        
        // Handle null or undefined values
        if (aValue === null || aValue === undefined) return 1;
        if (bValue === null || bValue === undefined) return -1;
        
        // String comparison
        if (typeof aValue === 'string' && typeof bValue === 'string') {
            return sortDirection.value === 'asc' 
                ? aValue.localeCompare(bValue)
                : bValue.localeCompare(aValue);
        }
        
        // Number comparison
        return sortDirection.value === 'asc' 
            ? aValue - bValue
            : bValue - aValue;
    });
});

// Pagination logic
const currentPage = ref(1);
const totalPages = computed(() => {
    return Math.max(1, Math.ceil(props.items.length / props.itemsPerPage));
});

const paginatedItems = computed(() => {
    if (!props.pagination) return sortedItems.value;
    
    const startIndex = (currentPage.value - 1) * props.itemsPerPage;
    const endIndex = startIndex + props.itemsPerPage;
    return sortedItems.value.slice(startIndex, endIndex);
});

const handlePageChanged = (page) => {
    currentPage.value = page;
};

// Function to get initials from a name
const getInitials = (name) => {
    if (!name) return '';
    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

// Function to get column width style
const getColumnWidthStyle = (header) => {
    const styles = {};
    
    if (header.width) {
        styles.width = header.width;
    }
    
    if (header.minWidth) {
        styles.minWidth = header.minWidth;
    }
    
    if (header.maxWidth) {
        styles.maxWidth = header.maxWidth;
    }
    
    return styleObjectToString(styles);
};
</script>

<template>
    <div class="table-wrapper">
        <!-- Empty state -->
        <div v-if="items.length === 0" class="text-gray-500 text-center py-4">
            {{ emptyStateMessage }}
        </div>

        <div v-else class="flex flex-col h-full">
            <!-- Fixed header -->
            <div class="sticky-header">
                <table class="min-w-full table-fixed">
                    <colgroup>
                        <col 
                            v-for="(header, index) in headers" 
                            :key="`col-header-${index}`"
                            :style="getColumnWidthStyle(header)"
                        />
                    </colgroup>
                    <thead class="bg-TopTableBG">
                        <tr class="h-[63px]">
                            <th
                                v-for="(header, index) in headers"
                                :key="`header-${index}`"
                                scope="col"
                                class="px-6 text-left text-[22px] font-medium text-gray-900 tracking-normal"
                                :class="
                                    [
                                        { 'cursor-pointer': header.sortable },
                                        header.headerClass
                                    ]"
                                :style="styleObjectToString(header.headerStyle)"
                                @click="header.sortable ? sortBy(header.label) : null"
                            >
                                <div class="flex items-center h-full">
                                    <span>{{ header.label }}</span>
                                    <svg 
                                        v-if="header.sortable"
                                        xmlns="http://www.w3.org/2000/svg" 
                                        width="24" 
                                        height="24" 
                                        viewBox="0 0 24 25" 
                                        fill="none"
                                        class="ml-2 transform transition-transform duration-200"
                                        :class="getSortIconClass(header.label)"
                                    >
                                        <mask id="mask0_1249_5047" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="25">
                                            <rect y="0.5" width="24" height="24" fill="#D9D9D9"/>
                                        </mask>
                                        <g mask="url(#mask0_1249_5047)">
                                            <path d="M7 14.5L12 9.5L17 14.5H7Z" fill="#111827"/>
                                        </g>
                                    </svg>
                                </div>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
            
            <!-- Scrollable content area - with height constraint -->
            <div class="table-content">
                <table class="min-w-full divide-y divide-gray-200 table-fixed">
                    <colgroup>
                        <col 
                            v-for="(header, index) in headers" 
                            :key="`col-${index}`"
                            :style="getColumnWidthStyle(header)"
                        />
                    </colgroup>
                    <tbody class="bg-white divide-y divide-gray-200 text-TableText">
                        <tr
                            v-for="(item, rowIndex) in paginatedItems"
                            :key="`row-${rowIndex}`"
                            :class="{ 'bg-menuBG': rowIndex % 2 !== 0, 'bg-white': rowIndex % 2 === 0 }"
                            class="h-[107px] text-[18px]"
                        >
                            <td 
                                v-for="(header, colIndex) in headers" 
                                :key="`cell-${rowIndex}-${colIndex}`"
                                class="px-6 py-4 text-TableText"
                                :class="
                                    [
                                        { 'whitespace-nowrap': header.nowrap },
                                        header.cellClass
                                    ]"
                                :style="styleObjectToString(header.style)"
                            >
                                <slot :name="`item.${header.label}`" :item="item" :header="header">
                                    <div v-if="header.avatar" class="flex items-center">
                                        <div 
                                            :style="{
                                                width: `${header.avatar.size || 24}px`,
                                                height: `${header.avatar.size || 24}px`,
                                                marginRight: `${header.avatar.gap || 12}px`
                                            }"
                                            class="rounded-full overflow-hidden flex-shrink-0 bg-gray-200 flex items-center justify-center"
                                        >
                                            <img 
                                                v-if="item[header.avatar.field]" 
                                                :src="item[header.avatar.field]" 
                                                :alt="`${item[getFieldName(header)]} avatar`"
                                                class="w-full h-full object-cover"
                                            />
                                            <span 
                                                v-else-if="header.avatar.fallback === 'initials'"
                                                class="text-xs font-medium text-gray-600"
                                            >
                                                {{ getInitials(item[getFieldName(header)]) }}
                                            </span>
                                            <img 
                                                v-else-if="header.avatar.fallbackUrl"
                                                :src="header.avatar.fallbackUrl"
                                                :alt="`${item[getFieldName(header)]} avatar`"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        <span>{{ item[getFieldName(header)] }}</span>
                                    </div>
                                    <template v-else>
                                        {{ item[getFieldName(header)] }}
                                    </template>
                                </slot>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Fixed pagination at bottom with consistent height -->
            <div class="sticky-pagination">
                <Pagination
                    v-if="pagination"
                    :current-page="currentPage"
                    :total-pages="totalPages"
                    :max-visible-buttons="5"
                    @page-changed="handlePageChanged"
                />
            </div>
        </div>
    </div>
</template>

<style>
/* Basic wrapper for table layout */
.table-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    min-height: 500px; /* Ensure minimum height to show all elements */
}

/* Fixed header styling */
.sticky-header {
    position: sticky;
    top: 0;
    z-index: 30;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    flex-shrink: 0; /* Prevent header from shrinking */
}

/* Scrollable content area with calculated height */
.table-content {
    position: relative;
    flex: 1;
    overflow-y: auto;
    overflow-x: auto;
    min-height: 200px; /* Ensure minimum height for content */
    height: calc(100% - 161px); /* Subtract header (63px) and pagination (98px) heights */
}

/* Fixed pagination at bottom with explicit height */
.sticky-pagination {
    position: sticky;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 30;
    background-color: white;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.05);
    height: 98px; /* Explicit height for pagination */
    flex-shrink: 0; /* Prevent pagination from shrinking */
}

/* Add this to ensure proper rotation and transition */
.rotate-180 {
    transform: rotate(180deg);
}

/* Hide scrollbar in Firefox */
.table-content {
    scrollbar-width: thin;
    scrollbar-color: rgba(203, 213, 225, 0.5) transparent;
}

/* Style scrollbar for webkit browsers */
.table-content::-webkit-scrollbar {
    width: 6px;
    height: 6px; /* Added height for horizontal scrollbar */
}

.table-content::-webkit-scrollbar-track {
    background: transparent;
}

.table-content::-webkit-scrollbar-thumb {
    background-color: rgba(203, 213, 225, 0.5);
    border-radius: 20px;
}

.table-content::-webkit-scrollbar-thumb:hover {
    background-color: rgba(203, 213, 225, 0.8);
}

/* Add this to support table with fixed layout */
table.table-fixed {
    table-layout: fixed;
    width: 100%;
}

/* Remove bottom margin from the tables in header and contents */
.sticky-header table, .table-content table {
    margin-bottom: 0;
}

/* New - ensure pagination is always visible on small screens */
@media (max-height: 600px) {
    .table-wrapper {
        min-height: 400px;
    }
    
    .table-content {
        min-height: 150px;
    }
}
</style>
