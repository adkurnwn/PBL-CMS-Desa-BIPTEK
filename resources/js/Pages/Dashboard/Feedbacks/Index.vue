<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import DropdownFilter from "@/Components/DashboardComponent/DropdownFilter.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";
import TabDevider from "@/Components/DashboardComponent/TabDevider.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import Labels from "@/Components/DashboardComponent/Labels.vue";
import Feedbacks from "@/Components/DashboardComponent/Feedbacks.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";

const props = defineProps({
    feedbacks: Object,
    flash: Object,
    search: {
        type: String,
        default: ''
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    // Add props for sorting
    sort: {
        type: String,
        default: 'created_at'
    },
    direction: {
        type: String,
        default: 'desc'
    }
});

// Extract pagination info from the feedbacks object
const pagination = computed(() => {
    if (!props.feedbacks) return null;

    return {
        currentPage: props.feedbacks.current_page,
        lastPage: props.feedbacks.last_page,
        perPage: props.feedbacks.per_page,
        total: props.feedbacks.total,
    };
});

// Extract the feedback data from the paginated response
const feedbackItems = computed(() => {
    return props.feedbacks?.data || [];
});

const form = useForm({});

// Update searchQuery to use the prop
const searchQuery = ref(props.search || '');

// Add filter state - exclude kategori from filters
const filterState = ref((() => {
    const filters = { ...props.filters };
    delete filters.kategori;
    return filters;
})());

// Add sort state
const sortField = ref(props.sort || 'created_at');
const sortDirection = ref(props.direction || 'desc');

// Table headers for feedback data
const tableHeaders = [
    {
        label: 'Judul',
        field: 'judul',
        sortable: true,
        nowrap: false,
        width: '55%',
        multiline: true
    },
    {
        label: 'Tanggal',
        field: 'created_at',
        sortable: true,
        width: '10%'
    },
    {
        label: 'Status',
        field: 'status',
        sortable: false,
        filterable: true,
        filterOptions: [
            { id: 'Menunggu', name: 'Menunggu' },
            { id: 'Ditanggapi', name: 'Ditanggapi' }
        ],
        width: '10%'
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        width: '10%'
    },
];

// Add activeTab state - default to 'Keluhan' instead of 'all'
const activeTab = ref(props.filters?.kategori || 'Keluhan');

// Update filterFields to exclude kategori since it's handled by tabs
const filterFields = computed(() => [
    {
        id: 'status',
        name: 'Status',
        options: [
            { id: 'Menunggu', name: 'Menunggu' },
            { id: 'Ditanggapi', name: 'Ditanggapi' }
        ]
    }
]);

// Handle tab changes
const handleTabChange = (kategori) => {
    activeTab.value = kategori;

    router.get(route('dashboard.feedbacks.index'), {
        search: searchQuery.value,
        ...filterState.value,
        kategori: kategori,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['feedbacks']
    });
};

// Implement search functionality
const handleSearch = (query) => {
    router.get(route('dashboard.feedbacks.index'), {
        search: query,
        ...filterState.value,
        kategori: activeTab.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['feedbacks']
    });
};

// Handle filter changes from the unified dropdown filter
const handleFilterChange = (newFilters) => {
    // Exclude kategori from filters since it's handled by tabs
    const updatedFilters = { ...newFilters };
    delete updatedFilters.kategori;

    filterState.value = updatedFilters;

    router.get(route('dashboard.feedbacks.index'), {
        search: searchQuery.value,
        ...updatedFilters,
        kategori: activeTab.value, // Add kategori separately
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['feedbacks']
    });
};

// Add sort handling
const handleSort = (field) => {
    // If clicking on the same field, toggle direction
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        // New field, default to ascending
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    // Preserve all current filters excluding kategori
    const currentFilters = { ...filterState.value };

    // Navigate with updated sort params
    router.get(route('dashboard.feedbacks.index'), {
        search: searchQuery.value,
        ...currentFilters,
        kategori: activeTab.value, // Add kategori separately
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['feedbacks']
    });
};

const deleteFeedback = (id) => {
    if (confirm("Are you sure you want to delete this feedback?")) {
        form.delete(route("dashboard.feedbacks.destroy", id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Watch for external search changes
watch(() => props.search, (newValue) => {
    if (newValue !== searchQuery.value) {
        searchQuery.value = newValue;
    }
}, { immediate: true });

// Watch for external filter changes
watch(() => props.filters, (newValue) => {
    const filtersWithoutKategori = { ...newValue };
    delete filtersWithoutKategori.kategori;

    if (JSON.stringify(filtersWithoutKategori) !== JSON.stringify(filterState.value)) {
        filterState.value = { ...filtersWithoutKategori };
        // Update active tab based on kategori filter - default to 'Keluhan'
        activeTab.value = newValue.kategori || 'Keluhan';
    }
}, { deep: true });

// Watch for external sort changes
watch(() => [props.sort, props.direction], ([newSort, newDirection]) => {
    if (newSort !== sortField.value) {
        sortField.value = newSort;
    }
    if (newDirection !== sortDirection.value) {
        sortDirection.value = newDirection;
    }
}, { immediate: true });
</script>

<template>

    <Head title="Feedback Management" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Saran & Keluhan" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="33" viewBox="0 0 32 33" fill="none">
                        <path
                            d="M3.70833 31.4163C2.86042 31.4163 2.13455 31.1144 1.53073 30.5106C0.92691 29.9068 0.625 29.1809 0.625 28.333V6.74967C0.625 5.90176 0.92691 5.17589 1.53073 4.57207C2.13455 3.96825 2.86042 3.66634 3.70833 3.66634H5.25V0.583008H8.33333V3.66634H20.6667V0.583008H23.75V3.66634H25.2917C26.1396 3.66634 26.8655 3.96825 27.4693 4.57207C28.0731 5.17589 28.375 5.90176 28.375 6.74967V13.957C28.375 14.3938 28.2273 14.7599 27.9318 15.0554C27.6363 15.3509 27.2701 15.4986 26.8333 15.4986C26.3965 15.4986 26.0304 15.3509 25.7349 15.0554C25.4394 14.7599 25.2917 14.3938 25.2917 13.957V12.9163H3.70833V28.333H12.65C13.0868 28.333 13.453 28.4808 13.7484 28.7762C14.0439 29.0717 14.1917 29.4379 14.1917 29.8747C14.1917 30.3115 14.0439 30.6776 13.7484 30.9731C13.453 31.2686 13.0868 31.4163 12.65 31.4163H3.70833ZM23.75 32.958C21.6174 32.958 19.7995 32.2064 18.2964 30.7033C16.7932 29.2002 16.0417 27.3823 16.0417 25.2497C16.0417 23.117 16.7932 21.2992 18.2964 19.796C19.7995 18.2929 21.6174 17.5413 23.75 17.5413C25.8826 17.5413 27.7005 18.2929 29.2036 19.796C30.7068 21.2992 31.4583 23.117 31.4583 25.2497C31.4583 27.3823 30.7068 29.2002 29.2036 30.7033C27.7005 32.2064 25.8826 32.958 23.75 32.958ZM26.3323 28.9111L27.4115 27.832L24.5208 24.9413V20.6247H22.9792V25.558L26.3323 28.9111Z"
                            fill="#4C7660" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Search v-model="searchQuery" placeholder="Cari saran & keluhan..." @search="handleSearch" />
            <DropdownFilter placeholder="Filter..." :filter-fields="filterFields" v-model:filters="filterState"
                @filter-changed="handleFilterChange" width="250px" dropdownTitle="Filter berdasarkan"
                activeFiltersTitle="Filter yang aktif" :close-on-select="false" />
        </template>

        <template #tab-devider>
            <TabDevider :tabs="[
                {
                    name: 'Keluhan',
                    href: '#',
                    active: activeTab === 'Keluhan',
                    onClick: () => handleTabChange('Keluhan')
                },
                {
                    name: 'Saran',
                    href: '#',
                    active: activeTab === 'Saran',
                    onClick: () => handleTabChange('Saran')
                }
            ]" />
        </template>

        <!-- Use the new table-content slot instead of default slot -->
        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm h-full">
                    <Tabel :headers="tableHeaders" :items="feedbackItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 25" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || 0"
                        :filters="filterState" :sort-field="sortField" :sort-direction="sortDirection"
                        :white-background="true" empty-state-message="No feedbacks found"
                        @filter-changed="handleFilterChange" @sort="handleSort">

                        <template #['item.Judul']="{ item }">
                            <Feedbacks :name="item.nama_user" :title="item.judul" :description="item.isi"></Feedbacks>
                        </template>

                        <!-- Template for Status column -->
                        <template #['item.Status']="{ item }">
                            <Labels :type="item.status === 'Ditanggapi' ? 'ditanggapi' : 'menunggu'"
                                :text="item.status" />
                        </template>

                        <!-- Template for Date column with improved formatting -->
                        <template #['item.Tanggal']="{ item }">
                            <div class="flex flex-col">
                                <span
                                    class="text-[#404DBD] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ formatDate(item.created_at) }}
                                </span>
                            </div>
                        </template>
                        <!-- Template for Aksi column -->
                        <template #['item.Aksi']="{ item }">
                            <Link :href="route('dashboard.feedbacks.show', item.id)">
                            <PrimaryButton>
                                <template #icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="full" height="20" viewBox="0 0 20 20"
                                        fill="none">
                                        <mask id="mask0_1526_8437" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                                            x="0" y="0" width="20" height="20">
                                            <rect width="20" height="20" fill="#D9D9D9" />
                                        </mask>
                                        <g mask="url(#mask0_1526_8437)">
                                            <path
                                                d="M15.8333 15.8327V12.4993C15.8333 11.8049 15.5903 11.2146 15.1042 10.7285C14.6181 10.2424 14.0278 9.99935 13.3333 9.99935H5.6875L8.6875 12.9993L7.5 14.166L2.5 9.16602L7.5 4.16602L8.6875 5.33268L5.6875 8.33268H13.3333C14.4861 8.33268 15.4688 8.73893 16.2812 9.55143C17.0938 10.3639 17.5 11.3466 17.5 12.4993V15.8327H15.8333Z"
                                                fill="white" />
                                        </g>
                                    </svg>
                                </template>
                                balas
                            </PrimaryButton>
                            </Link>
                        </template>
                    </Tabel>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
