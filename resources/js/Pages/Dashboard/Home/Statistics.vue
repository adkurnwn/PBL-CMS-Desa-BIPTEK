<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted, computed, watch } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import TabDevider from "@/Components/DashboardComponent/TabDevider.vue";
import Search from "@/Components/DashboardComponent/Search.vue";

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

</script>

<template>

    <Head title="Statistik Pengunjung" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Statistik Pengunjung" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#314D1C">
                        <path
                            d="M160-120q-17 0-28.5-11.5T120-160v-40q0-17 11.5-28.5T160-240q17 0 28.5 11.5T200-200v40q0 17-11.5 28.5T160-120Zm160 0q-17 0-28.5-11.5T280-160v-220q0-17 11.5-28.5T320-420q17 0 28.5 11.5T360-380v220q0 17-11.5 28.5T320-120Zm160 0q-17 0-28.5-11.5T440-160v-140q0-17 11.5-28.5T480-340q17 0 28.5 11.5T520-300v140q0 17-11.5 28.5T480-120Zm160 0q-17 0-28.5-11.5T600-160v-200q0-17 11.5-28.5T640-400q17 0 28.5 11.5T680-360v200q0 17-11.5 28.5T640-120Zm160 0q-17 0-28.5-11.5T760-160v-360q0-17 11.5-28.5T800-560q17 0 28.5 11.5T840-520v360q0 17-11.5 28.5T800-120ZM560-481q-16 0-30.5-6T503-504L400-607 188-395q-12 12-28.5 11.5T131-396q-11-12-10.5-28.5T132-452l211-211q12-12 26.5-17.5T400-686q16 0 31 5.5t26 17.5l103 103 212-212q12-12 28.5-11.5T829-771q11 12 10.5 28.5T828-715L617-504q-11 11-26 17t-31 6Z" />
                    </svg>
                </template>
                <template #right-content>
                </template>
            </HeadingMenu>
        </template>

        <template #tab-devider>
            <TabDevider :tabs="[
                { name: 'Home', href: route('dashboard'), active: false },
                ...(hasPermission('manage_activity-logs') ? [{ name: 'Aktivitas Terbaru', href: route('dashboard.activity-logs'), active: false }] : []),
                ...(hasPermission('manage_statistics') ? [{ name: 'Statistik', href: route('dashboard.statistics'), active: true }] : [])
            ]" />
        </template>




        <div class="h-full">
            <div class="overflow-hidden bg-white shadow-sm h-full overflow-y-auto">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <!-- Repeat blocks from the original file -->
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Aktivitas</h2>
                    <p class="mt-2 text-sm text-gray-600">Berikut adalah statistik aktivitas terbaru.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Remove the dashboard-container styles since we're using the table-content slot now */
</style>
