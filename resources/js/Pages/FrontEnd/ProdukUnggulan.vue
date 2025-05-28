<script setup>
import { ref, onMounted } from "vue";
import ProductCard from "@/Components/ProductCard.vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import useApiFetch from "@/Utility/useApiFetch";

const produkDesa = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
    const produkApi = useApiFetch();
    await produkApi.fetchData("/api/produk-desa");

    if (produkApi.data.value) {
        produkDesa.value = produkApi.data.value;
    }

    error.value = produkApi.error.value;
    loading.value = produkApi.loading.value;
});
</script>
<template>
    <UserLayout>
        <div class="min-h-screen bg-type1">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-40 font-merriweather"
            >
                <div class="flex flex-col items-center justify-center mb-12">
                    <h1 class="text-2xl font-bold text-accent1">
                        Produk Unggulan Desa
                    </h1>
                </div>
                <div class="flex items-center justify-between mt-4 mb-8">
                    <!-- Filter Bar -->
                    <div class="flex flex-wrap gap-2 text-sm">
                        <div class="relative">
                            <input
                                type="text"
                                placeholder="Cari produk desa..."
                                class="border-gray-300 rounded px-3 py-2 bg-type1 text-gray-300 text-xs focus:outline-none focus:ring-0 w-80"
                            />
                            <span
                                class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-300 text-base pointer-events-none"
                                >search</span
                            >
                        </div>
                    </div>
                </div>
                <!-- Loading/Error/Product Grid -->
                <div v-if="loading" class="text-center py-8">
                    Memuat produk...
                </div>
                <div v-else-if="error" class="text-center text-red-600 py-8">
                    {{ error }}
                </div>
                <div
                    v-else
                    class="grid grid-rows-2 sm:grid-cols-3 lg:grid-cols-4 gap-6"
                >
                    <ProductCard
                        v-for="produk in produkDesa"
                        :key="produk.id"
                        :produkDesa="produk"
                    />
                </div>
            </div>
        </div>
    </UserLayout>
</template>
