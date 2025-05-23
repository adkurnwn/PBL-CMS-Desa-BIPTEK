<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ref, computed, watch } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import DropdownFilter from "@/Components/DashboardComponent/DropdownFilter.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import IconAksi from "@/Components/DashboardComponent/IconAksi.vue";

const props = defineProps({
    news: Object,
    search: {
        type: String,
        default: ''
    },
    sort: {
        type: String,
        default: 'deleted_at'
    },
    direction: {
        type: String,
        default: 'desc'
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    categories: {
        type: Array,
        default: () => []
    }
});

const form = useForm({});
const searchQuery = ref(props.search || '');
const sortField = ref(props.sort || 'deleted_at');
const sortDirection = ref(props.direction || 'desc');
const filterState = ref(props.filters || {});

// Table headers configuration
const tableHeaders = [
    {
        label: 'Image',
        field: 'image',
        sortable: false,
        nowrap: true,
        width: '10%',
    },
    {
        label: 'Judul',
        field: 'title',
        sortable: true,
        nowrap: true,
        width: '35%', // Reduced from 40% to accommodate image column
        style: {
            fontSize: '18px',
            fontStyle: 'normal',
            fontWeight: '400',
            lineHeight: 'normal'
        },
    },
    {
        label: 'Diarsipkan',
        field: 'deleted_at',
        sortable: true,
        width: '15%' // Reduced from 20%
    },
    {
        label: 'Kategori',
        field: 'category.name',
        filterField: 'category_id',
        sortable: false,
        filterable: true,
        filterOptions: props.categories.map(cat => ({ id: cat.id, name: cat.name })),
        width: '20%'
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        width: '20%'
    },
];

const deleteNews = (slug) => {
    if (confirm("Are you sure you want to permanently delete this news?")) {
        form.delete(route("dashboard.news.destroy", slug), {
            preserveScroll: true,
        });
    }
};

const restoreNews = (slug) => {
    form.get(route("dashboard.news.restore", slug), {
        preserveScroll: true,
    });
};

// Unified filter fields configuration for archived news
const filterFields = computed(() => [
    {
        id: 'category_id',
        name: 'Kategori',
        options: props.categories.map(cat => ({ id: cat.id, name: cat.name }))
    }
]);

// Handle filter changes
const handleFilterChange = (newFilters) => {
    filterState.value = newFilters;

    router.get(route('dashboard.news.archive'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['news']
    });
};

// Handle search functionality
const handleSearch = (query) => {
    router.get(route('dashboard.news.archive'), {
        search: query,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['news']
    });
};

// Handle sort functionality
const handleSort = (field) => {
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    router.get(route('dashboard.news.archive'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['news']
    });
};

// Extract pagination info
const pagination = computed(() => {
    if (!props.news) return null;

    return {
        currentPage: props.news.current_page,
        lastPage: props.news.last_page,
        perPage: props.news.per_page,
        total: props.news.total,
    };
});

const formatDateTime = (date) => {
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');

    // Return an object with separate date and time properties
    return {
        date: `${day}/${month}/${year}`,
        time: `${hours}:${minutes}`
    };
};

// Format the news data for the table
const newsItems = computed(() => {
    const items = props.news?.data || [];
    return items.map(item => {
        // Get the first image from the news item if available
        let firstImage = null;
        if (item.images && item.images.length > 0) {
            firstImage = `/storage/${item.images[0].path}`;
        }
        
        // Format deleted_at date using the formatDateTime function
        const deletedDate = new Date(item.deleted_at);
        const formattedDateTime = formatDateTime(deletedDate);
        
        return {
            ...item,
            deleted_at_date: formattedDateTime.date,
            deleted_at_time: formattedDateTime.time,
            deleted_at: item.deleted_at, // Keep the original for sorting
            thumbnailImage: firstImage
        };
    });
});

// Handle page change
const handlePageChange = (page) => {
    router.get(route('dashboard.news.archive'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value,
        page: page,
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['news']
    });
};

// Watch for external changes
watch(() => props.search, (newValue) => {
    if (newValue !== searchQuery.value) {
        searchQuery.value = newValue;
    }
}, { immediate: true });

watch(() => [props.sort, props.direction], ([newSort, newDirection]) => {
    if (newSort !== sortField.value) {
        sortField.value = newSort;
    }
    if (newDirection !== sortDirection.value) {
        sortDirection.value = newDirection;
    }
}, { immediate: true });

watch(() => props.filters, (newValue) => {
    if (JSON.stringify(newValue) !== JSON.stringify(filterState.value)) {
        filterState.value = { ...newValue };
    }
}, { deep: true });
</script>

<template>
    <Head title="Arsip Berita" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Arsip Berita">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M160-120q-33 0-56.5-23.5T80-200v-640l67 67 66-67 67 67 67-67 66 67 67-67 67 67 66-67 67 67 67-67 66 67 67-67v640q0 33-23.5 56.5T800-120H160Zm0-80h280v-240H160v240Zm360 0h280v-80H520v80Zm0-160h280v-80H520v80ZM160-520h640v-120H160v120Z" />
                    </svg>
                </template>
                <template #right-content>
                    <div class="flex space-x-2">
                        <Link :href="route('dashboard.news.index')">
                            <PrimaryButton>List News</PrimaryButton>
                        </Link>
                    </div>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Search v-model="searchQuery" placeholder="Cari arsip berita..." @search="handleSearch" />
            <DropdownFilter 
                placeholder="Filter..." 
                :filter-fields="filterFields" 
                v-model:filters="filterState"
                @filter-changed="handleFilterChange" 
                width="250px" 
                dropdownTitle="Filter berdasarkan"
                activeFiltersTitle="Filter yang aktif" 
                :close-on-select="false" 
            />
            <Link :href="route('dashboard.news.index')">
                <TopButton />
            </Link>
        </template>

        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm h-full">
                    <Tabel 
                        :headers="tableHeaders" 
                        :items="newsItems" 
                        :pagination="true"
                        :items-per-page="pagination?.perPage || 10" 
                        :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" 
                        :total-items="pagination?.total || 0"
                        :filters="filterState"
                        :sort-field="sortField" 
                        :sort-direction="sortDirection"
                        empty-state-message="Tidak ada arsip berita yang ditemukan."
                        @sort="handleSort"
                        @filter-changed="handleFilterChange"
                        @page-changed="handlePageChange"
                    >
                        <!-- Add custom template for Image column -->
                        <template #['item.Image']="{ item }">
                            <div class="flex justify-center items-center h-full">
                                <div 
                                    class="w-[30px] h-[30px] sm:w-[40px] sm:h-[40px] md:w-[45px] md:h-[45px] lg:w-[50px] lg:h-[50px] xl:w-[50px] xl:h-[50px] 2xl:w-[60px] 2xl:h-[60px] 3xl:w-[70px] 3xl:h-[70px] 4xl:w-[80px] 4xl:h-[80px] rounded-md overflow-hidden bg-gray-100"
                                >
                                    <img 
                                        v-if="item.thumbnailImage" 
                                        :src="item.thumbnailImage"
                                        :alt="item.title"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-1/2 w-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template #[`item.Diarsipkan`]="{ item }">
                            <div class="flex flex-col">
                                <span class="text-[#404DBD] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ item.deleted_at_date }}
                                </span>
                                <span class="text-[#404DBD] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ item.deleted_at_time }}
                                </span>
                            </div>
                        </template>
                        
                        <!-- Add custom template for Kategori column -->
                        <template #['item.Kategori']="{ item }">
                            <span>{{ item.category ? item.category.name : 'Uncategorized' }}</span>
                        </template>
                        
                        <!-- Custom template for the Actions column -->
                        <template #['item.Aksi']="{ item }">
                            <div class="flex justify-left gap-[7px]">
                                <button @click="restoreNews(item.slug)"
                                    class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
                                    <IconAksi type="restore" />
                                </button>
                                <button @click="deleteNews(item.slug)"
                                    class="w-[21px] h-[21px] flex items-center justify-center text-red-600 hover:opacity-80 transition-opacity">
                                    <IconAksi type="delete" />
                                </button>
                            </div>
                        </template>
                    </Tabel>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
