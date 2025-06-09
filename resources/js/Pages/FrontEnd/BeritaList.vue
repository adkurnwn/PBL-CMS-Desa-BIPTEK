<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import useApiFetch from "@/Utility/useApiFetch";
import NewsCard from "@/Components/NewsCard.vue";
import { ref, onMounted, } from "vue";
import useFilter from "@/Utility/useFilter";
import {Head } from "@inertiajs/vue3";
import axios from "axios";

const news = ref([]);
const categories = ref([]);
const error = ref(null);
const newsLoading = ref(false);
const pagination = ref(2);
const totalPages= 2;
const newsApi = useApiFetch();
const categoriesApi = useApiFetch();
const showFilterModal = ref(false);
const filterWaktu = ref("semua");
const tanggalMulai = ref("");
const tanggalAkhir = ref("");

function openFilter() {
    showFilterModal.value = true;
}
function closeFilter() {
    showFilterModal.value = false;
}

const {
    searchQuery,
    selectedCategory,
    handleSearch,
    selectCategory,
    goToPage,
} = useFilter(fetchNews);

async function fetchNews({ search = "", category = null, page = 1 } = {}) {
    newsLoading.value = true;
    error.value = null;
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

    await newsApi.fetchData(`/api/news?${params.toString()}`);

    if (newsApi.data.value) {
        news.value = newsApi.data.value;
        pagination.value = newsApi.pagination.value;
    }else {
        news.value = [];
    }

    error.value = newsApi.error.value;
    newsLoading.value = false;
}

const fetchCategories = async () => {
    await categoriesApi.fetchData("/api/categories?type=1");
    categories.value = categoriesApi.data.value;
};

onMounted(() => {
    fetchNews();
    fetchCategories();
});
</script>

