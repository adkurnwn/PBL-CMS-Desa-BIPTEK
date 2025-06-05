<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted, computed, watch } from "vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import Labels from "@/Components/DashboardComponent/Labels.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import TabDevider from "@/Components/DashboardComponent/TabDevider.vue";

const props = defineProps({
    logs: Object,
    currentPage: {
        type: Number,
        default: 1
    },
    sortField: {
        type: String,
        default: 'time'
    },
    sortDirection: {
        type: String,
        default: 'desc'
    },
    search: {
        type: String,
        default: ''
    }
});

// Extract the logs data from the paginated response
const activityLogs = computed(() => props.logs?.data || []);

// Extract pagination info with proper error handling
const pagination = computed(() => {
    if (!props.logs) return null;

    const paginationData = {
        currentPage: props.logs.current_page || props.currentPage || 1,
        lastPage: props.logs.last_page || 1,
        perPage: props.logs.per_page || 25,
        total: props.logs.total || 0,
    };

    return paginationData;
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Function to get avatar URL from photo_profile
const getAvatarUrl = (user) => {
    if (!user || !user.photo_profile) return null;
    return `/storage/${user.photo_profile}`;
};

// Table headers configuration with percentage-based widths for better responsiveness
const tableHeaders = [
    {
        label: 'Pengguna',
        field: 'user',
        sortable: false,
        nowrap: true,
        width: '20%',  // Changed from '270px' to percentage
        style: {
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
        label: 'Aksi',
        field: 'action',
        sortable: false,
        nowrap: true,
        width: '12%',  // Changed from '190px' to percentage
    },
    {
        label: 'Modul',
        field: 'module',
        sortable: false,
        nowrap: true,
        width: '14%',  // Changed from '250px' to percentage
        transform: 'capitalize',
        truncate: true,
    },
    {
        label: 'Deskripsi',
        field: 'description',
        sortable: false,
        nowrap: false,
        truncate: false,
        multiline: true,
        width: '36%',  // Changed from minWidth: '450px' to percentage width
    },
    {
        label: 'Waktu',
        field: 'time',
        sortable: true,
        nowrap: true,
        width: '18%',  // Changed from '264px' to percentage
    },
];

// Transform logs to match the table format - remove capitalization here since we're doing it in the table
const tableItems = computed(() => {
    return activityLogs.value.map(log => ({
        user: log.user?.name || "System",
        // Create avatarUrl directly without using accessor
        avatarUrl: getAvatarUrl(log.user),
        action: log.action,
        // No need to capitalize here anymore, using the transform property instead
        module: log.module || "",
        description: log.description,
        time: log.created_at,
        // Keep the original data for reference
        raw: log
    }));
});

// Update search functionality
const searchQuery = ref(props.search || '');

const handleSearch = (query) => {
    // Reset to first page when searching
    router.get(route('dashboard.activity-logs'), {
        search: query,
        sort: props.sortField,
        direction: props.sortDirection
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['logs', 'search', 'currentPage']
    });
};

// Watch for external search changes (from props)
watch(() => props.search, (newValue) => {
    if (newValue !== searchQuery.value) {
        searchQuery.value = newValue;
    }
}, { immediate: true });

// Handle pagination changes - simplified and fixed
const handlePageChanged = (newPage) => {
    const routeParams = {
        page: newPage,
        direction: props.sortDirection
    };

    // Add search parameter if it exists
    if (searchQuery.value) {
        routeParams.search = searchQuery.value;
    }

    // Use the same route for all pages, Laravel will handle the page parameter
    router.get(route('dashboard.activity-logs'), routeParams, {
        preserveScroll: true,
        preserveState: true,
        only: ['logs', 'currentPage']
    });
};

// Handle sorting - fixed to maintain page state
const handleSort = (field) => {
    // Only allow sorting on time field
    if (field !== 'time') return;

    // Toggle sort direction
    const sortDirection = props.sortDirection === 'asc' ? 'desc' : 'asc';

    // Build route params
    const routeParams = {
        direction: sortDirection,
        page: 1 // Reset to first page when sorting
    };

    if (searchQuery.value) {
        routeParams.search = searchQuery.value;
    }

    router.get(route('dashboard.activity-logs'), routeParams, {
        preserveScroll: true,
        preserveState: true,
        only: ['logs', 'sortDirection', 'currentPage']
    });
};

// Add a console log to verify pagination data
watch(() => props.logs, (newLogs) => {
    if (newLogs) {
        console.log('Pagination Data:', {
            currentPage: newLogs.current_page,
            lastPage: newLogs.last_page,
            perPage: newLogs.per_page,
            total: newLogs.total
        });
    }
}, { immediate: true });

const page = usePage();
const auth = computed(() => page.props.auth);
const hasPermission = (permission) => {
    // Always show for admin users
    if (auth.value.user?.roles?.some(role => role.name.toLowerCase() === 'admin')) {
        return true;
    }
    // Check if user has the specific permission
    return auth.value.user?.permissions?.includes(permission);
};

// Add redirect check if user doesn't have permission
onMounted(() => {
    // If user doesn't have activity logs permission
    if (!hasPermission('manage_activity-logs')) {
        // Redirect to statistics if they have that permission, otherwise to dashboard
        if (hasPermission('manage_statistics')) {
            router.get(route('dashboard.statistics'));
        } else {
            router.get(route('dashboard'));
        }
    }
});
</script>

<template>

    <Head title="Aktivitas Terbaru" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Aktivitas Terbaru" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="41" viewBox="0 0 40 41" fill="none">
                        <mask id="mask0_1244_5243" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="40" height="41">
                            <rect y="0.5" width="40" height="40" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1244_5243)">
                            <path
                                d="M24.9996 33.8317C21.2774 33.8317 18.1246 32.54 15.5413 29.9567C12.9579 27.3734 11.6663 24.2206 11.6663 20.4984C11.6663 16.8039 12.9579 13.6581 15.5413 11.0609C18.1246 8.46365 21.2774 7.16504 24.9996 7.16504C28.694 7.16504 31.8399 8.46365 34.4371 11.0609C37.0343 13.6581 38.3329 16.8039 38.3329 20.4984C38.3329 24.2206 37.0343 27.3734 34.4371 29.9567C31.8399 32.54 28.694 33.8317 24.9996 33.8317ZM24.9996 30.4984C27.7774 30.4984 30.1385 29.5261 32.0829 27.5817C34.0274 25.6373 34.9996 23.2761 34.9996 20.4984C34.9996 17.7206 34.0274 15.3595 32.0829 13.415C30.1385 11.4706 27.7774 10.4984 24.9996 10.4984C22.2218 10.4984 19.8607 11.4706 17.9163 13.415C15.9718 15.3595 14.9996 17.7206 14.9996 20.4984C14.9996 23.2761 15.9718 25.6373 17.9163 27.5817C19.8607 29.5261 22.2218 30.4984 24.9996 30.4984ZM28.7913 26.7067L31.1663 24.3317L26.6663 19.8317V13.8317H23.3329V21.2067L28.7913 26.7067ZM3.33293 15.4984V12.165H9.99959V15.4984H3.33293ZM1.66626 22.165V18.8317H9.99959V22.165H1.66626ZM3.33293 28.8317V25.4984H9.99959V28.8317H3.33293Z"
                                fill="#4C7660" />
                        </g>
                    </svg>
                </template>
                <template #right-content>
                </template>
            </HeadingMenu>
        </template>

        <template #tab-devider>
            <TabDevider 
                :tabs="[
                    { name: 'Home', href: route('dashboard'), active: false },
                    ...(hasPermission('manage_activity-logs') ? [{ name: 'Aktivitas Terbaru', href: route('dashboard.activity-logs'), active: true }] : []),
                    ...(hasPermission('manage_statistics') ? [{ name: 'Statistik', href: route('dashboard.statistics'), active: false }] : [])
                ]" />
        </template>

        <!-- Add the search component in the search-area slot -->
        <template #search-area>
            <Search v-model="searchQuery" placeholder="Cari aktivitas..." @search="handleSearch" />
        </template>

        <!-- Use the table-content slot -->
        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm h-full">
                    <div class="h-full">
                        <Tabel :headers="tableHeaders" :items="tableItems" :pagination="true"
                            :items-per-page="pagination?.perPage || 25" :total-items="pagination?.total || 0"
                            empty-state-message="Log aktivitas tidak ditemukan."
                            :current-page="pagination?.currentPage || 1" :total-pages="pagination?.lastPage || 1"
                            @page-changed="handlePageChanged" @sort="handleSort" :sort-field="sortField"
                            :sort-direction="sortDirection">

                            <!-- Action column customization -->
                            <template #item.Aksi="{ item }">
                                <Labels
                                    :type="['create', 'update', 'delete', 'login', 'logout', 'edit'].includes(item.action) ? item.action : 'other'"
                                    :text="item.action" />
                            </template>
                            <template #item.Modul="{ item }">
                                <div class="italic truncate">
                                    {{ (item.module).toUpperCase() }}
                                </div>
                            </template>
                            <template #item.Deskripsi="{ item }">
                                <div class="italic">
                                    {{ item.description }}
                                </div>
                            </template>

                            <!-- Time column customization -->
                            <template #item.Waktu="{ item }">
                                <div
                                    class="text-[#404DBD] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ formatDate(item.time) }}
                                </div>
                            </template>
                        </Tabel>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Remove the dashboard-container styles since we're using the table-content slot now */
</style>
