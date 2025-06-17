<script setup>
import { computed } from 'vue';
import { parseMediaSosial } from '@/Utility/useParser';

const props = defineProps({
    layout: {
        type: Object,
        default: () => ({}),
    },
});

const socialMedia = computed(() => 
    parseMediaSosial(props.layout.media_sosial || '')
);
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
                    <h2
                        class="text-yellow-600 font-bold text-lg mb-2"
                    >
                        Pemerintah {{ layout.name }}
                    </h2>
                    <address
                        class="text-white text-sm md:text-base leading-relaxed"
                    >
                        {{ layout.name }}, Kecamatan {{ layout.kecamatan }},
                        {{ layout.kabupaten }}, Provinsi {{ layout.provinsi }}.
                    </address>
                </div>
            </div>

            <!-- Contact & Social Media Section -->
            <div class="text-center md:text-left">
                <h2
                    class="text-yellow-600 font-bold text-lg mb-4"
                >
                    Kontak & Media Sosial
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-center md:justify-start space-x-4 mb-4">
                        <a 
                            v-if="socialMedia.instagram.url" 
                            :href="socialMedia.instagram.url" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="text-xl hover:text-yellow-600 cursor-pointer transition-colors duration-200"
                        >
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a 
                            v-if="socialMedia.x.url" 
                            :href="socialMedia.x.url" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="text-xl hover:text-yellow-600 cursor-pointer transition-colors duration-200"
                        >
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a 
                            v-if="socialMedia.facebook.url" 
                            :href="socialMedia.facebook.url" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="text-xl hover:text-yellow-600 cursor-pointer transition-colors duration-200"
                        >
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a 
                            v-if="socialMedia.youtube.url" 
                            :href="socialMedia.youtube.url" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="text-xl hover:text-yellow-600 cursor-pointer transition-colors duration-200"
                        >
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                    <p class="text-white text-sm md:text-base">
                        <a :href="`tel:${layout.phone}`" class=" hover:text-yellow-600 transition-colors duration-200">
                            <i class="bi bi-telephone text-sm mr-2"></i>{{ layout.phone }}
                        </a>
                    </p>
                    <p class="text-white text-sm md:text-base">
                        <a :href="`mailto:${layout.email}`" class=" hover:text-yellow-600 transition-colors duration-200">
                            <i class="bi bi-envelope text-sm mr-2"></i>{{ layout.email }}
                        </a>
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
                    <p class="text-white hover:text-yellow-600 cursor-pointer">
                        <a href="/berita">Berita Desa</a>
                    </p>
                    <p class="text-white hover:text-yellow-600 cursor-pointer">
                        <a href="/event">Event & Agenda</a>
                    </p>
                    <p class="text-white hover:text-yellow-600 cursor-pointer">
                        <a href="/produk-hukum">Produk Hukum</a>
                    </p>
                    <p class="text-white hover:text-yellow-600 cursor-pointer">
                        <a href="/dana-desa">Laporan Dana Desa</a>
                    </p>
                    <p class="text-white hover:text-yellow-600 cursor-pointer">
                        <a href="/produk-unggulan">Produk Unggulan</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-accent1 py-4">
        <a href="https://www.biptek.com/" target="_blank" rel="noopener noreferrer">
        <p
            class="text-center text-white font-merriweather font-thin text-xs md:text-sm"
        >
            © 2025 Powered by PT BIPTEK Tokodata Indonesia
        </p>
        </a>
    </div>
</template>

<style scoped></style>
