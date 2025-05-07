<script setup>
defineProps({
    produkDesa: {
        type: Object,
        default: () => ({
            image: { path: "default-image.jpg", alt: "Default Product Image" },
            name: "Sample Product",
            price: "Rp 0",
        }),
    },
});
</script>
<template>
    <div class="w-full lg:w-1/4 p-1">
        <div
            class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-lg transition-colors duration-300"
        >
            <div class="px-3 mt-5">
                <img
                    :src="
                        produkDesa?.image?.path
                            ? `/storage/${produkDesa.image.path}`
                            : '/default-image.jpg'
                    "
                    :alt="produkDesa?.image?.alt || 'Default Image'"
                    class="w-96 h-[125px] md:h-60 rounded-xl overflow-hidden object-cover"
                />
            </div>
            <div class="px-4">
                <div class="mt-2 space-y-1">
                    <h2
                        class="px-1 text-lg nd:text-2xl font-semibold text-accent2"
                    >
                        {{ produkDesa?.name || "Unnamed Product" }}
                    </h2>
                    <h3 class="px-1 text-xs md:text-sm text-accent2">
                        {{ produkDesa?.price || "Price not set" }}
                    </h3>
                </div>
                <div
                    class="flex flex-row justify-center py-4 hover:scale-105 transition-transform"
                >
                    <button
                        class="w-full bg-accent2 text-white text-xs md:text-sm py-2 md:py-3 rounded-xl"
                    >
                        Lihat Katalog
                    </button>
                </div>
            </div>
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
</style>
