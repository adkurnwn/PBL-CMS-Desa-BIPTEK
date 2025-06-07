<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import DropdownFilter from "@/Components/DashboardComponent/DropdownFilter.vue";
import TabDevider from "@/Components/DashboardComponent/TabDevider.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import Labels from "@/Components/DashboardComponent/Labels.vue";
import IconAksi from "@/Components/DashboardComponent/IconAksi.vue";

const props = defineProps({
    news: Object,
    flash: Object,
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
        default: 'created_at'
    },
    direction: {
        type: String,
        default: 'desc'
    },
    categories: Array,
    pendingCommentsCount: {
        type: Number,
        default: 0
    }
});

// Extract pagination info from the news object
const pagination = computed(() => {
    if (!props.news) return null;

    return {
        currentPage: props.news.current_page,
        lastPage: props.news.last_page,
        perPage: props.news.per_page,
        total: props.news.total,
    };
});

// Extract the news data from the paginated response
const newsItems = computed(() => {
    return props.news?.data || [];
});

const form = useForm({});

// Update searchQuery to use the prop
const searchQuery = ref(props.search || '');

// Add filter state
const filterState = ref({ ...props.filters });

// Add sort state
const sortField = ref(props.sort || 'created_at');
const sortDirection = ref(props.direction || 'desc');

// Table headers for news data
const tableHeaders = [
    {
        label: 'Judul Berita',
        field: 'title',
        sortable: true,
        nowrap: false,
        width: '50%',
        multiline: true
    },
    {
        label: 'Moderasi',
        field: 'is_moderate',
        sortable: false,
        filterable: true,
        filterOptions: [
            { id: true, name: 'YA' },
            { id: false, name: 'TIDAK' }
        ],
        width: '10%'
    },
    {
        label: 'Total Komentar',
        field: 'comments_count',
        sortable: true,
        width: '15%'
    },
    {
        label: 'Komentar Pending',
        field: 'pending_comments_count',
        sortable: true,
        width: '15%'
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        width: '10%'
    }
];

// Filter fields for the dropdown
const filterFields = computed(() => [
    {
        id: 'is_moderate',
        name: 'Status Moderasi',
        type: 'select',
        options: [
            { id: 'true', name: 'Dimoderasi' },
            { id: 'false', name: 'Tidak Dimoderasi' }
        ]
    }
]);

