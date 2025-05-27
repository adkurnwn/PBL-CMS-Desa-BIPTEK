<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import DangerButton from "@/Components/DangerButton.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import { ref, computed, watch } from "vue";
import IconAksi from "@/Components/DashboardComponent/IconAksi.vue";
import DropdownFilter from "@/Components/DashboardComponent/DropdownFilter.vue";

const props = defineProps({
    demografi: Array,
    search: {
        type: String,
        default: ''
    },
    sort: {
        type: String,
        default: 'tahun'
    },
    direction: {
        type: String,
        default: 'desc'
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    years: {
        type: Array,
        default: () => []
    }
});

// State for search and sort
const searchQuery = ref(props.search || '');
const sortField = ref(props.sort || 'tahun');
const sortDirection = ref(props.direction || 'desc');
// Initialize filter state with empty object but watch for changes
const filterState = ref({});  // Empty object instead of using props.filters

// Get unique years from demografi data for filter options
const availableYears = computed(() => {
    if (props.years && props.years.length > 0) {
        return props.years;
    }

    // Extract unique years from demografi data if not provided
    const years = [...new Set(props.demografi?.map(item => item.tahun) || [])];
    return years.map(year => ({ id: year, name: year.toString() }));
});

// Filter fields configuration
const filterFields = computed(() => [
    {
        id: 'tahun',
        name: 'Tahun',
        options: availableYears.value
    }
]);

// Pagination information
const pagination = computed(() => {
    return {
        currentPage: 1,
        lastPage: 1,
        perPage: 25,
        total: props.demografi?.length || 0
    };
});

// Format demografi items for table
const demografiItems = computed(() => {
    return props.demografi || [];
});

// Table headers configuration
const tableHeaders = [
    {
        label: 'Tahun',
        field: 'tahun',
        sortable: true,
        nowrap: true,
        width: '10%',
        style: {
            fontSize: '18px',
            fontStyle: 'normal',
            fontWeight: '400',
            lineHeight: 'normal'
        }
    },
    {
        label: 'Jumlah Penduduk',
        field: 'jumlah_penduduk',
        sortable: true,
        nowrap: true,
        width: '20%'
    },
    {
        label: 'Jumlah KK',
        field: 'jumlah_keluarga',
        sortable: true,
        nowrap: true,
        width: '20%'
    },
    {
        label: 'Pendatang',
        field: 'jumlah_pendatang',
        sortable: true,
        nowrap: true,
        width: '20%'
    },
    {
        label: 'Pindah Keluar',
        field: 'jumlah_pindah_keluar',
        sortable: true,
        nowrap: true,
        width: '20%'
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        width: '10%'
    },
];

// Handle search
const handleSearch = (query) => {
    router.get(route('dashboard.demografi.index'), {
        search: query,
        sort: sortField.value,
        direction: sortDirection.value,
        ...filterState.value
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

// Handle filter changes
const handleFilterChange = (newFilters) => {
    // Check if filters were cleared
    const wasCleared = Object.keys(filterState.value).length > 0 && Object.keys(newFilters).length === 0;
    
    filterState.value = newFilters;
    
    // If filters were cleared, make a request without tahun parameter
    router.get(route('dashboard.demografi.index'), {
        search: searchQuery.value,
        sort: sortField.value,
        direction: sortDirection.value,
        ...(Object.keys(newFilters).length > 0 ? newFilters : {})
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

// Handle sort
const handleSort = (field) => {
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    router.get(route('dashboard.demografi.index'), {
        search: searchQuery.value,
        sort: sortField.value,
        direction: sortDirection.value,
        ...filterState.value
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

// Watch for external props changes
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

// Watch for external filter changes - update to handle empty filters properly
watch(() => props.filters, (newValue) => {
    // Only update if there's a meaningful difference
    const currentEmpty = !filterState.value || Object.keys(filterState.value).length === 0;
    const newEmpty = !newValue || Object.keys(newValue).length === 0 || 
                    (newValue.tahun === '' || newValue.tahun === undefined);
    
    if (currentEmpty && newEmpty) {
        // Both empty, no need to update
        return;
    }
    
    // Otherwise update the filter state
    if (newEmpty) {
        filterState.value = {};
    } else {
        filterState.value = { ...newValue };
    }
}, { deep: true, immediate: true });
</script>

<template>

    <Head title="Demografi" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Demografi" :hasRightContent="false">
                <template #icon>
                    <svg width="37" height="38" viewBox="0 0 37 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_1439_7132" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="37" height="38">
                            <rect y="0.5" width="37" height="37" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1439_7132)">
                            <path
                                d="M27.75 35.9583C25.6174 35.9583 23.7995 35.2068 22.2964 33.7036C20.7932 32.2005 20.0417 30.3826 20.0417 28.25C20.0417 26.1174 20.7932 24.2995 22.2964 22.7964C23.7995 21.2932 25.6174 20.5417 27.75 20.5417C29.8826 20.5417 31.7005 21.2932 33.2036 22.7964C34.7068 24.2995 35.4583 26.1174 35.4583 28.25C35.4583 30.3826 34.7068 32.2005 33.2036 33.7036C31.7005 35.2068 29.8826 35.9583 27.75 35.9583ZM10.7917 14.375H26.2083V11.2917H10.7917V14.375ZM17.999 32.875H7.70833C6.86042 32.875 6.13455 32.5731 5.53073 31.9693C4.92691 31.3655 4.625 30.6396 4.625 29.7917V8.20833C4.625 7.36042 4.92691 6.63455 5.53073 6.03073C6.13455 5.42691 6.86042 5.125 7.70833 5.125H29.2917C30.1396 5.125 30.8655 5.42691 31.4693 6.03073C32.0731 6.63455 32.375 7.36042 32.375 8.20833V18.5375C31.6299 18.1778 30.8783 17.908 30.1203 17.7281C29.3623 17.5483 28.5722 17.4583 27.75 17.4583C27.4674 17.4583 27.204 17.4648 26.9599 17.4776C26.7158 17.4905 26.4653 17.5226 26.2083 17.574V17.4583H10.7917V20.5417H20.2344C19.7719 20.9785 19.3543 21.4538 18.9818 21.9677C18.6092 22.4816 18.2816 23.034 17.999 23.625H10.7917V26.7083H17.074C17.0226 26.9653 16.9905 27.2158 16.9776 27.4599C16.9648 27.704 16.9583 27.9674 16.9583 28.25C16.9583 29.0979 17.0354 29.888 17.1896 30.6203C17.3438 31.3526 17.6135 32.1042 17.999 32.875ZM27.75 28.25C28.3924 28.25 28.9384 28.0252 29.388 27.5755C29.8377 27.1259 30.0625 26.5799 30.0625 25.9375C30.0625 25.2951 29.8377 24.7491 29.388 24.2995C28.9384 23.8498 28.3924 23.625 27.75 23.625C27.1076 23.625 26.5616 23.8498 26.112 24.2995C25.6623 24.7491 25.4375 25.2951 25.4375 25.9375C25.4375 26.5799 25.6623 27.1259 26.112 27.5755C26.5616 28.0252 27.1076 28.25 27.75 28.25ZM27.75 32.875C28.5208 32.875 29.2403 32.6951 29.9083 32.3354C30.5764 31.9757 31.1288 31.4747 31.5656 30.8323C30.9747 30.4726 30.358 30.2092 29.7156 30.0422C29.0733 29.8752 28.4181 29.7917 27.75 29.7917C27.0819 29.7917 26.4267 29.8752 25.7844 30.0422C25.142 30.2092 24.5253 30.4726 23.9344 30.8323C24.3712 31.4747 24.9236 31.9757 25.5917 32.3354C26.2597 32.6951 26.9792 32.875 27.75 32.875Z"
                                fill="#4C7660" />
                        </g>
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Search v-model="searchQuery" placeholder="Cari data demografi..." @search="handleSearch" />
            <DropdownFilter placeholder="Filter..." :filter-fields="filterFields" v-model:filters="filterState"
                @filter-changed="handleFilterChange" width="250px" dropdownTitle="Filter berdasarkan"
                activeFiltersTitle="Filter yang aktif" :close-on-select="false" />
            <Link :href="route('dashboard.demografi.create')">
            <TopButton>
                
            </TopButton>
            </Link>
        </template>

        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm   h-full">
                    <Tabel :headers="tableHeaders" :items="demografiItems" :pagination="true"
                        :items-per-page="pagination.perPage" :current-page="pagination.currentPage"
                        :total-pages="pagination.lastPage" :total-items="pagination.total" :sort-field="sortField"
                        :sort-direction="sortDirection"
                        empty-state-message="Tidak ada data demografi ditemukan. Buat data baru untuk memulai."
                        @sort="handleSort">
                        <!-- Custom rendering for Aksi column -->
                        <template #['item.Aksi']="{ item }">
                            <div class="flex justify-left">
                                <Link :href="route('dashboard.demografi.edit', item.tahun)">
                                <button
                                    class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
                                    <IconAksi type="edit" />
                                </button>
                                </Link>
                            </div>
                        </template>
                    </Tabel>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Dashboard specific styles to manage scrolling */
.dashboard-container {
    position: relative;
    height: calc(100vh - 348px);
    min-height: 650px;
    overflow: hidden;
}
</style>
