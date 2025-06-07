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
        width: '22%',  // Changed from '320px' to percentage
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
        width: '28%'  // Changed from '400px' to percentage
    },
    {
        label: 'Posisi',
        field: 'position.name',
        filterField: 'position_id',
        sortable: false,
        filterable: true,
        filterOptions: props.positions.map(pos => ({ id: pos.id, name: pos.name })),
        width: '21%'  // Changed from '300px' to percentage
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
        width: '19%'  // Changed from '264px' to percentage
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        width: '10%'  // Changed from '140px' to percentage
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
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M400-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18h14q6 0 12 2-8 18-13.5 37.5T404-360h-4q-71 0-127.5 18T180-306q-9 5-14.5 14t-5.5 20v32h252q6 21 16 41.5t22 38.5H80Zm560 40-12-60q-12-5-22.5-10.5T584-204l-58 18-40-68 46-40q-2-14-2-26t2-26l-46-40 40-68 58 18q11-8 21.5-13.5T628-460l12-60h80l12 60q12 5 22.5 11t21.5 15l58-20 40 70-46 40q2 12 2 25t-2 25l46 40-40 68-58-18q-11 8-21.5 13.5T732-180l-12 60h-80Zm40-120q33 0 56.5-23.5T760-320q0-33-23.5-56.5T680-400q-33 0-56.5 23.5T600-320q0 33 23.5 56.5T680-240ZM400-560q33 0 56.5-23.5T480-640q0-33-23.5-56.5T400-720q-33 0-56.5 23.5T320-640q0 33 23.5 56.5T400-560Zm0-80Zm12 400Z" />
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
            <TopButton>

            </TopButton>
            </Link>
        </template>

        <template #tab-devider>
            <TabDevider :tabs="[
                { name: 'Pengguna', href: route('dashboard.users.index'), active: true },
                { name: 'Posisi', href: route('dashboard.positions.index'), active: false },
            ]" />
        </template>

        <!-- Use the new table-content slot instead of default slot -->
        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm   h-full">
                    <Tabel :headers="tableHeaders" :items="userItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 25" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || 0"
                        :filters="filterState" :sort-field="sortField" :sort-direction="sortDirection"
                        empty-state-message="Tidak ada pengguna yang ditemukan" @filter-changed="handleFilterChange" @sort="handleSort">
                        <!-- Add slot templates for different columns -->
                        <!-- Add a template for the Kontak column -->
                        <template #['item.Kontak']="{ item }">
                            <div class="flex flex-col">
                                <span class="italic text-TableText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                                    {{ item.phone || '-' }}
                                </span>
                                <span class="italic text-emailText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                                    {{ item.email }}
                                </span>
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
                            <div class="flex justify-left gap-[7px] sm:gap-[7px] md:gap-[7px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px] items-center">
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
        </template>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Remove the dashboard-container styles since we're using the table-content slot now */
</style>
