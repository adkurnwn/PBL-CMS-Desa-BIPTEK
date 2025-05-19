<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const props = defineProps({
    logs: Array,
});

const activityLogs = computed(() => props.logs || []);

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Table headers configuration with custom styling for Pengguna column and avatar
const tableHeaders = [
    {
        label: 'Pengguna',
        field: 'user',
        sortable: true,
        nowrap: true,
        width: '270px',
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
        label: 'Aksi',
        field: 'action',
        sortable: true,
        nowrap: true,
        width: '190px'
    },
    {
        label: 'Modul',
        field: 'module',
        sortable: true,
        nowrap: true,
        width: '250px'
    },
    {
        label: 'Deskripsi',
        field: 'description',
        sortable: true,
        nowrap: false,
        minWidth: '450px',
        // No fixed width to allow it to take remaining space
    },
    {
        label: 'Waktu',
        field: 'time',
        sortable: true,
        nowrap: true,
        width: '264px',
        style: {
            fontSize: '18px',
            fontStyle: 'normal',
            fontWeight: '400',
            lineHeight: 'normal',
            color: '#6B86B5'
        }
    },
];

// Transform logs to match the table format
const tableItems = computed(() => {
    return activityLogs.value.map(log => ({
        user: log.user?.name || "System",
        // Add avatarUrl field for user profile picture
        avatarUrl: log.user?.avatar_url || null,
        action: log.action,
        module: log.module,
        description: log.description,
        time: log.created_at,
        // Keep the original data for reference
        raw: log
    }));
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

        <!-- Disable scrollbar from parent by adding special class -->
        <!-- Add height constraint to container to ensure all elements fit -->
        <div class="dashboard-container">
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg h-full">
                    <div class="text-gray-900 h-full">
                        <Tabel :headers="tableHeaders" :items="tableItems" :pagination="true" :items-per-page="25"
                            empty-state-message="Log aktivitas tidak ditemukan.">
                            <!-- Action column customization -->
                            <template #item.Aksi="{ item }">
                                <span class="px-2 py-1 text-xs rounded-full" :class="{
                                    'bg-green-100 text-green-800': item.action === 'create',
                                    'bg-blue-100 text-blue-800': item.action === 'update',
                                    'bg-red-100 text-red-800': item.action === 'delete',
                                    'bg-gray-100 text-gray-800': !['create', 'update', 'delete'].includes(item.action),
                                }">
                                    {{ item.action }}
                                </span>
                            </template>

                            <!-- Time column customization -->
                            <template #item.Waktu="{ item }">
                                <div class="text-[#6B86B5] text-[18px] font-normal">
                                    {{ formatDate(item.time) }}
                                </div>
                            </template>
                        </Tabel>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Dashboard specific styles to manage scrolling */
.dashboard-container {
    position: relative;
    height: calc(100vh - 304px);
    /* Adjusted to provide more space for the table */
    min-height: 650px;
    /* Increased by 30% from 500px to match the new table height */
    overflow: hidden;
}
</style>
