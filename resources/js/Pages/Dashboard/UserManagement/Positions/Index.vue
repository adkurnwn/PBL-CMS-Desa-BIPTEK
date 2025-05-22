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
        width: '350px',
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
        width: '934px',
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
        width: '140px',
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="26" viewBox="0 0 25 26" fill="none">
                        <path
                            d="M12.4998 13.0003C10.804 13.0003 9.35227 12.3965 8.14463 11.1889C6.93699 9.98123 6.33317 8.52949 6.33317 6.83366C6.33317 5.13783 6.93699 3.68609 8.14463 2.47845C9.35227 1.27081 10.804 0.666992 12.4998 0.666992C14.1957 0.666992 15.6474 1.27081 16.855 2.47845C18.0627 3.68609 18.6665 5.13783 18.6665 6.83366C18.6665 8.52949 18.0627 9.98123 16.855 11.1889C15.6474 12.3965 14.1957 13.0003 12.4998 13.0003ZM0.166504 25.3337V21.017C0.166504 20.1434 0.39133 19.3404 0.840983 18.6081C1.29064 17.8758 1.88803 17.317 2.63317 16.9316C4.22623 16.135 5.84498 15.5377 7.48942 15.1394C9.13386 14.7411 10.804 14.542 12.4998 14.542C14.1957 14.542 15.8658 14.7411 17.5103 15.1394C19.1547 15.5377 20.7734 16.135 22.3665 16.9316C23.1116 17.317 23.709 17.8758 24.1587 18.6081C24.6083 19.3404 24.8332 20.1434 24.8332 21.017V25.3337H0.166504Z"
                            fill="currentColor" />
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
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg h-full">
                    <Tabel :headers="tableHeaders" :items="positionItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 25" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || 0"
                        :filters="filterState" :sort-field="sortField" :sort-direction="sortDirection"
                        empty-state-message="No positions found" @page-changed="handlePageChanged" @sort="handleSort">
                        <!-- Custom template for job description column -->
                        <template #['item.Deskripsi_Pekerjaan']="{ item }">
                            <div>
                                <ul class="list-disc ml-5">
                                    <template
                                        v-for="(desc, index) in parseJobDescription(item.job_description).slice(0, 2)"
                                        :key="index">
                                        <li>{{ desc }}</li>
                                    </template>
                                    <li v-if="parseJobDescription(item.job_description).length > 2"
                                        class="text-menuText cursor-pointer mt-1 list-none">
                                        <Link :href="route('dashboard.positions.edit', item.id)">
                                        Lihat Selengkapnya
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </template>

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
