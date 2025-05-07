<script setup>
const props = defineProps({
    layout: {
        type: Object,
        default: () => ({}),
    },
});
</script>
<template>
    <div
        class="w-full h-56 flex text-white bg-accent1 space-x-40 sm:space-x-64 font-merriweather sm:text-base"
    >
        <div class="grid mt-4 md:mt-10 ml-3 md:ml-28">
            <img
                :src="`storage/${layout.logo}`"
                alt="Logo"
                class="w-16 h-17 mb-2"
            />
            <div class="">
                <h1
                    class="text-yellow-600 font-bold text-sm md:text-lg ml-27 -mt-2 max-w-30 md:max-w-30"
                >
                    Pemerintah {{ layout.name }}
                </h1>
                <p
                    class="text-white text-xs md:text-base max-w-lg md:max-w-sm ml-27 mt-2"
                >
                    {{ layout.name }}, Kecamatan {{ layout.kecamatan }},
                    {{ layout.kabupaten }}, Provinsi {{ layout.provinsi }}.
                </p>
            </div>
        </div>
        <div class="flex items-center -mt-28 md:-mt-20">
            <div class="-ml-16">
                <h1
                    class="text-yellow-600 font-bold text-sm md:text-lg mt-16 md:mt-7 -ml-16 md:-ml-12 max-w-[200px] md:max-w-[200px]"
                >
                    Kontak & Media Sosial
                </h1>
                <div class="mt-2 space-y-2 -ml-16 md:-ml-12">
                    <i
                        class="bi bi-instagram md:text-xl relative -top-1 mr-4"
                    ></i>
                    <i
                        class="bi bi-twitter-x md:text-xl relative -top-1 mr-4"
                    ></i>
                    <i
                        class="bi bi-facebook md:text-xl relative -top-1 mr-4"
                    ></i>
                    <i
                        class="bi bi-youtube text-xl md:text-2xl -top-0.5 md:-top-0 relative mr-4"
                    ></i>
                    <p class="text-white text-xs md:text-base max-w-[180px]">
                        <i class="bi bi-telephone text-sm mr-2"></i
                        >{{ layout.phone }}
                    </p>
                    <p
                        class="text-white text-xs md:text-base max-w-[500px] md:max-w-[450px]"
                    >
                        <i class="bi bi-envelope text-sm mr-2"></i
                        >{{ layout.email }}
                    </p>
                </div>
            </div>
        </div>
        <div class="flex items-center -mt-16">
            <div class="xl:-ml-28 sm:-ml-40">
                <h1
                    class="text-yellow-600 font-bold text-base md:text-lg sm:mt-20 ml-10"
                >
                    Layanan Publik
                </h1>
                <div class="ml-10 mt-2 space-y-1">
                    <p class="text-white text-sm md:text-base">Berita Desa</p>
                    <p class="text-white text-sm md:text-base">
                        Event & Agenda
                    </p>
                    <p class="text-white text-sm md:text-base">Produk Hukum</p>
                    <p class="text-white text-sm md:text-base">
                        Laporan Dana Desa
                    </p>
                    <p class="text-white text-sm md:text-base">Produk Unggul</p>
                    <p class="text-white text-sm md:text-base">
                        Feedback & Saran
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="relative bg-accent1 p-5">
        <p
            class="absolute bottom-0 left-1/2 -translate-x-1/2 text-white text-sm p-2"
        >
            © 2025 Powered by PT BIPTEK Tokodata Indonesia
        </p>
    </div>
</template>

<style scoped></style>
