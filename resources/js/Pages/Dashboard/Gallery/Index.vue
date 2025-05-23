<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import DropdownFilter from "@/Components/DashboardComponent/DropdownFilter.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import IconAksi from "@/Components/DashboardComponent/IconAksi.vue";
import Labels from "@/Components/DashboardComponent/Labels.vue";
import { ref, computed, watch } from "vue";

const props = defineProps({
    galleries: Object, // Changed from Array to Object to handle pagination
    search: {
        type: String,
        default: ''
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    sort: {
        type: String,
        default: 'title'
    },
    direction: {
        type: String,
        default: 'asc'
    },
    categories: {
        type: Array,
        default: () => []
    }
});

const form = useForm({});

// State variables for search, filter, and sort
const searchQuery = ref(props.search || '');
const filterState = ref(props.filters || {});
const sortField = ref(props.sort || 'title');
const sortDirection = ref(props.direction || 'asc');

// Table headers configuration
const tableHeaders = [
    {
        label: 'Gambar',
        field: 'image',
        sortable: false,
        width: '20%',
    },
    {
        label: 'Judul',
        field: 'title',
        sortable: true,
        width: '30%',
        style: {
            fontSize: '18px',
            fontStyle: 'normal',
            fontWeight: '400',
            lineHeight: 'normal'
        },
        truncate: true,
    },
    {
        label: 'Tanggal',
        field: 'created_at',
        sortable: true,
        width: '20%'
    },
    {
        label: 'Kategori',
        filterField: 'category_id',
        filterOptions: props.categories.map(cat => ({ id: cat.id, name: cat.name })),
        filterable: true,
        field: 'category.name',
        sortable: true,
        width: '20%'
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        width: '10%'
    }
];

// Filter fields configuration
const filterFields = computed(() => [
    {
        id: 'category_id',
        name: 'Kategori',
        options: props.categories.map(cat => ({ id: cat.id, name: cat.name }))
    },
]);

// Gallery items with any needed transformations
const galleryItems = computed(() => {
    const items = props.galleries?.data || [];
    return items.map(item => {
        // Get up to 3 images from the gallery for the stack effect
        let galleryImages = [];
        if (item.images && item.images.length > 0) {
            // Get up to 3 images for the stack
            galleryImages = item.images.slice(0, 3).map(img => ({
                path: `/storage/${img.path}`,
                alt: img.alt || item.title
            }));
        }

        return {
            ...item,
            originalTitle: item.title,
            thumbnailGambar: item.images && item.images.length > 0 ? `/storage/${item.images[0].path}` : null,
            galleryImages: galleryImages,
            imageCount: item.images ? item.images.length : 0
        };
    });
});

// Extract pagination info from the galleries object
const pagination = computed(() => {
    if (!props.galleries) return null;

    return {
        currentPage: props.galleries.current_page,
        lastPage: props.galleries.last_page,
        perPage: props.galleries.per_page,
        total: props.galleries.total,
    };
});

// Handle search functionality
const handleSearch = (query) => {
    router.get(route('dashboard.galleries.index'), {
        search: query,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

// Handle filter changes
const handleFilterChange = (newFilters) => {
    filterState.value = newFilters;

    router.get(route('dashboard.galleries.index'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['galleries']
    });
};

// Handle sort functionality
const handleSort = (field) => {
    // If clicking on the same field, toggle direction
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        // New field, default to ascending
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    // Navigate with updated sort params
    router.get(route('dashboard.galleries.index'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

// Handle page change
const handlePageChange = (page) => {
    router.get(route('dashboard.galleries.index'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value,
        page: page,
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

const deleteGallery = (slug) => {
    if (confirm("Are you sure you want to delete this gallery?")) {
        form.delete(route("dashboard.galleries.destroy", slug), {
            preserveScroll: true,
        });
    }
};

// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);

    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');

    return `${day}/${month}/${year} ${hours}:${minutes}`;
};

// Determine category type for styling
const getCategoryType = (categoryName) => {
    if (!categoryName) return 'other';

    const name = categoryName.toLowerCase();

    // Add logic to determine category type based on name
    if (name.includes('umum')) return 'umum';
    if (name.includes('kegiatan')) return 'kegiatan';
    if (name.includes('event')) return 'event';
    if (name.includes('fasilitas')) return 'fasilitas';

    // Default type
    return 'other';
};

// Watch for external search changes
watch(() => props.search, (newValue) => {
    if (newValue !== searchQuery.value) {
        searchQuery.value = newValue;
    }
}, { immediate: true });

// Watch for external sort changes
watch(() => [props.sort, props.direction], ([newSort, newDirection]) => {
    if (newSort !== sortField.value) {
        sortField.value = newSort;
    }
    if (newDirection !== sortDirection.value) {
        sortDirection.value = newDirection;
    }
}, { immediate: true });

// Watch for external filter changes
watch(() => props.filters, (newValue) => {
    if (JSON.stringify(newValue) !== JSON.stringify(filterState.value)) {
        filterState.value = { ...newValue };
    }
}, { deep: true, immediate: true });
</script>

<template>

    <Head title="Gallery" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Galeri">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M360-400h400L622-580l-92 120-62-80-108 140Zm-40 160q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320Zm0-80h480v-480H320v480ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Zm160-720v480-480Z" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Search v-model="searchQuery" placeholder="Cari galeri..." @search="handleSearch" />
            <DropdownFilter placeholder="Filter..." :filter-fields="filterFields" v-model:filters="filterState"
                @filter-changed="handleFilterChange" width="250px" dropdownTitle="Filter berdasarkan"
                activeFiltersTitle="Filter yang aktif" :close-on-select="false" />
            <Link :href="route('dashboard.galleries.create')">
            <TopButton></TopButton>
            </Link>
            <Link :href="route('dashboard.galleries.archive')">
            <TopButton type="Diarsipkan" />
            </Link>
        </template>

        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm h-full">
                    <Tabel :headers="tableHeaders" :items="galleryItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 10" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || 0"
                        :filters="filterState" :sort-field="sortField" :sort-direction="sortDirection"
                        empty-state-message="Tidak ada galeri ditemukan" @sort="handleSort"
                        @filter-changed="handleFilterChange" @page-changed="handlePageChange">
                        <!-- Add custom template for Gambar column with stacked effect -->
                        <template #['item.Gambar']="{ item }">
                            <div class="flex justify-center items-center h-full">
                                <div
                                    class="relative w-[40px] h-[40px] sm:w-[50px] sm:h-[50px] md:w-[55px] md:h-[55px] lg:w-[60px] lg:h-[60px] xl:w-[60px] xl:h-[60px] 2xl:w-[70px] 2xl:h-[70px] 3xl:w-[80px] 3xl:h-[80px] 4xl:w-[90px] 4xl:h-[90px]">
                                    <!-- Show empty state if no images -->
                                    <div v-if="!item.imageCount || item.imageCount === 0"
                                        class="w-full h-full flex items-center justify-center bg-gray-200 rounded-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-1/2 w-1/2 text-gray-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>

                                    <!-- Single image display when only one image is available, like in News component -->
                                    <div v-else-if="item.imageCount === 1"
                                        class="w-full h-full rounded-md overflow-hidden bg-gray-100">
                                        <img v-if="item.thumbnailGambar" :src="item.thumbnailGambar" :alt="item.title"
                                            class="w-full h-full object-cover" />
                                    </div>

                                    <!-- Stacked images when multiple images are available -->
                                    <template v-else>
                                        <!-- Display each image in the stack, in reverse order so first image is on top -->
                                        <div v-for="(image, index) in [...item.galleryImages].reverse()" :key="index"
                                            :style="{
                                                zIndex: item.galleryImages.length - index,
                                                transform: `translateX(${(item.galleryImages.length - index - 1) * -8}px) translateY(${(item.galleryImages.length - index - 1) * -4}px)`,
                                                opacity: index === 0 ? 1 : 0.85 - (index * 0.2)
                                            }"
                                            class="gallery-stack-image absolute rounded-md overflow-hidden shadow-sm bg-white">
                                            <img :src="image.path" :alt="image.alt"
                                                class="w-full h-full object-cover" />
                                        </div>

                                        <!-- Image count badge when more than shown in stack -->
                                        <div v-if="item.imageCount > 3"
                                            class="absolute -bottom-2 -right-2 bg-selectedText text-white text-xs rounded-full w-6 h-6 flex items-center justify-center shadow-sm"
                                            :style="{ zIndex: item.galleryImages.length + 1 }">
                                            +{{ item.imageCount - 3 }}
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Customize the display of the title field -->
                        <template #['item.Judul']="{ item }">
                            <span :title="item.originalTitle"
                                class="truncate-text-cell text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                                <span v-if="item.isDraft" class="text-red-600">Draft: </span>
                                <span v-if="item.isDraft" class="italic">"{{ item.originalTitle }}"</span>
                                <span v-if="!item.isDraft">{{ item.originalTitle }}</span>
                            </span>
                        </template>

                        <!-- Template for Category column with Labels component -->
                        <template #['item.Kategori']="{ item }">
                            <span>{{ item.category ? item.category.name : 'Uncategorized' }}</span>
                        </template>

                        <!-- Template for Date column with improved formatting -->
                        <template #['item.Tanggal']="{ item }">
                            <div class="flex flex-col">
                                <span
                                    class="text-gray-500 text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ item.isDraft ? 'Diperbarui pada' : 'Dibuat pada' }}
                                </span>
                                <span
                                    class="text-[#404DBD] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ formatDate(item.created_at) }}
                                </span>
                            </div>
                        </template>

                        <!-- Customize the actions column -->
                        <template #['item.Aksi']="{ item }">
                            <div
                                class="flex justify-left gap-[7px] sm:gap-[7px] md:gap-[7px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px] items-center">
                                <Link :href="route('dashboard.galleries.edit', item.slug)">
                                <button
                                    class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
                                    <IconAksi type="edit" />
                                </button>
                                </Link>
                                <button @click="deleteGallery(item.slug)"
                                    class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
                                    <IconAksi type="archive" />
                                </button>
                            </div>
                        </template>
                    </Tabel>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<style scoped>
.gallery-stack-image {
    width: 100%;
    height: 100%;
    transition: all 0.2s ease;
    border: 1px solid rgba(229, 231, 235, 0.5);
}

/* Hover effect to spread the stack slightly */
.gallery-stack-image:hover {
    transform: translateX(0) translateY(0) !important;
    z-index: 10 !important;
    opacity: 1 !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
