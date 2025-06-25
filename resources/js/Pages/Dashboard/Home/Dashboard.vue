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

                        

                        <!-- Tentang CMS -->
                        <div class="bg-white border border-gray-200 rounded-[8px] sm:rounded-[9px] md:rounded-[10px] xl:rounded-[11px] 2xl:rounded-[12px] 3xl:rounded-[13px] 4xl:rounded-[14px] p-[20px] sm:p-[22px] md:p-[24px] xl:p-[22px] 2xl:p-[24px] 3xl:p-[26px] 4xl:p-[28px] mt-[40px] sm:mt-[45px] md:mt-[50px] xl:mt-[45px] 2xl:mt-[50px] 3xl:mt-[55px] 4xl:mt-[60px]">
                            <div class="text-center">
                                <!-- Logo CMS Waradesa -->
                                <div class="mb-[20px] sm:mb-[22px] md:mb-[24px] xl:mb-[22px] 2xl:mb-[24px] 3xl:mb-[26px] 4xl:mb-[28px]">
                                    <svg width="90" height="auto" viewBox="0 0 671 440" fill="none" class="mx-auto text-selectedText fill-current" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M33.7781 48.9382C24.5659 19.425 17.9857 15.2088 0 15.2088V2.09184H117.565V15.2088C99.5796 15.2088 95.6315 19.425 104.405 47.5328L161.872 229.765L191.702 82.1991L181.174 48.9382C171.961 19.425 165.82 15.2088 147.395 15.2088V1.62339H264.961V15.2088C246.975 15.2088 243.027 19.425 251.8 47.5328L326.376 283.638C329.008 292.071 331.201 298.161 333.833 302.377C331.201 295.818 329.008 285.512 329.008 270.99C328.13 227.891 359.715 181.982 359.715 113.586C359.715 25.515 297.862 16.1458 297.862 16.1458V1.62339H402.705C402.705 1.62339 419.375 28.3258 419.375 67.6767C419.375 155.748 344.8 206.81 344.8 281.296C344.8 306.593 359.715 315.962 359.715 315.962V329.548H303.126C282.947 329.548 265.399 315.962 259.258 295.818L200.475 110.307L170.645 258.341L178.98 283.638C187.754 312.214 194.334 315.962 212.32 315.962V329.548H141.693L142.131 327.205C128.094 322.989 116.688 311.278 111.863 295.818L33.7781 48.9382Z" />
                                        <path d="M540.317 335.5H512.99H456.28C436.057 335.5 418.473 321.885 412.318 301.697L380.955 202.716C388.223 182.136 421.114 140.144 421.114 110.931L479.579 289.491C482.217 297.941 484.415 304.045 487.053 308.27C492.378 315.417 504.629 321.16 512.99 321.16C513.355 321.16 514.154 321.174 515.216 321.192C518.886 321.254 525.684 321.37 528.413 321.16C565.435 318.312 592.037 254.865 592.037 167.51C592.037 80.6352 565.716 24.335 528.585 24.335C493.78 22.4569 470.458 36.0933 463.748 77.652L459.96 102.544H449.678V0H537.985C629.169 0 671 64.3162 671 167.51C671 270.704 629.621 333.58 540.317 335.5Z" />
                                        <path d="M56.5879 438.5L31.5267 364.166H39.5973L63.0656 434.146H59.0303L83.2419 364.166H90.4629L114.356 434.146H110.533L134.214 364.166H141.753L116.692 438.5H108.409L85.5781 372.13H87.702L64.7646 438.5H56.5879ZM141.986 438.5L175.968 364.166H183.72L217.701 438.5H209.418L178.198 368.732H181.383L150.163 438.5H141.986ZM155.367 418.642L157.703 412.271H200.923L203.259 418.642H155.367ZM229.998 438.5V364.166H257.82C264.12 364.166 269.536 365.193 274.067 367.246C278.598 369.228 282.067 372.13 284.474 375.953C286.952 379.705 288.19 384.272 288.19 389.652C288.19 394.891 286.952 399.422 284.474 403.244C282.067 406.997 278.598 409.899 274.067 411.952C269.536 413.934 264.12 414.926 257.82 414.926H234.351L237.856 411.315V438.5H229.998ZM280.757 438.5L261.643 411.527H270.138L289.359 438.5H280.757ZM237.856 411.952L234.351 408.235H257.607C265.041 408.235 270.669 406.607 274.492 403.351C278.385 400.094 280.332 395.528 280.332 389.652C280.332 383.705 278.385 379.104 274.492 375.847C270.669 372.591 265.041 370.962 257.607 370.962H234.351L237.856 367.246V411.952ZM294.844 438.5L328.825 364.166H336.577L370.558 438.5H362.275L331.055 368.732H334.241L303.021 438.5H294.844ZM308.224 418.642L310.56 412.271H353.78L356.116 418.642H308.224ZM382.855 438.5V364.166H413.12C421.048 364.166 427.986 365.759 433.933 368.945C439.951 372.06 444.623 376.413 447.95 382.006C451.278 387.599 452.941 394.041 452.941 401.333C452.941 408.625 451.278 415.067 447.95 420.66C444.623 426.253 439.951 430.642 433.933 433.828C427.986 436.943 421.048 438.5 413.12 438.5H382.855ZM390.713 431.704H412.695C419.279 431.704 424.978 430.429 429.792 427.881C434.676 425.261 438.464 421.686 441.154 417.156C443.844 412.554 445.189 407.28 445.189 401.333C445.189 395.316 443.844 390.041 441.154 385.511C438.464 380.98 434.676 377.44 429.792 374.891C424.978 372.272 419.279 370.962 412.695 370.962H390.713V431.704ZM477.596 397.404H516.887V404.094H477.596V397.404ZM478.445 431.704H523.152V438.5H470.587V364.166H521.559V370.962H478.445V431.704ZM562.118 439.137C556.596 439.137 551.287 438.252 546.19 436.482C541.163 434.642 537.27 432.306 534.509 429.474L537.588 423.421C540.208 425.969 543.712 428.129 548.101 429.899C552.561 431.598 557.234 432.447 562.118 432.447C566.791 432.447 570.578 431.881 573.481 430.748C576.454 429.545 578.613 427.952 579.959 425.969C581.374 423.987 582.082 421.793 582.082 419.386C582.082 416.483 581.233 414.147 579.534 412.377C577.906 410.607 575.746 409.227 573.056 408.235C570.366 407.174 567.393 406.253 564.136 405.474C560.88 404.696 557.623 403.882 554.366 403.032C551.11 402.112 548.101 400.908 545.34 399.422C542.65 397.935 540.455 395.988 538.756 393.581C537.128 391.103 536.314 387.882 536.314 383.918C536.314 380.236 537.27 376.874 539.181 373.829C541.163 370.715 544.172 368.237 548.207 366.396C552.243 364.485 557.411 363.529 563.711 363.529C567.888 363.529 572.03 364.131 576.136 365.334C580.242 366.467 583.781 368.06 586.755 370.113L584.1 376.378C580.914 374.254 577.516 372.697 573.906 371.706C570.366 370.715 566.932 370.219 563.605 370.219C559.145 370.219 555.464 370.821 552.561 372.024C549.659 373.228 547.499 374.856 546.084 376.909C544.738 378.891 544.066 381.157 544.066 383.705C544.066 386.608 544.88 388.944 546.508 390.714C548.207 392.484 550.402 393.864 553.092 394.855C555.853 395.846 558.862 396.731 562.118 397.51C565.375 398.289 568.596 399.138 571.782 400.059C575.038 400.979 578.012 402.183 580.702 403.669C583.463 405.085 585.657 406.997 587.286 409.404C588.985 411.811 589.834 414.961 589.834 418.855C589.834 422.465 588.843 425.828 586.861 428.943C584.879 431.987 581.835 434.465 577.729 436.376C573.693 438.217 568.49 439.137 562.118 439.137ZM595.063 438.5L629.044 364.166H636.796L670.777 438.5H662.494L631.274 368.732H634.46L603.239 438.5H595.063ZM608.443 418.642L610.779 412.271H653.999L656.335 418.642H608.443Z" />
                                    </svg>
                                </div>

                                <!-- Tentang CMS Content -->
                                <h3 class="text-[18px] sm:text-[19px] md:text-[20px] xl:text-[19px] 2xl:text-[20px] 3xl:text-[21px] 4xl:text-[22px] font-semibold text-selectedText mb-[15px] sm:mb-[17px] md:mb-[19px] xl:mb-[17px] 2xl:mb-[19px] 3xl:mb-[21px] 4xl:mb-[23px]">
                                    Tentang CMS Waradesa
                                </h3>
                                <p class="text-[14px] sm:text-[15px] md:text-[16px] xl:text-[15px] 2xl:text-[16px] 3xl:text-[17px] 4xl:text-[18px] text-menuText mb-[20px] sm:mb-[22px] md:mb-[24px] xl:mb-[22px] 2xl:mb-[24px] 3xl:mb-[26px] 4xl:mb-[28px]">
                                    CMS Waradesa adalah sistem manajemen konten yang dirancang khusus untuk memudahkan pengelolaan informasi dan konten digital di tingkat desa. Platform ini menyediakan berbagai fitur lengkap untuk mengelola berita, galeri foto, agenda kegiatan, dan administrasi pengguna dengan antarmuka yang user-friendly dan responsif.
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-[15px] sm:gap-[17px] md:gap-[19px] xl:gap-[17px] 2xl:gap-[19px] 3xl:gap-[21px] 4xl:gap-[23px]">
                                    <div class="text-left">
                                        <h4 class="text-[14px] sm:text-[15px] md:text-[16px] xl:text-[15px] 2xl:text-[16px] 3xl:text-[17px] 4xl:text-[18px] font-medium text-selectedText mb-[8px] sm:mb-[9px] md:mb-[10px] xl:mb-[9px] 2xl:mb-[10px] 3xl:mb-[11px] 4xl:mb-[12px]">
                                            ✨ Fitur Utama:
                                        </h4>
                                        <ul class="text-[13px] sm:text-[14px] md:text-[15px] xl:text-[14px] 2xl:text-[15px] 3xl:text-[16px] 4xl:text-[17px] text-menuText space-y-[4px] sm:space-y-[5px] md:space-y-[6px] xl:space-y-[5px] 2xl:space-y-[6px] 3xl:space-y-[7px] 4xl:space-y-[8px]">
                                            <li>• Manajemen berita dan artikel</li>
                                            <li>• Galeri foto dan media</li>
                                            <li>• Agenda dan kalender kegiatan</li>
                                            <li>• Sistem pengguna dan hak akses</li>
                                        </ul>
                                    </div>
                                    <div class="text-left">
                                        <h4 class="text-[14px] sm:text-[15px] md:text-[16px] xl:text-[15px] 2xl:text-[16px] 3xl:text-[17px] 4xl:text-[18px] font-medium text-selectedText mb-[8px] sm:mb-[9px] md:mb-[10px] xl:mb-[9px] 2xl:mb-[10px] 3xl:mb-[11px] 4xl:mb-[12px]">
                                            🚀 Keunggulan:
                                        </h4>
                                        <ul class="text-[13px] sm:text-[14px] md:text-[15px] xl:text-[14px] 2xl:text-[15px] 3xl:text-[16px] 4xl:text-[17px] text-menuText space-y-[4px] sm:space-y-[5px] md:space-y-[6px] xl:space-y-[5px] 2xl:space-y-[6px] 3xl:space-y-[7px] 4xl:space-y-[8px]">
                                            <li>• Interface yang mudah digunakan</li>
                                            <li>• Desain responsif</li>
                                            <li>• Terintegrasi dengan layanan peta</li>
                                            <li>• Monitoring aktivitas real-time</li>
                                        </ul>
                                    </div>
                                </div>
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
