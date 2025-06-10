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

    <Head title="Aktivitas Terbaru" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Dashboard" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#314D1C">
                        <path
                            d="M240-200h120v-200q0-17 11.5-28.5T400-440h160q17 0 28.5 11.5T600-400v200h120v-360L480-740 240-560v360Zm-80 0v-360q0-19 8.5-36t23.5-28l240-180q21-16 48-16t48 16l240 180q15 11 23.5 28t8.5 36v360q0 33-23.5 56.5T720-120H560q-17 0-28.5-11.5T520-160v-200h-80v200q0 17-11.5 28.5T400-120H240q-33 0-56.5-23.5T160-200Zm320-270Z" />
                    </svg>
                </template>
                <template #right-content>
                </template>
            </HeadingMenu>
        </template>

        <template #tab-devider>
            <TabDevider :tabs="[
                { name: 'Home', href: route('dashboard'), active: true },
                ...(hasPermission('manage_activity-logs') ? [{ name: 'Aktivitas Terbaru', href: route('dashboard.activity-logs'), active: false }] : []),
                ...(hasPermission('manage_statistics') ? [{ name: 'Statistik', href: route('dashboard.statistics'), active: false }] : [])
            ]" />
        </template>

        <!-- Welcome -->
 
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm h-full">
                    <div class="p-[20px] sm:p-[25px] md:p-[30px] xl:p-[35px] 2xl:p-[40px] 3xl:p-[45px] 4xl:p-[50px]">
                        <!-- Welcome Header -->
                        <div class="mb-[30px] sm:mb-[35px] md:mb-[40px] xl:mb-[35px] 2xl:mb-[40px] 3xl:mb-[45px] 4xl:mb-[50px]">
                            <h1 class="text-[24px] sm:text-[26px] md:text-[28px] xl:text-[30px] 2xl:text-[34px] 3xl:text-[38px] 4xl:text-[42px] font-bold text-selectedText mb-[8px] sm:mb-[10px] md:mb-[12px] xl:mb-[10px] 2xl:mb-[12px] 3xl:mb-[14px] 4xl:mb-[16px]">
                                Selamat Datang, {{ $page.props.auth.user.name }}!
                            </h1>
                            <p class="text-[14px] sm:text-[15px] md:text-[16px] xl:text-[16px] 2xl:text-[18px] 3xl:text-[20px] 4xl:text-[22px] text-menuText">
                                Kelola konten dan informasi {{ $page.props.profile.name }} dengan mudah melalui CMS ini.
                            </p>
                        </div>

                        <!-- Welcome Cards Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[20px] sm:gap-[22px] md:gap-[24px] xl:gap-[26px] 2xl:gap-[28px] 3xl:gap-[30px] 4xl:gap-[32px] mb-[40px] sm:mb-[45px] md:mb-[50px] xl:mb-[45px] 2xl:mb-[50px] 3xl:mb-[55px] 4xl:mb-[60px]">
                            <!-- Quick Actions Card -->
                            <div class="bg-selectedBG border border-selectedText rounded-[8px] sm:rounded-[9px] md:rounded-[10px] xl:rounded-[11px] 2xl:rounded-[12px] 3xl:rounded-[13px] 4xl:rounded-[14px] p-[20px] sm:p-[22px] md:p-[24px] xl:p-[22px] 2xl:p-[24px] 3xl:p-[26px] 4xl:p-[28px]">
                                <div class="flex items-center mb-[15px] sm:mb-[17px] md:mb-[19px] xl:mb-[17px] 2xl:mb-[19px] 3xl:mb-[21px] 4xl:mb-[23px]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[24px] w-[24px] sm:h-[26px] sm:w-[26px] md:h-[28px] md:w-[28px] xl:h-[26px] xl:w-[26px] 2xl:h-[28px] 2xl:w-[28px] 3xl:h-[30px] 3xl:w-[30px] 4xl:h-[32px] 4xl:w-[32px] text-selectedText mr-[8px] sm:mr-[9px] md:mr-[10px] xl:mr-[9px] 2xl:mr-[10px] 3xl:mr-[11px] 4xl:mr-[12px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <h3 class="text-[16px] sm:text-[17px] md:text-[18px] xl:text-[17px] 2xl:text-[18px] 3xl:text-[19px] 4xl:text-[20px] font-semibold text-selectedText">
                                        Akses Cepat
                                    </h3>
                                </div>
                                <p class="text-[13px] sm:text-[14px] md:text-[15px] xl:text-[14px] 2xl:text-[15px] 3xl:text-[16px] 4xl:text-[17px] text-selectedText">
                                    Akses semua fitur utama sistem manajemen konten dengan cepat melalui menu navigasi di sebelah kiri.
                                </p>
                            </div>

                            <!-- Content Management Card -->
                            <div class="bg-selectedBG border border-selectedText rounded-[8px] sm:rounded-[9px] md:rounded-[10px] xl:rounded-[11px] 2xl:rounded-[12px] 3xl:rounded-[13px] 4xl:rounded-[14px] p-[20px] sm:p-[22px] md:p-[24px] xl:p-[22px] 2xl:p-[24px] 3xl:p-[26px] 4xl:p-[28px]">
                                <div class="flex items-center mb-[15px] sm:mb-[17px] md:mb-[19px] xl:mb-[17px] 2xl:mb-[19px] 3xl:mb-[21px] 4xl:mb-[23px]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[24px] w-[24px] sm:h-[26px] sm:w-[26px] md:h-[28px] md:w-[28px] xl:h-[26px] xl:w-[26px] 2xl:h-[28px] 2xl:w-[28px] 3xl:h-[30px] 3xl:w-[30px] 4xl:h-[32px] 4xl:w-[32px] text-selectedText mr-[8px] sm:mr-[9px] md:mr-[10px] xl:mr-[9px] 2xl:mr-[10px] 3xl:mr-[11px] 4xl:mr-[12px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <h3 class="text-[16px] sm:text-[17px] md:text-[18px] xl:text-[17px] 2xl:text-[18px] 3xl:text-[19px] 4xl:text-[20px] font-semibold text-selectedText">
                                        Kelola Konten
                                    </h3>
                                </div>
                                <p class="text-[13px] sm:text-[14px] md:text-[15px] xl:text-[14px] 2xl:text-[15px] 3xl:text-[16px] 4xl:text-[17px] text-selectedText">
                                    Buat dan kelola berita, galeri, agenda, serta konten lainnya untuk menyampaikan informasi kepada warga.
                                </p>
                            </div>

                            <!-- Hak akses Card -->
                            <div class="bg-selectedBG border border-selectedText rounded-[8px] sm:rounded-[9px] md:rounded-[10px] xl:rounded-[11px] 2xl:rounded-[12px] 3xl:rounded-[13px] 4xl:rounded-[14px] p-[20px] sm:p-[22px] md:p-[24px] xl:p-[22px] 2xl:p-[24px] 3xl:p-[26px] 4xl:p-[28px]">
                                <div class="flex items-center mb-[15px] sm:mb-[17px] md:mb-[19px] xl:mb-[17px] 2xl:mb-[19px] 3xl:mb-[21px] 4xl:mb-[23px]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[24px] w-[24px] sm:h-[26px] sm:w-[26px] md:h-[28px] md:w-[28px] xl:h-[26px] xl:w-[26px] 2xl:h-[28px] 2xl:w-[28px] 3xl:h-[30px] 3xl:w-[30px] 4xl:h-[32px] 4xl:w-[32px] text-selectedText mr-[8px] sm:mr-[9px] md:mr-[10px] xl:mr-[9px] 2xl:mr-[10px] 3xl:mr-[11px] 4xl:mr-[12px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <h3 class="text-[16px] sm:text-[17px] md:text-[18px] xl:text-[17px] 2xl:text-[18px] 3xl:text-[19px] 4xl:text-[20px] font-semibold text-selectedText">
                                        Hak Akses
                                    </h3>
                                </div>
                                <p class="text-[13px] sm:text-[14px] md:text-[15px] xl:text-[14px] 2xl:text-[15px] 3xl:text-[16px] 4xl:text-[17px] text-selectedText">
                                    Pastikan anda memiliki hak akses yang diberikan dari Superadmin untuk mengelola CMS ini.
                                </p>
                            </div>
                        </div>

                        <!-- Getting Started Section -->
                        <div class="bg-gray-50 rounded-[8px] sm:rounded-[9px] md:rounded-[10px] xl:rounded-[11px] 2xl:rounded-[12px] 3xl:rounded-[13px] 4xl:rounded-[14px] p-[20px] sm:p-[22px] md:p-[24px] xl:p-[22px] 2xl:p-[24px] 3xl:p-[26px] 4xl:p-[28px]">
                            <h3 class="text-[18px] sm:text-[19px] md:text-[20px] xl:text-[19px] 2xl:text-[20px] 3xl:text-[21px] 4xl:text-[22px] font-semibold text-selectedText mb-[15px] sm:mb-[17px] md:mb-[19px] xl:mb-[17px] 2xl:mb-[19px] 3xl:mb-[21px] 4xl:mb-[23px]">
                                Mulai Kelola CMS {{ $page.props.profile.name }}!
                            </h3>
                            <p class="text-[14px] sm:text-[15px] md:text-[16px] xl:text-[15px] 2xl:text-[16px] 3xl:text-[17px] 4xl:text-[18px] text-menuText mb-[20px] sm:mb-[22px] md:mb-[24px] xl:mb-[22px] 2xl:mb-[24px] 3xl:mb-[26px] 4xl:mb-[28px]">
                                Gunakan menu navigasi di sebelah kiri untuk mengakses berbagai fitur sistem. Anda dapat mengelola konten, mengatur pengguna, memantau aktivitas, dan masih banyak lagi. Pastikan anda memiliki hak akses yang diberikan dari Superadmin untuk mengelola CMS ini.
                            </p>
                            <div class="flex flex-wrap gap-[10px] sm:gap-[11px] md:gap-[12px] xl:gap-[11px] 2xl:gap-[12px] 3xl:gap-[13px] 4xl:gap-[14px]">
                                <span class="inline-flex items-center px-[12px] sm:px-[13px] md:px-[14px] xl:px-[13px] 2xl:px-[14px] 3xl:px-[15px] 4xl:px-[16px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[7px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] rounded-full text-[12px] sm:text-[13px] md:text-[14px] xl:text-[13px] 2xl:text-[14px] 3xl:text-[15px] 4xl:text-[16px] font-medium bg-selectedBG text-selectedText">
                                    📰 Kelola Berita
                                </span>
                                <span class="inline-flex items-center px-[12px] sm:px-[13px] md:px-[14px] xl:px-[13px] 2xl:px-[14px] 3xl:px-[15px] 4xl:px-[16px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[7px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] rounded-full text-[12px] sm:text-[13px] md:text-[14px] xl:text-[13px] 2xl:text-[14px] 3xl:text-[15px] 4xl:text-[16px] font-medium bg-selectedBG text-selectedText">
                                    🖼️ Galeri Foto
                                </span>
                                <span class="inline-flex items-center px-[12px] sm:px-[13px] md:px-[14px] xl:px-[13px] 2xl:px-[14px] 3xl:px-[15px] 4xl:px-[16px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[7px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] rounded-full text-[12px] sm:text-[13px] md:text-[14px] xl:text-[13px] 2xl:text-[14px] 3xl:text-[15px] 4xl:text-[16px] font-medium bg-selectedBG text-selectedText">
                                    📅 Agenda Desa
                                </span>
                                <span class="inline-flex items-center px-[12px] sm:px-[13px] md:px-[14px] xl:px-[13px] 2xl:px-[14px] 3xl:px-[15px] 4xl:px-[16px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[7px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] rounded-full text-[12px] sm:text-[13px] md:text-[14px] xl:text-[13px] 2xl:text-[14px] 3xl:text-[15px] 4xl:text-[16px] font-medium bg-selectedBG text-selectedText">
                                    👥 Kelola Pengguna
                                </span>
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
