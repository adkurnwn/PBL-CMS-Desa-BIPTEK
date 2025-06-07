<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import useApiFetch from "@/Utility/useApiFetch";
import useFilter from "@/Utility/useFilter";
import { computed } from "vue";
import Pagination from "@/Components/FrontendComponent/Pagination.vue";

const produkHukumApi = useApiFetch();
const categoriesApi = useApiFetch();

// Search inputs
const nomorPeraturanQuery = ref("");
const judulQuery = ref("");
const selectedYear = ref("");

const totalPages = computed(() => {
    if (!produkHukumApi.pagination.value) return [];
    return Array.from({ length: produkHukumApi.pagination.value.last_page }, (_, i) => i + 1);
});

const fetchProdukHukumData = async ({
    search = "",
    category = null,
    year = null,
    page = 1,
} = {}) => {
    const params = new URLSearchParams();

    if (search) params.append("search", search);
    if (category && category !== "Semua" && category !== "") {
        if (category === "Populer") {
            params.append("sort", "views");
        } else {
            params.append("category", category);
        }
    }
    if (year && year !== "") {
        params.append("year", year);
    }
    params.append("page", page);

    await produkHukumApi.fetchData(`/api/produk-hukum?${params.toString()}`);
};

const {
    searchQuery,
    selectedCategory,
    handleSearch,
    selectCategory,
    goToPage,
} = useFilter(fetchProdukHukumData);

// Initialize selectedCategory to match "Semua Kategori" option value
selectedCategory.value = "";

// Handle nomor peraturan search
const handleNomorSearch = () => {
    // Clear judul search when searching by nomor
    judulQuery.value = "";
    
    if (nomorPeraturanQuery.value.trim()) {
        searchQuery.value = `nomor ${nomorPeraturanQuery.value.trim()}`;
    } else {
        searchQuery.value = "";
    }
    handleSearch();
};

// Handle judul search
const handleJudulSearch = () => {
    // Clear nomor search when searching by judul
    nomorPeraturanQuery.value = "";
    
    searchQuery.value = judulQuery.value.trim();
    handleSearch();
};

