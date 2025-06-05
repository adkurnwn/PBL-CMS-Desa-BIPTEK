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
        class="w-full text-white bg-accent1 font-merriweather py-10 px-4 md:px-8 lg:px-16"
    >
        <div class="grid grid-cols-1 md:grid-cols-[auto_auto_auto] gap-x-36 gap-y-8 justify-center max-w-7xl mx-auto">
            <!-- Village Information Section -->
            <div class="flex flex-col items-center md:items-start">
                <img
                    :src="`/storage/${layout.logo}`"
                    :alt="'Logo ' + layout.name"
                    class="w-16 h-auto mb-4"
                />
                <div class="text-center md:text-left">
                    <h1
                        class="text-yellow-600 font-bold text-lg mb-2"
                    >
                        Pemerintah {{ layout.name }}
                    </h1>
                    <p
                        class="text-white text-sm md:text-base max-w-[200px] leading-relaxed"
                    >
                        {{ layout.name }}, Kecamatan {{ layout.kecamatan }},
                        {{ layout.kabupaten }}, Provinsi {{ layout.provinsi }}.
                    </p>
                </div>
            </div>

            <!-- Contact & Social Media Section -->
            <div class="text-center md:text-left">
                <h1
                    class="text-yellow-600 font-bold text-lg mb-4"
                >
                    Kontak & Media Sosial
                </h1>
                <div class="space-y-3">
                    <div class="flex justify-center md:justify-start space-x-4 mb-4">
                        <i class="bi bi-instagram text-xl hover:text-yellow-600 cursor-pointer"></i>
                        <i class="bi bi-twitter-x text-xl hover:text-yellow-600 cursor-pointer"></i>
                        <i class="bi bi-facebook text-xl hover:text-yellow-600 cursor-pointer"></i>
                        <i class="bi bi-youtube text-xl hover:text-yellow-600 cursor-pointer"></i>
                    </div>
                    <p class="text-white text-sm md:text-base">
                        <i class="bi bi-telephone text-sm mr-2"></i>{{ layout.phone }}
                    </p>
                    <p class="text-white text-sm md:text-base">
                        <i class="bi bi-envelope text-sm mr-2"></i>{{ layout.email }}
                    </p>
                </div>
            </div>

            <!-- Public Services Section -->
            <div class="text-center md:text-left">
                <h1
                    class="text-yellow-600 font-bold text-lg mb-4"
                >
                    Layanan Publik
                </h1>
                <div class="space-y-2 text-sm md:text-base">
                    <p class="text-white hover:text-yellow-600 cursor-pointer">Berita Desa</p>
                    <p class="text-white hover:text-yellow-600 cursor-pointer">Event & Agenda</p>
                    <p class="text-white hover:text-yellow-600 cursor-pointer">Produk Hukum</p>
                    <p class="text-white hover:text-yellow-600 cursor-pointer">Laporan Dana Desa</p>
                    <p class="text-white hover:text-yellow-600 cursor-pointer">Produk Unggul</p>
                    <p class="text-white hover:text-yellow-600 cursor-pointer">Feedback & Saran</p>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-accent1 py-4">
        <p
            class="text-center text-white font-merriweather font-thin text-xs md:text-sm"
        >
            © 2025 Powered by PT BIPTEK Tokodata Indonesia
        </p>
    </div>
</template>

<style scoped></style>
