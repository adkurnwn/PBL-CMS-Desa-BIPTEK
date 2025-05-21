<script setup>
import { ref, computed, watch, onMounted } from "vue";
import Pagination from "@/Components/DashboardComponent/Pagination.vue";
import { Link } from "@inertiajs/vue3";

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
        //   maxWidth: '300px',           // Maximum column width
        //   transform: 'capitalize'      // Text transformation: 'capitalize', 'uppercase', 'lowercase', 'titlecase'
        //   filterable: true,          // Whether column supports filtering
        //   filterOptions: [],         // Array of filter options if pre-defined
        //   filterField: 'position.id' // Field to filter on if different from field
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
    },
    // Add props for external pagination control
    currentPage: {
        type: Number,
        default: 1
    },
    totalPages: {
        type: Number,
        default: 1
    },
    // Add props for external sorting control
    sortField: {
        type: String,
        default: null
    },
    sortDirection: {
        type: String,
        default: 'asc'
    },
    // Add totalItems prop to receive the total count from parent
    totalItems: {
        type: Number,
        default: 0
    },
    // Add props for external filtering
    filters: {
        type: Object,
        default: () => ({})
    },
});

const emit = defineEmits(['page-changed', 'sort', 'filter-changed']);

// Helper to get field name for a header (fallback to lowercase label if not provided)
const getFieldName = (header) => {
    return header.field || header.label.toLowerCase();
};

// Update sort function to emit event instead of locally sorting
const sortBy = (column) => {
    const header = props.headers.find(h => h.label === column);
    if (!header || !header.sortable) return;
    
    // Get the field name for this column
    const field = getFieldName(header);
    
    // Emit the sort event to be handled by the parent
    emit('sort', field);
};