// Handle Enter key press
const handleKeyPress = (event, type) => {
    if (event.key === 'Enter') {
        if (type === 'nomor') {
            handleNomorSearch();
        } else if (type === 'judul') {
            handleJudulSearch();
        }
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

// Handle year selection
const handleYearChange = () => {
    fetchProdukHukumData({
        search: searchQuery.value,
        category: selectedCategory.value !== "" ? selectedCategory.value : null,
        year: selectedYear.value,
        page: 1
    });
};

// Generate year options from available data
const yearOptions = ref([]);

const generateYearOptions = () => {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let i = 0; i < 10; i++) {
        years.push(currentYear - i);
    }
    yearOptions.value = years;
};

onMounted(async () => {
    await fetchProdukHukumData();
    await categoriesApi.fetchData("/api/categories?type=0");
    generateYearOptions();
});

// Handle pagination
const handlePageChange = (page) => {
    goToPage(page);
};
</script>

<template>
    <Head title="Produk Hukum" />
    <UserLayout v-slot="{layout}">
        <div class="bg-type1">
            <div class="max-w-7xl mx-auto mt-20 px-4 sm:px-8 xl:px-8 pt-12 lg:pt-20 font-merriweather">
                <div class="flex flex-col items-center justify-center mb-12">
                    <h1 class="text-2xl font-bold text-accent1">Produk Hukum</h1>
                </div>
                <div class="flex flex-col xl:flex-row xl:items-center justify-between mt-4 mb-8">
                    <h1 class="sm:text-base xl:text-xl text-accent2 flex">
                    <span class="material-symbols-outlined mr-2 sm:text-base xl:text-xl align-middle">description</span>
                    Produk Hukum {{ layout.name }}
                    </h1>
                    <!-- Filter Bar -->
                    <div class="flex flex-wrap gap-2 text-sm sm:mt-4 xl:mt-0 mt-4 xl:justify-end">
                        <!-- Nomor Peraturan -->
                        <div class="relative">
                            <input
                                v-model="nomorPeraturanQuery"
                                @keypress="handleKeyPress($event, 'nomor')"
                                @blur="handleNomorSearch"
                                type="text"
                                placeholder="Cari nomor peraturan..."
                                class="border-gray-300 rounded px-3 py-2 bg-type1 text-gray-500 text-xs focus:outline-none focus:ring-0 w-72 xl:w-44"
                            />
                            <span
                                @click="handleNomorSearch"
                                class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-300 text-base cursor-pointer hover:text-gray-400"
                                >search</span
                            >
                        </div>
                        <!-- Judul -->
                        <div class="relative">
                            <input
                                v-model="judulQuery"
                                @keypress="handleKeyPress($event, 'judul')"
                                @blur="handleJudulSearch"
                                type="text"
                                placeholder="Cari judul..."
                                class="border-gray-300 rounded px-3 py-2 bg-type1 text-gray-500 text-xs focus:outline-none focus:ring-0 w-72 xl:w-44"
                            />
                            <span
                                @click="handleJudulSearch"
                                class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-300 text-base cursor-pointer hover:text-gray-400"
                                >search</span
                            >
                        </div>
                        <!-- Kategori -->
                        <select
                            v-model="selectedCategory"
                            @change="selectCategory(selectedCategory)"
                            class="border rounded px-3 py-2 bg-type1 text-gray-500 text-xs focus:outline-none focus:ring-0 w-72 xl:w-36"
                        >
                            <option value="">Semua Kategori</option>
                            <option 
                                v-for="category in categoriesApi.data.value" 
                                :key="category.id" 
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                        <!-- Tahun Penetapan -->
                        <select
                            v-model="selectedYear"
                            @change="handleYearChange"
                            class="border rounded px-3 py-2 bg-type1 text-gray-500 text-xs focus:outline-none focus:ring-0 w-72 xl:w-40"
                        >
                            <option value="">Tahun Penetapan</option>
                            <option 
                                v-for="year in yearOptions" 
                                :key="year" 
                                :value="year"
                            >
                                {{ year }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Loading State -->
                <div
                    v-if="produkHukumApi.loading.value"
                    class="flex justify-center items-center py-12"
                >
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>

                <!-- Error State -->
                <div v-else-if="produkHukumApi.error.value" class="text-center py-12">
                    <p class="text-red-600">{{ produkHukumApi.error.value }}</p>
                </div>

                <!-- Documents Grid -->
                <div v-else>
                    <a
                        v-for="(dokumen, idx) in produkHukumApi.data.value"
                        :key="dokumen.id"
                        :href="`/produk-hukum/${dokumen.slug}`"
                        class="flex items-center bg-white rounded-lg shadow px-4 py-1 mb-3 overflow-hidden hover:shadow-xl transition-shadow duration-300 cursor-pointer group"
                        style="text-decoration: none;"
                    >
                        <div class="flex-1 min-w-0 flex flex-col mt-2">
                            <!-- Nomor & Judul -->
                            <span class="text-accent1 mr-3 text-sm flex-shrink-0">{{ idx + 1 }}. <span class="ml-5">{{ dokumen.title }}</span></span>
                            <!-- Kategori -->
                            <div class="ml-8">
                                <span
                                    v-if="dokumen.category"
                                    class="inline-block px-2 py-0.5 text-[9px] font-semibold text-white bg-accent1 rounded-full whitespace-nowrap mx-auto"
                                >
                                    {{ dokumen.category }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 ml-8 mt-1 mb-2">
                                <!-- Metadata -->
                                <div class="flex flex-wrap items-center text-xs text-filterBorder gap-x-4 gap-y-1">
                                    <span class="inline-block" style="width: 28ch;">
                                        <span class="material-symbols-outlined align-middle text-sm mr-1">calendar_today</span>
                                        Ditetapkan: {{ formatDate(dokumen.date) }}
                                    </span>
                                    <span>
                                        <span class="material-symbols-outlined align-middle text-sm mr-1">visibility</span>
                                        Dilihat {{ dokumen.views || 0 }} kali
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!--Tombol Download-->
                        <div class="ml-4">
                            <a
                                v-if="dokumen.file"
                                :href="`/storage/${dokumen.file}`"
                                target="_blank"
                                class="inline-flex items-center justify-center w-8 h-8 rounded text-accent1 bg-secondary hover:bg-yellow-500"
                                tittle="Download"
                            >
                                <span class="material-symbols-outlined">download</span>
                             </a>
                             <div v-else
                                class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-500 text-white cursor-not-allowed"
                                title="File tidak tersedia">
                                <span class="material-symbols-outlined">download</span>
                            </div>
                        </div>
                    </a>

                    <!-- Empty State -->
                    <div
                        v-if="!produkHukumApi.data.value || produkHukumApi.data.value.length === 0"
                        class="col-span-full text-center py-12"
                    >
                        <p class="text-gray-500 text-lg">
                            Belum ada dokumen yang tersedia.
                        </p>
                    </div>
                </div>

                <!-- Pagination -->
                <Pagination
                    v-if="produkHukumApi.pagination.value && produkHukumApi.pagination.value.last_page > 1"
                    :current-page="produkHukumApi.pagination.value.current_page"
                    :last-page="produkHukumApi.pagination.value.last_page"
                    @page-changed="handlePageChange"
                />
            </div>
        </div>
    </UserLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
