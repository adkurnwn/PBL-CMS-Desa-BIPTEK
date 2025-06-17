<script setup>
import { Link } from '@inertiajs/vue3';
import { useImageErrorHandler } from '@/Utility/useImageErrorHandler';

const { handleImageError, getImageSrc } = useImageErrorHandler();

defineProps({
    produkDesa: {
        type: Object,
        default: () => ({
            image: { path: "default-image.jpg", alt: "Default Product Image" },
            name: "Sample Product",
            price: "Rp 0",
        }),
    },
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>
<template>
    <div class="w-full max-w-lg p-1">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-lg transition-colors duration-300">
            <!-- Skeleton Loading State -->
            <template v-if="loading">
                <div class="px-3 mt-5">
                    <div class="w-full h-[125px] md:h-60 rounded-[5px] bg-gray-200 animate-pulse"></div>
                </div>
                <div class="px-4">
                    <div class="mt-2 space-y-1">
                        <div class="px-1 min-h-14 space-y-2">
                            <div class="h-6 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-6 bg-gray-200 rounded w-3/4 animate-pulse"></div>
                        </div>
                        <div class="px-1 h-4 bg-gray-200 rounded w-1/2 animate-pulse"></div>
                    </div>
                    <div class="flex flex-row justify-center py-4">
                        <div class="w-full h-10 md:h-12 bg-gray-200 rounded-lg animate-pulse"></div>
                    </div>
                </div>
            </template>
            
            <!-- Actual Content -->
            <template v-else>
                <div class="px-3 mt-5">
                    <img
                        :src="getImageSrc(`/storage/${produkDesa.thumbnail}`)"
                        :alt="produkDesa?.name || 'Produk UMKM'"
                        class="w-full h-[125px] md:h-60 rounded-[5px] overflow-hidden object-cover"
                        @error="handleImageError(`/storage/${produkDesa.thumbnail}`)"
                        loading="lazy"
                    />
                </div>
                <div class="px-4">
                    <div class="mt-2 space-y-1 ">
                        <h2 class="px-1 text-lg nd:text-2xl font-semibold text-accent2 line-clamp-2 min-h-14">
                            {{ produkDesa?.name || "Unnamed Product" }}
                        </h2>
                        <h3 class="px-1 text-xs md:text-sm text-accent2">
                            {{ produkDesa?.price || "Price not set" }}
                        </h3>
                    </div>
                    <div class="flex flex-row justify-center py-4 hover:scale-105 transition-transform">
                        <Link
                            :href="`/produk-unggulan/${produkDesa.slug}`"
                            class="flex w-full mx-auto justify-center bg-accent2 text-white text-xs md:text-sm py-2 md:py-3 rounded-lg"
                        >
                            <p>Lihat Katalog</p>
                        </Link>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<style scoped>
.news-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

.news-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.news-card__image {
    width: 100%;
    height: auto;
}

.news-card__content {
    padding: 16px;
}

.news-card__title {
    font-size: 1.5em;
    margin: 0 0 8px;
}

.news-card__description {
    font-size: 1em;
    color: #555;
}
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
