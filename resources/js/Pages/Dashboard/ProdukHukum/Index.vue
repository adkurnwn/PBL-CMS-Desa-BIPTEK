<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import DropdownFilter from "@/Components/DashboardComponent/DropdownFilter.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import IconAksi from "@/Components/DashboardComponent/IconAksi.vue";
import { ref, computed, watch } from "vue";

const props = defineProps({
    produkHukum: {
        type: Object,
        default: () => ({
            data: [],
            meta: {
                current_page: 1,
                last_page: 1,
                total: 0,
                per_page: 10
            }
        })
    },
    categories: {
        type: Array,
        default: () => []
    },
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
    availableYears: {
        type: Array,
        default: () => []
    },
});

const form = useForm({});

// State variables for search, filter, and sort
const searchQuery = ref(props.search || '');
const filterState = ref(props.filters || {});
const sortField = ref(props.sort || 'created_at');
const sortDirection = ref(props.direction || 'desc');

// Table headers configuration
const tableHeaders = [
    {
        label: 'Judul',
        field: 'title',
        sortable: true,
        width: '45%',
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
        field: 'category.name',
        filterField: 'category_id',
        filterable: true,
        filterOptions: props.categories.map(cat => ({ id: cat.id, name: cat.name })),
        sortable: false,
        width: '20%'
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        width: '15%'
    },
];

// Delete produk hukum function
const deleteProdukHukum = (slug) => {
    if (confirm("Apakah Anda yakin ingin menghapus produk hukum ini?")) {
        form.delete(route("dashboard.produk-hukum.destroy", slug), {
            preserveScroll: true,
        });
    }
};

// Filter fields configuration
const filterFields = computed(() => [
    {
        id: 'category_id',
        name: 'Kategori',
        options: props.categories.map(cat => ({ id: cat.id, name: cat.name }))
    },
    {
        id: 'year',
        name: 'Tahun',
        options: props.availableYears.length > 0 
            ? props.availableYears 
            : [{ id: new Date().getFullYear(), name: new Date().getFullYear().toString() }]
    }
]);

// Handle search functionality
const handleSearch = (query) => {
    router.get(route('dashboard.produk-hukum.index'), {
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
    
    router.get(route('dashboard.produk-hukum.index'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true
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
    router.get(route('dashboard.produk-hukum.index'), {
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
    router.get(route('dashboard.produk-hukum.index'), {
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

// Format the produk hukum data for the table
const produkHukumItems = computed(() => {
    if (!props.produkHukum || !props.produkHukum.data) {
        return [];
    }
    
    return props.produkHukum.data.map(item => {
        // Format dates from the date field instead of created_at
        const itemDate = item.date ? new Date(item.date) : new Date(item.created_at);
        
        const formatDateTime = (date) => {
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            
            return `${day}/${month}/${year}`;
        };
        
        return {
            ...item,
            created_at: formatDateTime(itemDate), // Still use created_at as the display field
        };
    });
});

// Function to get display name for filter values
const getFilterDisplayName = (filterId, filterValue) => {
    if (filterId === 'category_id') {
        const category = props.categories.find(c => c.id === filterValue);
        return category ? category.name : filterValue;
    }
    
    if (filterId === 'year') {
        return filterValue;
    }
    
    return filterValue;
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
}, { deep: true });
</script>

<template>
    <Head title="Produk Hukum" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Produk Hukum">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M160-120v-80h480v80H160Zm226-194L160-540l84-86 228 226-86 86Zm254-254L414-796l86-84 226 226-86 86Zm184 408L302-682l56-56 522 522-56 56Z" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Search v-model="searchQuery" placeholder="Cari produk hukum..." @search="handleSearch" />
            <DropdownFilter 
                placeholder="Filter..." 
                :filter-fields="filterFields" 
                v-model:filters="filterState"
                @filter-changed="handleFilterChange" 
                dropdownTitle="Filter berdasarkan"
                activeFiltersTitle="Filter yang aktif" 
                :close-on-select="false"
                :get-display-name="getFilterDisplayName"
            />
            <Link :href="route('dashboard.produk-hukum.create')">
                <TopButton></TopButton>
            </Link>
        </template>

        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm h-full">
                    <Tabel 
                        :headers="tableHeaders" 
                        :items="produkHukumItems" 
                        :pagination="true"
                        :items-per-page="produkHukum.meta?.per_page || 10" 
                        :current-page="produkHukum.meta?.current_page || 1"
                        :total-pages="produkHukum.meta?.last_page || 1" 
                        :total-items="produkHukum.meta?.total || 0"
                        :filters="filterState" 
                        :sort-field="sortField" 
                        :sort-direction="sortDirection"
                        empty-state-message="Tidak ada produk hukum yang ditemukan."
                        @sort="handleSort" 
                        @filter-changed="handleFilterChange" 
                        @page-changed="handlePageChange"
                    >
                        <!-- Custom template for Judul column -->
                        <template #['item.Judul']="{ item }">
                            <span class="text-TableText">{{ item.title }}</span>
                        </template>

                        <!-- Custom template for Tanggal column -->
                        <template #['item.Tanggal']="{ item }">
                            <div class="flex flex-col">
                                <span class="text-[#404DBD] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ item.created_at }}
                                </span>
                            </div>
                        </template>

                        <!-- Custom template for Kategori column -->
                        <template #['item.Kategori']="{ item }">
                            <span>{{ item.category ? item.category.name : 'Uncategorized' }}</span>
                        </template>

                        <!-- Custom template for the Actions column -->
                        <template #['item.Aksi']="{ item }">
                            <div class="flex justify-left gap-2">
                                <Link :href="route('dashboard.produk-hukum.edit', item.slug)">
                                    <button class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
                                        <IconAksi type="edit" />
                                    </button>
                                </Link>
                                <button @click="deleteProdukHukum(item.slug)" 
                                    class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
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