// Add search handling function
const handleSearch = () => {
    const params = {
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    };
    
    if (searchQuery.value) {
        params.search = searchQuery.value;
    }
    
    router.get(route('dashboard.comments.news'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Add filter change handling function
const handleFilterChange = (filters) => {
    filterState.value = filters;
    
    const params = {
        ...filters,
        sort: sortField.value,
        direction: sortDirection.value
    };
    
    if (searchQuery.value) {
        params.search = searchQuery.value;
    }
    
    router.get(route('dashboard.comments.news'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Add sort handling function
const handleSort = (field) => {
    // Toggle direction if same field, otherwise use 'asc'
    const newDirection = (sortField.value === field && sortDirection.value === 'asc') ? 'desc' : 'asc';
    
    sortField.value = field;
    sortDirection.value = newDirection;
    
    const params = {
        ...filterState.value,
        sort: field,
        direction: newDirection
    };
    
    if (searchQuery.value) {
        params.search = searchQuery.value;
    }
    
    router.get(route('dashboard.comments.news'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Add pagination handling function
const handlePageChanged = (page) => {
    const params = {
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value,
        page: page
    };
    
    if (searchQuery.value) {
        params.search = searchQuery.value;
    }
    
    router.get(route('dashboard.comments.news'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Watch for search query changes to handle real-time search
watch(searchQuery, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        // Debounce search to avoid too many requests
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            handleSearch();
        }, 300);
    }
});

let searchTimeout;

const formatDate = (date) => {
    return new Date(date).toLocaleString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Watch for external filter changes
watch(() => props.filters, (newValue) => {
    // Clean up empty filters before comparing
    const cleanedNewValue = Object.fromEntries(
        Object.entries(newValue).filter(([key, value]) => value !== '' && value !== null && value !== undefined)
    );
    const cleanedCurrentValue = Object.fromEntries(
        Object.entries(filterState.value).filter(([key, value]) => value !== '' && value !== null && value !== undefined)
    );
    
    if (JSON.stringify(cleanedNewValue) !== JSON.stringify(cleanedCurrentValue)) {
        filterState.value = { ...cleanedNewValue };
    }
}, { deep: true });

const viewNewsComments = (newsId) => {
    router.get(route('dashboard.comments.news.edit', newsId));
};

</script>

<template>
    <Head title="News Comment Moderation" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Moderasi Komentar Berita" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="37" height="38" viewBox="0 0 37 38" fill="none">
                        <mask id="mask0_1448_7519" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="37" height="38">
                            <rect y="0.5" width="37" height="37" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1448_7519)">
                            <path
                                d="M27.75 32.8753L25.5917 30.717L28.0198 28.2503H21.5833V25.167H28.0198L25.5917 22.7003L27.75 20.542L33.9167 26.7087L27.75 32.8753ZM4.625 32.8753V9.75033C4.625 8.90241 4.92691 8.17654 5.53073 7.57272C6.13455 6.9689 6.86042 6.66699 7.70833 6.66699H26.2083C27.0562 6.66699 27.7821 6.9689 28.3859 7.57272C28.9898 8.17654 29.2917 8.90241 29.2917 9.75033V17.5743C29.0347 17.5229 28.7778 17.4908 28.5208 17.4779C28.2639 17.4651 28.0069 17.4587 27.75 17.4587C25.1549 17.4587 22.9644 18.358 21.1786 20.1566C19.3929 21.9552 18.5 24.1392 18.5 26.7087C18.5 26.9656 18.5064 27.2225 18.5193 27.4795C18.5321 27.7364 18.5642 27.9934 18.6156 28.2503H9.25L4.625 32.8753Z"
                                fill="#4C7660" />
                        </g>
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #tab-devider>
            <TabDevider :tabs="[
                {
                    name: 'Semua Komentar',
                    href: route('dashboard.comments'),
                    active: false
                },
                {
                    name: 'Menunggu Moderasi',
                    href: route('dashboard.comments', { is_approved: 'pending' }),
                    active: false,
                    badge: pendingCommentsCount
                },
                {
                    name: 'Daftar Berita',
                    href: '#',
                    active: true
                }
            ]" />
        </template>

        <template #search-area>
            <Search 
                v-model="searchQuery" 
                placeholder="Cari berita..." 
                @search="handleSearch" 
            />
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
        </template>

        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm h-full">
                    <Tabel :headers="tableHeaders" :items="newsItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 25" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || 0"
                        :filters="filterState" :sort-field="sortField" :sort-direction="sortDirection"
                        :white-background="true" empty-state-message="tidak ada berita yang ditemukan"
                        @filter-changed="handleFilterChange" @sort="handleSort" @page-changed="handlePageChanged">

                        <template #['item.Judul_Berita']="{ item }">
                            <div class="flex flex-col">
                                <span class="font-medium text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                                    {{ item.title }}
                                </span>
                                <span class="text-[#404DBD] text-[7px] sm:text-[8px] md:text-[9px] lg:text-[10px] xl:text-[11px] 2xl:text-[12px] 3xl:text-[14px] 4xl:text-[16px] mt-1">
                                    {{ formatDate(item.created_at) }}
                                </span>
                            </div>
                        </template>

                        <template #['item.Total_Komentar']="{ item }">
                            <div class="flex flex-col">
                                <span class="text-[#404DBD] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ item.comments_count || "-" }}
                                </span>
                            </div>
                        </template>

                        <template #['item.Komentar_Pending']="{ item }">
                            <div class="flex flex-col">
                                    <span class="text-[#404DBD] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                        {{ item.pending_comments_count || "-" }}
                                    </span>
                            </div>
                        </template>

                        <template #['item.Moderasi']="{ item }">
                            <Labels :type="item.is_moderate ? 'ditanggapi' : 'delete'"
                                :text="item.is_moderate ? 'YA' : 'TIDAK'" />
                        </template>

                        <template #['item.Aksi']="{ item }">
                            <div class="flex justify-center items-center">
                                <button @click="viewNewsComments(item.id)" class="hover:opacity-75 transition-opacity">
                                    <IconAksi type="view" />
                                </button>
                            </div>
                        </template>
                    </Tabel>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