<template>
    <Head title="Berita Desa" />
    <UserLayout>
        <main
            class="flex flex-col items-center justify-center min-h-screen bg-type1 font-merriweather"
        >
            <!-- Judul -->
            <h1
                class="xl:text-2xl sm:text-xl text-md font-semibold text-accent1 mt-40"
            >
                Berita Desa
            </h1>
            <!-- Search Bar & Filter -->
            <div class="flex items-center justify-center w-full max-w-3xl mx-auto mt-6 relative">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Cari Informasi atau Kegiatan..."
                    class="flex-grow py-2 border-2 border-accent1 bg-type1 shadow-lg rounded-full italic font-light px-6"
                />
                <!-- Button Ikon Search -->
                <button
                    class="absolute right-20 text-accent1"
                    @click="handleSearch"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        height="24px"
                        viewBox="0 -960 960 960"
                        width="24px"
                        fill="244031"
                    >
                        <path
                            d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"
                        />
                    </svg>
                </button>
                <!-- Button Filter Waktu Berita -->
                <div class="relative inline-block">
                    <button
                        @click="openFilter"
                        class="ml-5 w-11 h-11 border-2 border-accent1 shadow-lg rounded-full text-accent1 hover:text-white transition"
                    >
                        <svg
                            class="ml-2"
                            xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="244031"
                        >
                            <path
                                d="M520-600v-80h120v-160h80v160h120v80H520Zm120 480v-400h80v400h-80Zm-400 0v-160H120v-80h320v80H320v160h-80Zm0-320v-400h80v400h-80Z"
                            />
                        </svg>
                    </button>
                    <!-- Pop Up Filter Berita -->
                    <div
                        v-if="showFilterModal"
                        class="absolute left-full top-12 flex items-start justify-start -ml-5"
                    >
                        <div class="bg-white rounded-lg p-3 w-72 shadow-lg relative">
                            <button
                                class="absolute top-2 right-2 text-gray-400 hover:text-gray-700"
                                @click="closeFilter"
                            >&#10005;</button>
                            <div class="mb-2 ml-2 text-accent1 text-base">Waktu</div>
                            <div class="flex flex-col gap-2 text-accent1 text-xs ml-2">
                                <label class="flex gap-2 custom-radio">
                                    <input type="radio" v-model="filterWaktu" value="semua" />
                                    Semua Waktu
                                </label>
                                <label class="flex gap-2 custom-radio">
                                    <input type="radio" v-model="filterWaktu" value="hari_ini" />
                                    Hari ini
                                </label>
                                <label class="flex gap-2 custom-radio">
                                    <input type="radio" v-model="filterWaktu" value="kemarin" />
                                    Kemarin
                                </label>
                                <label class="flex gap-2 custom-radio">
                                    <input type="radio" v-model="filterWaktu" value="sebulan" />
                                    Sebulan lalu
                                </label>
                                <label class="flex items-center gap-2 custom-radio">
                                    <input type="radio" v-model="filterWaktu" value="custom" />
                                    Pilih waktu
                                </label>
                                <div v-if="filterWaktu === 'custom'" class="flex gap-2 mt-2">
                                    <div class="relative w-28">
                                        <input
                                            type="date"
                                            v-model="tanggalMulai"
                                            class="border rounded px-2 py-1 text-accent1 text-xxs w-full h-6"
                                            style="appearance: none; -webkit-appearance: none;"
                                        />
                                        <svg class="custom-calendar-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="244031">
                                            <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-160 0q-17 0-28.5-11.5T280-440q0-17 11.5-28.5T320-480q17 0 28.5 11.5T360-440q0 17-11.5 28.5T320-400Zm320 0q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-160 0q-17 0-28.5-11.5T280-280q0-17 11.5-28.5T320-320q17 0 28.5 11.5T360-280q0 17-11.5 28.5T320-240Zm320 0q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z"/>
                                        </svg>
                                    </div>
                                    <span class="mt-1">-</span>
                                    <div class="relative w-28">
                                        <input
                                            type="date"
                                            v-model="tanggalAkhir"
                                            class="border rounded px-2 py-1 text-accent1 text-xxs w-full h-6"
                                            style="appearance: none; -webkit-appearance: none;"
                                        />
                                        <svg class="custom-calendar-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="244031">
                                            <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-160 0q-17 0-28.5-11.5T280-440q0-17 11.5-28.5T320-480q17 0 28.5 11.5T360-440q0 17-11.5 28.5T320-400Zm320 0q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-160 0q-17 0-28.5-11.5T280-280q0-17 11.5-28.5T320-320q17 0 28.5 11.5T360-280q0 17-11.5 28.5T320-240Zm320 0q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end mt-4">
                                <button
                                    class="px-3 py-1 bg-accent1 text-white text-xs rounded hover:bg-accent2"
                                    @click="closeFilter"
                                >
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Kategori Berita -->
            <div
                class="flex items-center justify-center w-full max-w-3xl mx-auto xl:space-x-7 space-x-5 mt-6 xl:text-lg text-sm text-accent1"
            >
                <button
                    @click="selectCategory('Semua')"
                    class="hover:underline"
                    :class="{ 'font-medium': selectedCategory === 'Semua' }"
                >
                    Semua
                </button>
                <button
                    @click="selectCategory('Populer')"
                    class="hover:underline"
                    :class="{ 'font-medium': selectedCategory === 'Populer' }"
                >
                    Populer
                </button>

                <!-- Dynamic categories from API -->
                <button
                    v-for="category in categories"
                    :key="category.id"
                    @click="selectCategory(category.id)"
                    class="hover:underline"
                    :class="{
                        'font-medium': selectedCategory === category.id,
                    }"
                >
                    {{ category.name }}
                </button>

                <!-- Button Kategori Berita Lainnya -->
                <button
                    class="transform w-11 h-11 text-accent1 hover:text-white transition"
                >
                    <svg 
                        xmlns="http://www.w3.org/2000/svg" 
                        height="24px" 
                        viewBox="0 -960 960 960" 
                        width="24px" fill="244031">
                        <path d="M80-160v-160h160v160H80Zm240 0v-160h560v160H320ZM80-400v-160h160v160H80Zm240 0v-160h560v160H320ZM80-640v-160h160v160H80Zm240 0v-160h560v160H320Z"/>
                    </svg>
                </button>
            </div>
            <!--Berita Terkini-->
            <h1
                class="w-full text-center text-xl font-bold text-accent1 mt-5 mr-96 pr-96 -ml-60"
            >
                Berita Terkini
            </h1>
            <article>
                <!-- Loading State -->
                <div
                    v-if="newsLoading"
                    class="flex justify-center items-center py-12"
                >
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-accent1"
                    ></div>
                </div>

                <!-- Error State -->
                <div v-else-if="error || news.length === 0" class="text-center py-12">
                    <p class="text-gray-500 text-lg">
                        {{ error ? error : 'Berita tidak ditemukan.' }}
                    </p>
                </div>

                <!-- News Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7 xl:max-w-6xl max-w-xl mb-4"
                >
                    <NewsCard
                        v-for="newsItem in news"
                        :key="newsItem.id"
                        :href="`/berita/${newsItem.slug}`"
                        :isHome="true"
                        :news="newsItem"
                    />

                    <!-- Empty State -->
                    <div
                        v-if="news.length === 0"
                        class="col-span-full text-center py-12"
                    >
                        <p class="text-gray-500 text-lg">
                            Belum ada berita yang tersedia.
                        </p>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-5 gap-2 mb-10">
                    <button
                        class="w-8 h-8 rounded-full flex items-center justify-center bg-accent1 text-type1 text-xs"
                        :disabled="pagination === 1"
                        @click="pagination--"
                    >&lt;</button>
                    <button
                        v-for="p in totalPages"
                        :key="p"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-xs"
                        :class="pagination === p ? 'bg-accent1 text-type1' : 'border text-accent1 bg-type1'"
                        @click="pagination = p"
                    >{{ p }}</button>
                    <button
                        class="w-8 h-8 rounded-full flex items-center justify-center bg-accent1 text-type1 text-xs"
                        :disabled="pagination === totalPages"
                        @click="pagination++"
                    >&gt;</button>
                </div>
            </article>
        </main>
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

.custom-calendar-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    width: 22px;
    height: 22px;
}

.custom-calendar-icon {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    width: 16px;
}
input[type="date"]::-webkit-calendar-picker-indicator {
    opacity: 0;
}
</style>
