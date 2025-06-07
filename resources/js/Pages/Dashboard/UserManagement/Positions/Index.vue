<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import TabDevider from "@/Components/DashboardComponent/TabDevider.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import { ref, computed, watch } from "vue";
import IconAksi from "@/Components/DashboardComponent/IconAksi.vue";

const props = defineProps({
    positions: Object,
    flash: Object,
    sort: {
        type: String,
        default: "name",
    },
    search: {
        type: String,
        default: "",
    },
    direction: {
        type: String,
        default: "asc",
    },
});

// Extract pagination info from the positions object
const pagination = computed(() => {
    if (!props.positions) return null;

    return {
        currentPage: props.positions.current_page,
        lastPage: props.positions.last_page,
        perPage: props.positions.per_page,
        total: props.positions.total,
    };
});

// Extract the position data from the paginated response
const positionItems = computed(() => {
    return props.positions?.data || [];
});

const searchQuery = ref(props.search || '');
const sortField = ref(props.sort || 'name');
const sortDirection = ref(props.direction || 'asc');
const filterState = ref({});

const form = useForm({});

const tableHeaders = [
    {
        label: 'Nama Posisi',
        field: 'name',
        sortable: true,
        filterable: true,
        width: '25%',  // Changed from '350px' to percentage
        style: {
            fontSize: '18px',
            fontStyle: 'normal',
            fontWeight: '400',
            lineHeight: 'normal'
        },
    },
    {
        label: 'Deskripsi Pekerjaan',
        field: 'job_description',
        sortable: false,
        filterable: true,
        width: '65%',  // Changed from '934px' to percentage
        style: {
            fontSize: '18px',
            fontStyle: 'normal',
            fontWeight: '400',
            lineHeight: 'normal'
        },
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        filterable: false,
        width: '10%',  // Changed from '140px' to percentage
    }
];

// Delete position function
const deletePosition = (positionId) => {
    if (confirm("Are you sure you want to delete this position?")) {
        form.delete(route("dashboard.positions.destroy", positionId), {
            preserveScroll: true,
        });
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

// Handle search functionality
const handleSearch = () => {
    router.get(route('dashboard.positions.index'), {
        search: searchQuery.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['positions']
    });
};

// Handle page change
const handlePageChanged = (page) => {
    router.get(route('dashboard.positions.index'), {
        page,
        search: searchQuery.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['positions']
    });
};

// Handle sort
const handleSort = (field) => {
    // If clicking on the same field, toggle direction
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        // New field, default to ascending
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    router.get(route('dashboard.positions.index'), {
        search: searchQuery.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['positions']
    });
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
</script>

<template>

    <Head title="Manajemen Posisi" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Manajemen Posisi" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h168q14-36 44-58t68-22q38 0 68 22t44 58h168q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm280-670q13 0 21.5-8.5T510-820q0-13-8.5-21.5T480-850q-13 0-21.5 8.5T450-820q0 13 8.5 21.5T480-790ZM200-246q54-53 125.5-83.5T480-360q83 0 154.5 30.5T760-246v-514H200v514Zm280-194q58 0 99-41t41-99q0-58-41-99t-99-41q-58 0-99 41t-41 99q0 58 41 99t99 41ZM280-200h400v-10q-42-35-93-52.5T480-280q-56 0-107 17.5T280-210v10Zm200-320q-25 0-42.5-17.5T420-580q0-25 17.5-42.5T480-640q25 0 42.5 17.5T540-580q0 25-17.5 42.5T480-520Zm0 17Z" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Search v-model="searchQuery" placeholder="Cari posisi..." @search="handleSearch" />
            <Link :href="route('dashboard.positions.create')">
            <TopButton>

            </TopButton>
            </Link>
        </template>

        <template #tab-devider>
            <TabDevider :tabs="[
                { name: 'Pengguna', href: route('dashboard.users.index'), active: false },
                { name: 'Posisi', href: route('dashboard.positions.index'), active: true },
            ]" />
        </template>

        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm   h-full">
                    <Tabel :headers="tableHeaders" :items="positionItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 25" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || 0"
                        :filters="filterState" :sort-field="sortField" :sort-direction="sortDirection"
                        empty-state-message="Tidak ada posisi yang ditemukan" @page-changed="handlePageChanged" @sort="handleSort">
                        
                        <!-- Custom template for actions column -->
                        <template #['item.Aksi']="{ item }">
                            <div class="flex justify-left gap-[10px]">
                                <Link :href="route('dashboard.positions.edit', item.id)" class="">
                                <IconAksi type="edit" />
                                </Link>

                                <button @click="deletePosition(item.id)" class=""
                                    :disabled="['Admin Sistem', 'Kepala Desa'].includes(item.name)">
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

<style scoped>
/* Dashboard specific styles to manage scrolling */
.dashboard-container {
    position: relative;
    height: calc(100vh - 348px);
    min-height: 650px;
    overflow: hidden;
}
</style>
