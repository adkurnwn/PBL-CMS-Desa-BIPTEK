<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";

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

// Add sort state tracking
const sortColumn = ref(null);
const sortDirection = ref('asc');

// Function to handle sorting
const sortBy = (column) => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column;
        sortDirection.value = 'asc';
    }
    
    // Here you would typically sort the data
    // For now we'll just track the state for the UI
};

// Helper to determine icon rotation class
const getSortIconClass = (column) => {
    if (sortColumn.value !== column) return '';
    return sortDirection.value === 'desc' ? 'rotate-180' : '';
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #page-icon>
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 41" class="text-selectedText">
                
                <g mask="url(#mask0_1244_5243)">
                    <path d="M24.9996 33.8317C21.2774 33.8317 18.1246 32.54 15.5413 29.9567C12.9579 27.3734 11.6663 24.2206 11.6663 20.4984C11.6663 16.8039 12.9579 13.6581 15.5413 11.0609C18.1246 8.46365 21.2774 7.16504 24.9996 7.16504C28.694 7.16504 31.8399 8.46365 34.4371 11.0609C37.0343 13.6581 38.3329 16.8039 38.3329 20.4984C38.3329 24.2206 37.0343 27.3734 34.4371 29.9567C31.8399 32.54 28.694 33.8317 24.9996 33.8317ZM24.9996 30.4984C27.7774 30.4984 30.1385 29.5261 32.0829 27.5817C34.0274 25.6373 34.9996 23.2761 34.9996 20.4984C34.9996 17.7206 34.0274 15.3595 32.0829 13.415C30.1385 11.4706 27.7774 10.4984 24.9996 10.4984C22.2218 10.4984 19.8607 11.4706 17.9163 13.415C15.9718 15.3595 14.9996 17.7206 14.9996 20.4984C14.9996 23.2761 15.9718 25.6373 17.9163 27.5817C19.8607 29.5261 22.2218 30.4984 24.9996 30.4984ZM28.7913 26.7067L31.1663 24.3317L26.6663 19.8317V13.8317H23.3329V21.2067L28.7913 26.7067ZM3.33293 15.4984V12.165H9.99959V15.4984H3.33293ZM1.66626 22.165V18.8317H9.99959V22.165H1.66626ZM3.33293 28.8317V25.4984H9.99959V28.8317H3.33293Z" fill="currentColor"/>
                </g>
            </svg>
        </template>
        
        <template #header>
            <h2 class="text-[37px] font-semibold leading-tight text-selectedText">
            Aktivitas Terbaru
            </h2>
        </template>

        <div class="">
            <div class="">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="text-gray-900">

                        <!-- Empty state -->
                        <div
                            v-if="activityLogs.length === 0"
                            class="text-gray-500 text-center py-4"
                        >
                            Log aktivitas tidak ditemukan.
                        </div>

                        <!-- Activity logs table with fixed header -->
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-TopTableBG sticky top-0 z-10">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-[22px] font-medium text-gray-900 tracking-normal cursor-pointer"
                                            @click="sortBy('user')"
                                        >
                                            <div class="flex items-center">
                                                <span>Pengguna</span>
                                                <svg 
                                                    xmlns="http://www.w3.org/2000/svg" 
                                                    width="24" 
                                                    height="24" 
                                                    viewBox="0 0 24 25" 
                                                    fill="none"
                                                    class="ml-2 transform transition-transform duration-200"
                                                    :class="getSortIconClass('user')"
                                                >
                                                    <mask id="mask0_1249_5047" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="25">
                                                        <rect y="0.5" width="24" height="24" fill="#D9D9D9"/>
                                                    </mask>
                                                    <g mask="url(#mask0_1249_5047)">
                                                        <path d="M7 14.5L12 9.5L17 14.5H7Z" fill="#111827"/>
                                                    </g>
                                                </svg>
                                            </div>
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-[22px] font-medium text-gray-900 tracking-normal cursor-pointer"
                                            @click="sortBy('action')"
                                        >
                                            <div class="flex items-center">
                                                <span>Aksi</span>
                                                <svg 
                                                    xmlns="http://www.w3.org/2000/svg" 
                                                    width="24" 
                                                    height="24" 
                                                    viewBox="0 0 24 25" 
                                                    fill="none"
                                                    class="ml-2 transform transition-transform duration-200"
                                                    :class="getSortIconClass('action')"
                                                >
                                                    <mask id="mask0_1249_5047" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="25">
                                                        <rect y="0.5" width="24" height="24" fill="#D9D9D9"/>
                                                    </mask>
                                                    <g mask="url(#mask0_1249_5047)">
                                                        <path d="M7 14.5L12 9.5L17 14.5H7Z" fill="#111827"/>
                                                    </g>
                                                </svg>
                                            </div>
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-[22px] font-medium text-gray-900 tracking-normal cursor-pointer"
                                            @click="sortBy('module')"
                                        >
                                            <div class="flex items-center">
                                                <span>Modul</span>
                                                <svg 
                                                    xmlns="http://www.w3.org/2000/svg" 
                                                    width="24" 
                                                    height="24" 
                                                    viewBox="0 0 24 25" 
                                                    fill="none"
                                                    class="ml-2 transform transition-transform duration-200"
                                                    :class="getSortIconClass('module')"
                                                >
                                                    <mask id="mask0_1249_5047" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="25">
                                                        <rect y="0.5" width="24" height="24" fill="#D9D9D9"/>
                                                    </mask>
                                                    <g mask="url(#mask0_1249_5047)">
                                                        <path d="M7 14.5L12 9.5L17 14.5H7Z" fill="#111827"/>
                                                    </g>
                                                </svg>
                                            </div>
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-[22px] font-medium text-gray-900 tracking-normal cursor-pointer"
                                            @click="sortBy('description')"
                                        >
                                            <div class="flex items-center">
                                                <span>Deskripsi</span>
                                                <svg 
                                                    xmlns="http://www.w3.org/2000/svg" 
                                                    width="24" 
                                                    height="24" 
                                                    viewBox="0 0 24 25" 
                                                    fill="none"
                                                    class="ml-2 transform transition-transform duration-200"
                                                    :class="getSortIconClass('description')"
                                                >
                                                    <mask id="mask0_1249_5047" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="25">
                                                        <rect y="0.5" width="24" height="24" fill="#D9D9D9"/>
                                                    </mask>
                                                    <g mask="url(#mask0_1249_5047)">
                                                        <path d="M7 14.5L12 9.5L17 14.5H7Z" fill="#111827"/>
                                                    </g>
                                                </svg>
                                            </div>
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-[22px] font-medium text-gray-900 tracking-normal cursor-pointer"
                                            @click="sortBy('time')"
                                        >
                                            <div class="flex items-center">
                                                <span>Waktu</span>
                                                <svg 
                                                    xmlns="http://www.w3.org/2000/svg" 
                                                    width="24" 
                                                    height="24" 
                                                    viewBox="0 0 24 25" 
                                                    fill="none"
                                                    class="ml-2 transform transition-transform duration-200"
                                                    :class="getSortIconClass('time')"
                                                >
                                                    <mask id="mask0_1249_5047" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="25">
                                                        <rect y="0.5" width="24" height="24" fill="#D9D9D9"/>
                                                    </mask>
                                                    <g mask="url(#mask0_1249_5047)">
                                                        <path d="M7 14.5L12 9.5L17 14.5H7Z" fill="#111827"/>
                                                    </g>
                                                </svg>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr
                                        v-for="log in activityLogs"
                                        :key="log.id"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ log.user?.name || "System" }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full"
                                                :class="{
                                                    'bg-green-100 text-green-800':
                                                        log.action === 'create',
                                                    'bg-blue-100 text-blue-800':
                                                        log.action === 'update',
                                                    'bg-red-100 text-red-800':
                                                        log.action === 'delete',
                                                    'bg-gray-100 text-gray-800':
                                                        ![
                                                            'create',
                                                            'update',
                                                            'delete',
                                                        ].includes(log.action),
                                                }"
                                            >
                                                {{ log.action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ log.module }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ log.description }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                {{ formatDate(log.created_at) }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Add this to ensure proper rotation and transition */
.rotate-180 {
    transform: rotate(180deg);
}

/* Make the thead sticky at the top */
thead {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: var(--TopTableBG-color, #F9FAFB);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Hide scrollbar in Firefox */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: rgba(203, 213, 225, 0.5) transparent;
}

/* Style scrollbar for webkit browsers */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: rgba(203, 213, 225, 0.5);
    border-radius: 20px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: rgba(203, 213, 225, 0.8);
}
</style>
