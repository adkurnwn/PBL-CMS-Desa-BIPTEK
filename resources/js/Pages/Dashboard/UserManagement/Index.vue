<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import TabDevider from "@/Components/DashboardComponent/TabDevider.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";
import Labels from "@/Components/DashboardComponent/Labels.vue";
import SwitchToggle from "@/Components/DashboardComponent/SwitchToggle.vue";
import DropdownFilter from "@/Components/DashboardComponent/DropdownFilter.vue";
import IconAksi from "@/Components/DashboardComponent/IconAksi.vue";

const props = defineProps({
    users: Object,
    flash: Object,
    search: {
        type: String,
        default: ''
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    positions: {
        type: Array,
        default: () => []
    },
    // Add props for sorting
    sort: {
        type: String,
        default: 'name'
    },
    direction: {
        type: String,
        default: 'asc'
    }
});

// Extract pagination info from the users object
const pagination = computed(() => {
    if (!props.users) return null;

    return {
        currentPage: props.users.current_page,
        lastPage: props.users.last_page,
        perPage: props.users.per_page,
        total: props.users.total,
    };
});

// Extract the user data from the paginated response
const userItems = computed(() => {
    const users = props.users?.data || [];
    return users.map(user => ({
        ...user,
        avatarUrl: getAvatarUrl(user)
    }));
});

const form = useForm({});
// Update searchQuery to use the prop
const searchQuery = ref(props.search || '');

// Add filter state
const filterState = ref(props.filters || {});

// Add sort state
const sortField = ref(props.sort || 'name');
const sortDirection = ref(props.direction || 'asc');

// Table headers with filter configuration
const tableHeaders = [
    {
        label: 'Pengguna',
        field: 'name',
        sortable: true,
        nowrap: true,
        width: '320px',
        style: {
            fontSize: '18px',
            fontStyle: 'normal',
            fontWeight: '400',
            lineHeight: 'normal'
        },
        avatar: {
            field: 'avatarUrl',
            size: 24,
            gap: 12,
            fallback: 'initials'
        }
    },
    {
        label: 'Kontak',
        field: 'email',
        sortable: false,
        width: '400px'
    },
    {
        label: 'Posisi',
        field: 'position.name',
        filterField: 'position_id',
        sortable: false,
        filterable: true,
        filterOptions: props.positions.map(pos => ({ id: pos.id, name: pos.name })),
        width: '300px'
    },
    {
        label: 'Status',
        field: 'status',
        sortable: false,
        filterable: true,
        filterOptions: [
            { id: 1, name: 'Aktif' },
            { id: 0, name: 'Nonaktif' }
        ],
        width: '264px'
    },
    {
        label: 'Aksi', // Changed from Actions to Aksi
        field: 'actions',
        sortable: false,
        width: '140px'
    },
];

// Implement search functionality
const handleSearch = (query) => {
    router.get(route('dashboard.users.index'), {
        search: query
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['users']
    });
};

// Unified filter fields configuration for user management
const filterFields = computed(() => [
    {
        id: 'position_id',
        name: 'Posisi',
        options: props.positions.map(pos => ({ id: pos.id, name: pos.name }))
    },
    {
        id: 'status',
        name: 'Status',
        options: [
            { id: 1, name: 'Aktif' },
            { id: 0, name: 'Nonaktif' }
        ]
    }
    // More filter fields can be added here for future use
]);

// Handle filter changes from the unified dropdown filter
const handleFilterChange = (newFilters) => {
    filterState.value = newFilters;

    router.get(route('dashboard.users.index'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['users']
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

    // Navigate with updated sort params
    router.get(route('dashboard.users.index'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['users']
    });
};

const toggleUserStatus = (userId) => {
    form.delete(route("dashboard.users.destroy", userId), {
        preserveScroll: true,
    });
};

const getAvatarUrl = (user) => {
    if (!user || !user.photo_profile) return null;
    return `/storage/${user.photo_profile}`;
};

// Watch for external search changes
watch(() => props.search, (newValue) => {
    if (newValue !== searchQuery.value) {
        searchQuery.value = newValue;
    }
}, { immediate: true });

// Watch for external filter changes
watch(() => props.filters, (newValue) => {
    if (JSON.stringify(newValue) !== JSON.stringify(filterState.value)) {
        filterState.value = { ...newValue };
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

    <Head title="Manajemen Pengguna" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Manajemen Pengguna" :hasRightContent="false">
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
            <Search v-model="searchQuery" placeholder="Cari pengguna..." @search="handleSearch" />
            <DropdownFilter placeholder="Filter..." :filter-fields="filterFields" v-model:filters="filterState"
                @filter-changed="handleFilterChange" width="250px" dropdownTitle="Filter berdasarkan"
                activeFiltersTitle="Filter yang aktif" :close-on-select="false" />
            <Link :href="route('dashboard.users.create')">
            <TopButton :icon="true">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                        <mask id="mask0_1642_3445" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="21" height="20">
                            <rect x="0.5" width="20" height="20" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1642_3445)">
                            <path
                                d="M9.6665 10.8337H4.6665V9.16699H9.6665V4.16699H11.3332V9.16699H16.3332V10.8337H11.3332V15.8337H9.6665V10.8337Z"
                                fill="white" />
                        </g>
                    </svg>
                </template>
                Create
            </TopButton>
            </Link>
        </template>

        <template #tab-devider>
            <TabDevider :tabs="[
                { name: 'Pengguna', href: route('dashboard.users.index'), active: true },
                { name: 'Posisi', href: route('dashboard.positions.index'), active: false },
            ]" />
        </template>

        <div class="dashboard-container">
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg h-full">
                    <Tabel :headers="tableHeaders" :items="userItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 25" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || 0"
                        :filters="filterState" :sort-field="sortField" :sort-direction="sortDirection"
                        empty-state-message="No users found" @filter-changed="handleFilterChange" @sort="handleSort">
                        <!-- Add a template for the Kontak column -->
                        <template #['item.Kontak']="{ item }">
                            <div class="flex flex-col">
                                <span class="text-[18px] italic text-TableText">{{ item.phone || '-' }}</span>
                                <span class="italic text-[18px] text-emailText">{{ item.email }}</span>
                            </div>
                        </template>

                        <!-- Fixed Position column template to match the header label 'Posisi' -->
                        <template #['item.Posisi']="{ item }">
                            <Labels v-if="item.position"
                                :type="item.position.name.toLowerCase().includes('kepala') ? 'kepala desa' :
                                    item.position.name.toLowerCase().includes('sekretaris') ? 'sekretaris desa' :
                                        item.position.name.toLowerCase().includes('bendahara') ? 'bendahara desa' : 'other'"
                                :text="item.position.name" />
                            <span v-else>-</span>
                        </template>

                        <!-- Fix the slot syntax for Status column -->
                        <template #['item.Status']="{ item }">
                            <div @click.stop="toggleUserStatus(item.id)" class="cursor-pointer">
                                <SwitchToggle :type="item.status" :text="item.status ? 'Aktif' : 'Nonaktif'" />
                            </div>
                        </template>

                        <!-- Fixed Actions column template to match the header label 'Aksi' -->
                        <template #['item.Aksi']="{ item }">
                            <div class="flex justify-left">
                                <Link :href="route('dashboard.users.edit', item.id)">
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
        </div>
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
