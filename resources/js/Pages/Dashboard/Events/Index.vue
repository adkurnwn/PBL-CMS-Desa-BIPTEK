<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import DropdownFilter from "@/Components/DashboardComponent/DropdownFilter.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import IconAksi from "@/Components/DashboardComponent/IconAksi.vue";
import Labels from "@/Components/DashboardComponent/Labels.vue";
import { ref, computed, watch } from "vue";

const props = defineProps({
    events: Object,
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
        label: 'Nama Kegiatan',
        field: 'title',
        sortable: true,
        width: '25%',
        truncate: true,
    },
    {
        label: 'Tanggal',
        field: 'date',
        sortable: true,
        width: '15%'
    },
    {
        label: 'Lokasi',
        field: 'location',
        sortable: true,
        width: '15%',
        truncate: true,
    },
    {
        label: 'Status',
        field: 'status',
        sortable: false,
        width: '15%'
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
        id: 'date_range',
        name: 'Status',
        options: [
            { id: 'future', name: 'Mendatang' },
            { id: 'today', name: 'Berlangsung' },
            { id: 'past', name: 'Berakhir' }
        ]
    },
]);


// Improved format date function to handle 'YYYY-MM-DD' format from database
const formatDate = (dateString) => {
    if (!dateString) return '-';
    
    try {
        const date = new Date(dateString);
        
        // Check if date is valid
        if (isNaN(date.getTime())) {
            return dateString; // Return original if not a valid date
        }
        
        // Format as DD/MM/YYYY
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        
        return `${day}/${month}/${year}`;
    } catch (error) {
        console.error('Error formatting date:', error);
        return dateString; // Return original on error
    }
};

// Determine event status based on date
const getEventStatus = (dateString) => {
    if (!dateString) return 'mendatang';
    
    try {
        const eventDate = new Date(dateString);
        const today = new Date();
        
        // Reset time portion for accurate date comparison
        eventDate.setHours(0, 0, 0, 0);
        today.setHours(0, 0, 0, 0);
        
        // Calculate difference in days
        const diffTime = eventDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 0) return 'berakhir';
        if (diffDays === 0) return 'berlangsung';
        return 'mendatang';
    } catch (error) {
        console.error('Error determining event status:', error);
        return 'mendatang';
    }
};

// Process event items for display
const eventItems = computed(() => {
    try {
        const items = props.events?.data || props.events || [];
        return items.map(item => {
            const dateField = item.date || item.event_date;
            return {
                ...item,
                formattedDate: formatDate(dateField),
                status: getEventStatus(dateField)
            };
        });
    } catch (error) {
        console.error('Error processing items:', error);
        return [];
    }
});

// Extract pagination info from the events object
const pagination = computed(() => {
    if (!props.events || !props.events.current_page) return null;

    return {
        currentPage: props.events.current_page,
        lastPage: props.events.last_page,
        perPage: props.events.per_page,
        total: props.events.total,
    };
});

// Handle search functionality
const handleSearch = (query) => {
    router.get(route('dashboard.events.index'), {
        search: query,
        filters: filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['events']
    });
};

// Handle filter changes
const handleFilterChange = (newFilters) => {
    filterState.value = newFilters;
    
    router.get(route('dashboard.events.index'), {
        search: searchQuery.value,
        filters: newFilters,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['events']
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
    router.get(route('dashboard.events.index'), {
        search: searchQuery.value,
        filters: filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['events']
    });
};

// Handle page change
const handlePageChange = (page) => {
    router.get(route('dashboard.events.index'), {
        search: searchQuery.value,
        filters: filterState.value,
        sort: sortField.value,
        direction: sortDirection.value,
        page: page,
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['events']
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
}, { deep: true, immediate: true });
</script>

<template>
    <Head title="Event" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Agenda">
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
            <Search v-model="searchQuery" placeholder="Cari agenda..." @search="handleSearch" />
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
            <Link :href="route('dashboard.events.create')">
                <TopButton></TopButton>
            </Link>
        </template>

        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm h-full">
                    <Tabel :headers="tableHeaders" :items="eventItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 10" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || eventItems.length"
                        :filters="filterState" :sort-field="sortField" :sort-direction="sortDirection"
                        empty-state-message="No events found. Create a new event to get started."
                        @sort="handleSort" @filter-changed="handleFilterChange" @page-changed="handlePageChange">
                        
                        <!-- Template for Nama Kegiatan column -->
                        <template #['item.Nama_Kegiatan']="{ item }">
                            <div class="flex flex-col">
                                <span class="truncate-text-cell text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ item.title }}
                                </span>
                            </div>
                        </template>

                        <!-- Template for Tanggal column -->
                        <template #['item.Tanggal']="{ item }">
                            <div class="flex flex-col">
                                <span class="text-[#404DBD] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ item.formattedDate }}
                                </span>
                            </div>
                        </template>

                        <!-- Template for Status column using Labels component -->
                        <template #['item.Status']="{ item }">
                            <div class="items-center justify-left">
                                <Labels 
                                    :type="item.status.toLowerCase()" 
                                    :text="item.status.charAt(0).toUpperCase() + item.status.slice(1)"
                                />
                            </div>
                        </template>

                        <!-- Template for actions column -->
                        <template #['item.Aksi']="{ item }">
                            <div class="flex justify-left gap-[7px] sm:gap-[7px] md:gap-[7px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px] items-center">
                                <Link v-if="item.status.toLowerCase() == 'mendatang'" :href="route('dashboard.events.edit', item.slug)">
                                    <button class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
                                        <IconAksi type="edit" />
                                    </button>
                                </Link>
                                <Link v-if="item.status.toLowerCase() == 'berlangsung' || item.status.toLowerCase() == 'berakhir'" :href="route('dashboard.events.show', item.slug)">
                                    <button class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
                                        <IconAksi type="view" />
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
/* Add any specific styles here */
</style>
