<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted, computed, watch } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import TabDevider from "@/Components/DashboardComponent/TabDevider.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import LineChart from "@/Components/DashboardComponent/Charts/LineChart.vue";
import DoughnutChart from "@/Components/DashboardComponent/Charts/DoughnutChart.vue";

const props = defineProps({
    stats: Object,
    filters: Object,
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

// Computed properties for easy access to stats
const visitors = computed(() => props.stats?.visitors || {});
const contentStats = computed(() => props.stats?.content || {});
const featureStats = computed(() => props.stats?.features || []);

// Content period filter (frontend only now)
const contentPeriod = ref('month');

// Get filtered content stats based on selected period
const filteredContentStats = computed(() => {
    return contentStats.value?.all_periods?.[contentPeriod.value] || [];
});

// Helper function to format numbers
const formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num || 0);
};

// Handle content period change (no server request needed)
const handleContentPeriodChange = () => {
    // No need to make server request, just update the reactive variable
    // The computed property will automatically update the display
};

// Get period label for display
const getPeriodLabel = (period) => {
    const labels = {
        'day': 'Hari Ini',
        'week': 'Minggu Ini', 
        'month': 'Bulan Ini',
        'year': 'Tahun Ini',
        'all': 'Sepanjang Waktu'
    };
    return labels[period] || 'Bulan Ini';
};

// Visitor chart period filter
const visitorPeriod = ref('weekly'); // Changed from 'daily' to 'weekly'

// Get filtered visitor chart data based on selected period
const visitorChartData = computed(() => {
    const timeSeriesData = props.stats?.visitors?.time_series;
    if (!timeSeriesData || !timeSeriesData[visitorPeriod.value]) {
        return { labels: [], values: [] };
    }
    return timeSeriesData[visitorPeriod.value];
});

// Get visitor chart title
const visitorChartTitle = computed(() => {
    const titles = {
        'daily': 'Pengunjung Unik per 4 Jam (Hari Ini)',
        'weekly': 'Pengunjung Unik Harian (Minggu Ini)',
        'monthly': 'Pengunjung Unik Mingguan (Bulan Ini)',
        'yearly': 'Pengunjung Unik Bulanan (Tahun Ini)',
        'all': 'Pengunjung Unik Tahunan (Sepanjang Waktu)'
    };
    return titles[visitorPeriod.value] || 'Pengunjung Unik';
});

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
                <!-- Visitors Chart -->
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Statistik Pengunjung Unik</h2>
                            <p class="mt-2 text-sm text-gray-600">Grafik pengunjung unik berdasarkan periode waktu yang dipilih.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="visitor-period" class="text-sm font-medium text-gray-700">Periode:</label>
                            <select
                                id="visitor-period"
                                v-model="visitorPeriod"
                                class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >
                                <option value="daily">Harian (4 Jam)</option>
                                <option value="weekly">Mingguan (Hari)</option>
                                <option value="monthly">Bulanan (Minggu)</option>
                                <option value="yearly">Tahunan (Bulan)</option>
                                <option value="all">Sepanjang Waktu (Tahun)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <LineChart 
                            :data="visitorChartData"
                            :title="visitorChartTitle"
                            color="#3B614A"
                        />
                    </div>
                    
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-blue-800">Hari Ini</h3>
                            <p class="text-2xl font-bold text-blue-900">{{ formatNumber(visitors.today) }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-green-800">Minggu Ini</h3>
                            <p class="text-2xl font-bold text-green-900">{{ formatNumber(visitors.week) }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-yellow-800">Bulan Ini</h3>
                            <p class="text-2xl font-bold text-yellow-900">{{ formatNumber(visitors.month) }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-purple-800">Tahun Ini</h3>
                            <p class="text-2xl font-bold text-purple-900">{{ formatNumber(visitors.year) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Content Views by Category -->
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Tampilan Konten Berdasarkan Kategori</h2>
                    <p class="mt-2 text-sm text-gray-600">Statistik tampilan konten untuk setiap kategori.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-indigo-800">Berita</h3>
                            <p class="text-xl font-bold text-indigo-900">{{ formatNumber(contentStats.news?.length || 0) }} konten</p>
                            <p class="text-sm text-indigo-600">Total views: {{ formatNumber(contentStats.news?.reduce((sum, item) => sum + (item.views || 0), 0) || 0) }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-red-800">Produk Hukum</h3>
                            <p class="text-xl font-bold text-red-900">{{ formatNumber(contentStats.produk_hukum?.length || 0) }} konten</p>
                            <p class="text-sm text-red-600">Total views: {{ formatNumber(contentStats.produk_hukum?.reduce((sum, item) => sum + (item.views || 0), 0) || 0) }}</p>
                        </div>
                        <div class="bg-teal-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-teal-800">Dana Desa</h3>
                            <p class="text-xl font-bold text-teal-900">{{ formatNumber(contentStats.dana_desa?.length || 0) }} konten</p>
                            <p class="text-sm text-teal-600">Total views: {{ formatNumber(contentStats.dana_desa?.reduce((sum, item) => sum + (item.views || 0), 0) || 0) }}</p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-orange-800">Produk Desa</h3>
                            <p class="text-xl font-bold text-orange-900">{{ formatNumber(contentStats.produk_desa?.length || 0) }} konten</p>
                            <p class="text-sm text-orange-600">Total views: {{ formatNumber(contentStats.produk_desa?.reduce((sum, item) => sum + (item.views || 0), 0) || 0) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Popular Content -->
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Konten Terpopuler</h2>
                            <p class="mt-2 text-sm text-gray-600">Konten dengan skor popularitas tertinggi (Views × 1 + Comments × 3) - {{ getPeriodLabel(contentPeriod) }}.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="content-period" class="text-sm font-medium text-gray-700">Periode:</label>
                            <select
                                id="content-period"
                                v-model="contentPeriod"
                                @change="handleContentPeriodChange"
                                class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >
                                <option value="day">Hari Ini</option>
                                <option value="week">Minggu Ini</option>
                                <option value="month">Bulan Ini</option>
                                <option value="year">Tahun Ini</option>
                                <option value="all">Sepanjang Waktu</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div v-if="filteredContentStats && filteredContentStats.length > 0" class="space-y-3">
                            <div v-for="(content, index) in filteredContentStats.slice(0, 5)" :key="index" 
                                 class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ content.title }}</p>
                                    <p class="text-sm text-gray-600">Kategori: {{ content.category }}</p>
                                    <div class="flex gap-4 mt-1">
                                        <span class="text-xs text-blue-600">{{ formatNumber(content.views) }} views</span>
                                        <span v-if="content.comments > 0" class="text-xs text-green-600">{{ formatNumber(content.comments) }} comments</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">{{ formatNumber(content.weighted_score) }}</p>
                                    <p class="text-sm text-gray-600">skor</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            Tidak ada data konten yang tersedia untuk periode {{ getPeriodLabel(contentPeriod).toLowerCase() }}
                        </div>
                    </div>
                </div>

                <!-- Feature Usage -->
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Penggunaan Fitur</h2>
                    <p class="mt-2 text-sm text-gray-600">Statistik penggunaan fitur-fitur pada website.</p>
                    <div class="mt-4">
                        <div v-if="featureStats && featureStats.length > 0" class="flex flex-col lg:flex-row gap-6">
                            <!-- Chart Section -->
                            <div class="flex-1">
                                <DoughnutChart 
                                    :data="featureStats"
                                    title="Penggunaan Fitur"
                                />
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            Tidak ada data penggunaan fitur yang tersedia
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Remove the dashboard-container styles since we're using the table-content slot now */
</style>