// Update the sort icon to use props instead of local state
const getSortIconClass = (column) => {
    const header = props.headers.find(h => h.label === column);
    if (!header) return '';
    
    const field = getFieldName(header);
    if (props.sortField !== field) return '';
    
    return props.sortDirection === 'desc' ? 'rotate-180' : '';
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

// Use props for pagination or internal state
const internalCurrentPage = ref(props.currentPage);

// Watch for external currentPage changes
watch(() => props.currentPage, (newValue) => {
    internalCurrentPage.value = newValue;
});

const totalPagesComputed = computed(() => {
    if (props.pagination && props.totalPages > 1) {
        return props.totalPages;
    }
    return Math.max(1, Math.ceil(props.items.length / props.itemsPerPage));
});

const paginatedItems = computed(() => {
    // If using external pagination, just return the sorted items
    if (props.pagination && props.totalPages > 1) {
        return sortedItems.value;
    }
    
    // Otherwise, handle pagination locally
    if (!props.pagination) return sortedItems.value;
    
    const startIndex = (internalCurrentPage.value - 1) * props.itemsPerPage;
    const endIndex = startIndex + props.itemsPerPage;
    return sortedItems.value.slice(startIndex, endIndex);
});

const handlePageChanged = (page) => {
    internalCurrentPage.value = page;
    emit('page-changed', page);
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

// Function to transform text based on header transform property
const transformText = (text, transform) => {
    if (!text || typeof text !== 'string') return text;
    
    switch (transform) {
        case 'capitalize':
            return text.charAt(0).toUpperCase() + text.slice(1);
        case 'uppercase':
            return text.toUpperCase();
        case 'lowercase':
            return text.toLowerCase();
        case 'titlecase':
            return text.replace(/\w\S*/g, (word) => {
                return word.charAt(0).toUpperCase() + word.substr(1).toLowerCase();
            });
        default:
            return text;
    }
};

// Function to parse JSON job descriptions
const parseJobDescription = (description) => {
    if (!description) return [];
    if (Array.isArray(description)) return description;

    try {
        return JSON.parse(description);
    } catch (e) {
        return [];
    }
};

// Modify truncation approach - replace the existing functions with these
const isTextTruncated = (text) => {
    if (!text || typeof text !== 'string') return false;
    return true; // Always provide title for consistent hover behavior
};

// Remove character-based truncation function, 
// we'll rely on CSS for truncation instead
const truncateText = (text) => {
    if (!text || typeof text !== 'string') return text;
    return text; // Return full text, CSS will handle truncation
};

// Add to the props headers documentation:
// Expected format: [{ 
//   // ...existing properties...
//   truncate: true,           // Whether to truncate text (default: true)
//   multiline: false,         // Whether to allow multiple lines (default: false)
// }]

// Modify this function to check if text should be truncated
// Default to true if not specified
const shouldTruncate = (header) => {
    // If truncate property is explicitly set to false, don't truncate
    // Otherwise, truncate by default (if truncate is undefined or true)
    return header.truncate !== false;
};

// Check if cell should allow multiple lines
const isMultiline = (header) => {
    // If multiline is true, allow multiple lines
    // Default to false if not specified
    return header.multiline === true;
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
                                        { 'cursor-pointer': header.sortable || header.filterable },
                                        header.headerClass
                                    ]"
                                :style="styleObjectToString(header.headerStyle)"
                            >
                                <div class="flex items-center h-full justify-between">
                                    <div class="flex items-center">
                                        <span>{{ header.label }}</span>
                                        
                                        <!-- Sort icon -->
                                        <svg 
                                            v-if="header.sortable"
                                            xmlns="http://www.w3.org/2000/svg" 
                                            width="24" 
                                            height="24" 
                                            viewBox="0 0 24 25" 
                                            fill="none"
                                            class="ml-2 transform transition-transform duration-200"
                                            :class="getSortIconClass(header.label)"
                                            @click.stop="header.sortable ? sortBy(header.label) : null"
                                        >
                                            <mask id="mask0_1249_5047" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="25">
                                                <rect y="0.5" width="24" height="24" fill="#D9D9D9"/>
                                            </mask>
                                            <g mask="url(#mask0_1249_5047)">
                                                <path d="M7 14.5L12 9.5L17 14.5H7Z" fill="#111827"/>
                                            </g>
                                        </svg>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
            
            <!-- Scrollable content area -->
            <div class="table-content">
                <table class="min-w-full divide-y divide-gray-200 table-fixed">
                    <colgroup>
                        <col 
                            v-for="(header, index) in headers" 
                            :key="`col-${index}`"
                            :style="getColumnWidthStyle(header)"
                        />
                    </colgroup>
                    <tbody class="bg-white text-TableText">
                        <tr
                            v-for="(item, rowIndex) in items"
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
                                        { 'whitespace-nowrap': header.nowrap && !isMultiline(header) },
                                        header.cellClass
                                    ]"
                                :style="styleObjectToString(header.style)"
                            >
                                <!-- Use format that matches how it's used in Dashboard.vue and elsewhere -->
                                <slot :name="`item.${header.label}`" :item="item" :header="header">
                                    <!-- Add special handling for job_description field -->
                                    <div v-if="getFieldName(header) === 'job_description'">
                                        <ul class="list-disc ml-5">
                                            <template v-for="(desc, index) in parseJobDescription(item[getFieldName(header)]).slice(0, 2)" :key="index">
                                                <li>
                                                    <span 
                                                        :title="shouldTruncate(header) ? desc : ''"
                                                        :class="{'truncate-text-cell': shouldTruncate(header), 'multiline-text-cell': isMultiline(header)}"
                                                    >
                                                        {{ desc }}
                                                    </span>
                                                </li>
                                            </template>
                                            <li v-if="parseJobDescription(item[getFieldName(header)]).length > 2" 
                                                class="text-menuText cursor-pointer mt-1 list-none">
                                                <Link :href="route('dashboard.positions.edit', item.id)">
                                                    Lihat Selengkapnya
                                                </Link>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- Normal avatar handling -->
                                    <div v-else-if="header.avatar" class="flex items-center">
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
                                        <span 
                                            :title="shouldTruncate(header) ? item[getFieldName(header)] : ''"
                                            :class="{'truncate-text-cell': shouldTruncate(header), 'multiline-text-cell': isMultiline(header)}"
                                        >
                                            {{ header.transform ? transformText(item[getFieldName(header)], header.transform) : item[getFieldName(header)] }}
                                        </span>
                                    </div>
                                    <template v-else>
                                        <span 
                                            :title="shouldTruncate(header) ? item[getFieldName(header)] : ''"
                                            :class="{'truncate-text-cell': shouldTruncate(header), 'multiline-text-cell': isMultiline(header)}"
                                        >
                                            {{ header.transform ? transformText(item[getFieldName(header)], header.transform) : item[getFieldName(header)] }}
                                        </span>
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
                    :current-page="internalCurrentPage"
                    :total-pages="totalPagesComputed"
                    :max-visible-buttons="5"
                    :total-items="props.totalItems"
                    :items-per-page="itemsPerPage"
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

/* Filter Styles */
.filter-badge {
    @apply inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800;
}

/* Truncated text styling */
.truncate-text-cell {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    width: 100%;
    display: block;
}

/* Multiline text styling */
.multiline-text-cell {
    white-space: normal;
    overflow: visible;
    word-break: break-word;
    line-height: 1.5;
    display: block;
}

/* Ensure tooltip displays properly */
[title] {
    position: relative;
}

/* Make sure cursor shows it's hoverable */
[title]:not([title=""]) {
    cursor: help;
}
</style>
