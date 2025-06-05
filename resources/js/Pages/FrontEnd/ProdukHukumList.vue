<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import useApiFetch from "@/Utility/useApiFetch";
import useFilter from "@/Utility/useFilter";
import { computed } from "vue";

const produkHukum = ref([]);
const categories = ref([]);
const error = ref(null);
const produkHukumApi = useApiFetch();
const categoriesApi = useApiFetch();
const pagination = ref(null);
const totalPages = computed(() => {
    if (!pagination.value) return [];
    return Array.from({ length: pagination.value.last_page }, (_, i) => i + 1);
});


const {
    searchQuery,
    selectedCategory,
    handleSearch,
    selectCategory,
    goToPage,
} = useFilter(fetchProdukHukum);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

async function fetchProdukHukum({
    search = "",
    category = null,
    page = 1,
} = {}) {
    const produkHukumLoading = ref(true);
    const params = new URLSearchParams();

    if (search) params.append("search", search);
    if (category && category !== "Semua") {
        if (category === "Populer") {
            params.append("sort", "views");
        } else {
            params.append("category", category);
        }
    }
    params.append("page", page);

    await produkHukumApi.fetchData(`/api/produk-hukum?${params.toString()}`);

    if (produkHukumApi.data.value) {
        produkHukum.value = produkHukumApi.data.value;
        pagination.value = produkHukumApi.pagination.value;
    }

    error.value = produkHukumApi.error.value;
    produkHukumLoading.value = produkHukumApi.loading.value;
}

const fetchCategories = async () => {
    await categoriesApi.fetchData("/api/categories?type=0");
    categories.value = categoriesApi.data.value;
};

onMounted(() => {
    fetchProdukHukum();
    fetchCategories();
});
</script>

<template>
    <UserLayout>
        <div class="min-h-screen bg-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-40 font-merriweather">
                <div class="flex flex-col items-center justify-center mb-12">
                    <h1 class="text-2xl font-bold text-accent1">Produk Hukum</h1>
                </div>
                <div class="flex xl:flex-row sm:flex-col items-center justify-between mt-4 mb-8 gap-4">
                    <h1 class="xl:text-xl sm:text-md text-accent2 flex items-center xl:mr-0 sm:mr-80">
                    <span class="material-symbols-outlined mr-2 text-[1.3em] align-middle">description</span>
                    Produk Hukum Desa Pao-Pao
                    </h1>
                    <!-- Filter Bar -->
                    <div class="flex flex-wrap gap-2 text-sm">
                        <!-- Nomor Peraturan -->
                        <div class="relative">
                            <input
                                type="text"
                                placeholder="Cari nomor peraturan..."
                                class="border-gray-300 rounded px-3 py-2 bg-type1 text-gray-300 text-xs focus:outline-none focus:ring-0 w-44"
                            />
                            <span
                                class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-300 text-base pointer-events-none"
                                >search</span
                            >
                        </div>
                        <!-- Judul -->
                        <div class="relative">
                            <input
                                type="text"
                                placeholder="Cari judul..."
                                class="border-gray-300 rounded px-3 py-2 bg-type1 text-gray-300 text-xs focus:outline-none focus:ring-0 w-44"
                            />
                            <span
                                class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-300 text-base pointer-events-none"
                                >search</span
                            >
                        </div>
                        <!-- Kategori -->
                        <select
                            class="border rounded px-3 py-2 bg-type1 text-gray-500 text-xs focus:outline-none focus:ring-0 w-36"
                        >
                            <option>Kategori</option>
                            <!-- Tambahkan opsi lain sesuai kebutuhan -->
                        </select>
                        <!-- Tahun Penetapan -->
                        <select
                            class="border rounded px-3 py-2 bg-type1 text-gray-500 text-xs focus:outline-none focus:ring-0 w-40"
                        >
                            <option>Tahun Penetapan</option>
                        </select>
                    </div>
                </div>

                <!-- Loading State -->
                <div
                    v-if="produkHukumLoading"
                    class="flex justify-center items-center py-12"
                >
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="text-center py-12">
                    <p class="text-red-600">{{ error }}</p>
                </div>

                <!-- Documents Grid -->
                <div v-else>
                    <div
                        v-for="(dokumen, idx) in produkHukum"
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
                                    class="inline-block px-2 py-0.5 text-xs font-semibold text-white bg-accent1 rounded-full whitespace-nowrap mx-auto"
                                >
                                    {{ dokumen.category }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 ml-8 mt-1 mb-2">
                                <!-- Metadata -->
                                <div class="flex flex-wrap items-center text-xs text-filterBorder gap-x-4 gap-y-1">
                                    <span>
                                        <span class="material-symbols-outlined align-middle text-sm mr-1">calendar_today</span>
                                        Ditetapkan: {{ formatDate(dokumen.date) }}
                                    </span>
                                    <span>
                                        <span class="material-symbols-outlined align-middle text-sm mr-1">visibility</span>
                                        Dilihat {{ dokumen.views || 0 }} kali
                                    </span>
                                    <span>
                                        <span class="material-symbols-outlined align-middle text-sm mr-1">file_save</span>
                                        Diunduh {{ dokumen.downloads || 0 }} kali
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
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="produkHukum.length === 0"
                        class="col-span-full text-center py-12"
                    >
                        <p class="text-gray-500 text-lg">
                            Belum ada dokumen yang tersedia.
                        </p>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="pagination && pagination.last_page > 1"
                    class="flex justify-center mt-5 gap-2"
                >
                    <button
                        @click="goToPage(pagination.current_page - 1)"
                        class="w-8 h-8 rounded-full flex items-center justify-center bg-accent1 text-type1 text-xs"
                        :disabled="pagination.current_page === 1"
                    >
                        &lt;
                    </button>
                    <span
                        v-for="p in totalPages"
                        :key="p"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-xs"
                        :class="pagination.current_page === p ? 'bg-accent1 text-type1' : 'border text-accent1 bg-type1'"
                        @click="pagination.current_page = p"
                    >{{ p }}</span>
                    <button
                        @click="goToPage(pagination.current_page + 1)"
                        class="w-8 h-8 rounded-full flex items-center justify-center bg-accent1 text-type1 text-xs"
                        :disabled="
                            pagination.current_page === pagination.last_page
                        "
                    >
                        &gt;
                    </button>
                </div>
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
