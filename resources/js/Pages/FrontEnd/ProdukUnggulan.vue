<script setup>
import { ref, onMounted } from "vue";
import ProductCard from "@/Components/ProductCard.vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import useApiFetch from "@/Utility/useApiFetch";
import Search from "@/Components/FrontendComponent/Search.vue";
import Pagination from "@/Components/FrontendComponent/Pagination.vue";

const produkDesa = ref([]);
const loading = ref(true);
const error = ref(null);
const searchQuery = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
const pagination = ref([]);

const fetchProduk = async (search = '', page = 1) => {
    loading.value = true;
    error.value = null;
    
    const produkApi = useApiFetch();
    pagination.value = produkApi;
    
    let apiUrl = `/api/produk-desa?page=${page}`;
    if (search) {
        apiUrl += `&search=${encodeURIComponent(search)}`;
    }
    
    await produkApi.fetchData(apiUrl);

    let responseData = produkApi.data.value;
    
    // Handle different possible response structures like GaleriList
    if (responseData && typeof responseData === 'object') {
        // Try direct access first
        if (responseData.success && responseData.data && Array.isArray(responseData.data)) {
            produkDesa.value = responseData.data;
        }
        // Try if data is nested deeper
        else if (responseData.data && responseData.data.success && responseData.data.data) {
            produkDesa.value = responseData.data.data;
        }
        // Try if it's just an array
        else if (Array.isArray(responseData)) {
            produkDesa.value = responseData;
        }
        else {
            produkDesa.value = [];
        }
    } else {
        produkDesa.value = [];
    }

    // Update pagination using the correct structure - match GaleriList pattern
    if (responseData?.meta) {
        currentPage.value = responseData.meta.current_page;
        totalPages.value = responseData.meta.last_page;
    } else if (pagination.value?.pagination?.last_page) {
        totalPages.value = pagination.value.pagination.last_page;
    }

    error.value = produkApi.error.value;
    loading.value = produkApi.loading.value;
};

const handleSearch = (query) => {
    searchQuery.value = query;
    currentPage.value = 1;
    fetchProduk(query, 1);
};

const handlePageChange = async (page) => {
    currentPage.value = page;
    await fetchProduk(searchQuery.value, page);
};

onMounted(async () => {
    await fetchProduk();
});
</script>
<template>
    <UserLayout>
        <div class="min-h-screen max-w-screen-4xl px-[6.56%] bg-type1 w-full mx-auto pt-32 4xl:pt-44 pb-8 xl:pb-16 font-merriweather">
            <div class="">
                <div class="flex flex-col items-center justify-center mb-5 xl:mb-10 lg:px-[9%]">
                    <h1 class="xl:text-2xl text-xl font-semibold text-accent1 justify-center mt-8">
                        Produk Unggulan Desa
                    </h1>
                    <Search 
                        class="py-8 lg:px-[3%]"
                        v-model="searchQuery"
                        @search="handleSearch"
                        placeholder="Cari produk unggulan desa..."
                    />
                </div>
                
                <!-- Loading/Error/Product Grid -->
                <div v-if="loading">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <ProductCard v-for="n in 12" :key="n" :loading="true" />
                    </div>
                </div>
                <div v-else-if="error" class="text-center text-red-600 py-8">
                    {{ error }}
                </div>
                <div v-else-if="produkDesa.length === 0" class="text-center py-8 text-gray-500">
                    Tidak ada produk ditemukan.
                </div>
                <div v-else>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <ProductCard
                            v-for="produk in produkDesa"
                            :key="produk.id"
                            :produkDesa="produk"
                            :loading="false"
                        />
                    </div>
                    
                    <!-- Pagination - match GaleriList pattern -->
                </div>
                <Pagination
                        :current-page="pagination?.pagination?.current_page || currentPage"
                        :last-page="pagination?.pagination?.last_page || totalPages"
                        @page-changed="handlePageChange"
                    />
            </div>
        </div>
    </UserLayout>
</template>
