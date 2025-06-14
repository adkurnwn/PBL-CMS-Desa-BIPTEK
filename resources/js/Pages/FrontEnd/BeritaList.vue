<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import useApiFetch from "@/Utility/useApiFetch";
import NewsCard from "@/Components/NewsCard.vue";
import Search from "@/Components/FrontendComponent/Search.vue";
import Pagination from "@/Components/FrontendComponent/Pagination.vue";
import { ref, onMounted, onUnmounted } from "vue";
import useFilter from "@/Utility/useFilter";
import {Head } from "@inertiajs/vue3";
import axios from "axios";

const news = ref([]);
const categories = ref([]);
const error = ref(null);
const newsLoading = ref(false);
const pagination = ref({});
const totalPages = ref(1);
const newsApi = useApiFetch();
const categoriesApi = useApiFetch();
const showFilterModal = ref(false);
const showDropdown = ref(false); // Add dropdown state
const filterWaktu = ref("semua");
const tanggalMulai = ref("");
const tanggalAkhir = ref("");

function openFilter() {
    showFilterModal.value = true;
}
function closeFilter() {
    showFilterModal.value = false;
}

// Handle filter button click from Search component
const handleFilter = () => {
    openFilter();
};

// Handle search input changes from Search component
const handleSearchInput = (query) => {
    searchQuery.value = query;
};

// Handle search from Search component
const handleSearchSubmit = (query) => {
    handleSearch();
};

const {
    searchQuery,
    selectedCategory,
    handleSearch,
    selectCategory,
    goToPage,
} = useFilter(fetchNews);

const applyTimeFilter = () => {
    fetchNews({ 
        search: searchQuery.value, 
        category: selectedCategory.value === 'Semua' ? null : selectedCategory.value,
        timeFilter: filterWaktu.value,
        startDate: tanggalMulai.value,
        endDate: tanggalAkhir.value,
        page: 1 
    });
    closeFilter();
};

async function fetchNews({ search = "", category = null, page = 1, timeFilter = "semua", startDate = "", endDate = "" } = {}) {
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
    
    // Add time filter parameters
    if (timeFilter && timeFilter !== "semua") {
        params.append("time_filter", timeFilter);
        if (timeFilter === "custom") {
            if (startDate) params.append("start_date", startDate);
            if (endDate) params.append("end_date", endDate);
        }
    }
    
    params.append("page", page);

    await newsApi.fetchData(`/api/news?${params.toString()}`);

    if (newsApi.data.value) {
        news.value = newsApi.data.value;
        pagination.value = newsApi.pagination.value || {};
        
        // Update total pages from pagination data
        if (newsApi.pagination?.value?.last_page) {
            totalPages.value = newsApi.pagination.value.last_page;
        }
    } else {
        news.value = [];
        pagination.value = {};
        totalPages.value = 1;
    }

    error.value = newsApi.error.value;
    newsLoading.value = false;
}

const fetchCategories = async () => {
    await categoriesApi.fetchData("/api/categories?type=1");
    categories.value = categoriesApi.data.value;
};

// Function to handle category selection
const handleCategorySelect = (categoryId = null) => {
    if (categoryId === null) {
        selectCategory('Semua');
    } else if (categoryId === 'Populer') {
        selectCategory('Populer');
    } else {
        selectCategory(categoryId);
    }
    showDropdown.value = false; // Close dropdown after selection
};

// Function to toggle dropdown
const toggleDropdown = (event) => {
    event.stopPropagation();
    showDropdown.value = !showDropdown.value;
};

// Close dropdown when clicking outside
const handleOutsideClick = (event) => {
    const dropdownContainer = document.querySelector('.dropdown-container');
    const filterContainer = document.querySelector('.filter-container');
    
    if (dropdownContainer && !dropdownContainer.contains(event.target) &&
        filterContainer && !filterContainer.contains(event.target)) {
        showDropdown.value = false;
        showFilterModal.value = false;
    }
};

// Function to handle pagination with time filter
const handlePageChange = async (page) => {
    await fetchNews({ 
        search: searchQuery.value, 
        category: selectedCategory.value === 'Semua' ? null : selectedCategory.value, 
        timeFilter: filterWaktu.value,
        startDate: tanggalMulai.value,
        endDate: tanggalAkhir.value,
        page: page 
    });
};

onMounted(() => {
    // Add global click listener for closing dropdown
    document.addEventListener('click', handleOutsideClick);
    
    fetchNews();
    fetchCategories();
});

// Cleanup on unmount
onUnmounted(() => {
    document.removeEventListener('click', handleOutsideClick);
});
</script>

<template>
    <Head title="Berita Desa" />
    <UserLayout>
        <main
            class="flex flex-col w-full mx-auto max-w-screen-4xl px-[6.56%] py-4 mt-20 md:py-10 bg-type1 font-merriweather"
        >
        <div class="flex flex-col items-center justify-center">
            <!-- Judul -->
            <h1
                class="xl:text-2xl text-xl font-semibold text-accent1 justify-center mt-8"
            >
                Berita Desa
            </h1>
            <!-- Search Bar & Filter -->
            <div class="relative flex w-full flex-grow items-center justify-center max-w-7xl mx-auto mb-4">
            <div class="flex-grow mt-6">
                    <Search
                        class="lg:px-[3%]"
                        type="with-filter"
                        :model-value="searchQuery"
                        @update:model-value="handleSearchInput"
                        @search="handleSearchSubmit"
                        @filter="handleFilter"
                        placeholder="Cari Berita..."
                    />
                    
                    <!-- Filter Dropdown -->
                    <div
                        v-if="showFilterModal"
                        class="absolute bg-white border border-gray-200 rounded-lg shadow-xl w-80 z-50"
                        :class="{
                            'right-0': true,
                            'left-0 sm:left-auto sm:right-0': true
                        }"
                        style="top: calc(100% + 8px);"
                    >
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-accent1 text-lg font-semibold">Filter Waktu</div>
                                <button
                                    class="text-gray-400 hover:text-gray-700 p-1"
                                    @click="closeFilter"
                                >
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="flex flex-col gap-3 text-accent1 text-sm">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filterWaktu" value="semua" class="text-accent1" />
                                    Semua Waktu
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filterWaktu" value="hari_ini" class="text-accent1" />
                                    Hari ini
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filterWaktu" value="kemarin" class="text-accent1" />
                                    Kemarin
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filterWaktu" value="sebulan" class="text-accent1" />
                                    Sebulan lalu
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filterWaktu" value="custom" class="text-accent1" />
                                    Pilih waktu
                                </label>
                                
                                <div v-if="filterWaktu === 'custom'" class="flex flex-col gap-2 mt-2 pl-6">
                                    <div class="text-xs text-gray-600 mb-2">Dari tanggal:</div>
                                    <input
                                        type="date"
                                        v-model="tanggalMulai"
                                        class="border rounded px-3 py-2 text-accent1 text-sm w-full"
                                    />
                                    <div class="text-xs text-gray-600 mb-2 mt-2">Sampai tanggal:</div>
                                    <input
                                        type="date"
                                        v-model="tanggalAkhir"
                                        class="border rounded px-3 py-2 text-accent1 text-sm w-full"
                                    />
                                </div>
                            </div>
                            
                            <div class="flex justify-end mt-6 gap-2">
                                <button
                                    class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50 text-sm"
                                    @click="closeFilter"
                                >
                                    Batal
                                </button>
                                <button
                                    class="px-4 py-2 bg-accent1 text-white rounded hover:bg-accent2 text-sm"
                                    @click="applyTimeFilter"
                                >
                                    Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Kategori Berita -->
            <div
                class="flex items-center justify-center w-full max-w-3xl mx-auto xl:space-x-7 space-x-5 xl:text-lg text-sm text-accent1"
            >
                <!-- Always visible "Semua" button -->
                <button
                    @click="handleCategorySelect(null)"
                    class="hover:underline whitespace-nowrap"
                    :class="{ 'font-medium': selectedCategory === 'Semua' }"
                >
                    Semua
                </button>
                
                <!-- Always visible "Populer" button -->
                <button
                    @click="handleCategorySelect('Populer')"
                    class="hover:underline whitespace-nowrap"
                    :class="{ 'font-medium': selectedCategory === 'Populer' }"
                >
                    Populer
                </button>

                <!-- Categories visible on larger screens -->
                <div class="hidden xl:flex xl:space-x-7">
                    <button
                        v-for="category in categories.slice(0, 6)"
                        :key="category.id"
                        @click="handleCategorySelect(category.id)"
                        class="hover:underline whitespace-nowrap"
                        :class="{
                            'font-medium': selectedCategory === category.id,
                        }"
                    >
                        {{ category.name }}
                    </button>
                </div>
                
                <!-- Categories visible on large screens (limited to 4) -->
                <div class="hidden lg:flex xl:hidden space-x-7">
                    <button
                        v-for="category in categories.slice(0, 4)"
                        :key="category.id"
                        @click="handleCategorySelect(category.id)"
                        class="hover:underline whitespace-nowrap"
                        :class="{
                            'font-medium': selectedCategory === category.id,
                        }"
                    >
                        {{ category.name }}
                    </button>
                </div>
                
                <!-- Categories visible on medium screens (limited to 3) -->
                <div class="hidden md:flex lg:hidden space-x-7">
                    <button
                        v-for="category in categories.slice(0, 3)"
                        :key="category.id"
                        @click="handleCategorySelect(category.id)"
                        class="hover:underline whitespace-nowrap"
                        :class="{
                            'font-medium': selectedCategory === category.id,
                        }"
                    >
                        {{ category.name }}
                    </button>
                </div>
                
                <!-- Categories visible on small screens (limited to 1) -->
                <div class="flex md:hidden space-x-7">
                    <button
                        v-for="category in categories.slice(0, 1)"
                        :key="category.id"
                        @click="handleCategorySelect(category.id)"
                        class="hover:underline whitespace-nowrap"
                        :class="{
                            'font-medium': selectedCategory === category.id,
                        }"
                    >
                        {{ category.name }}
                    </button>
                </div>

                <!-- Dropdown button -->
                <div class="relative dropdown-container">
                    <button
                        @click="toggleDropdown"
                        class="transform w-11 h-11 text-accent1 hover:text-white transition flex items-center justify-center"
                    >
                        <svg 
                            xmlns="http://www.w3.org/2000/svg" 
                            height="24px" 
                            viewBox="0 -960 960 960" 
                            width="24px" fill="244031">
                            <path d="M80-160v-160h160v160H80Zm240 0v-160h560v160H320ZM80-400v-160h160v160H80Zm240 0v-160h560v160H320ZM80-640v-160h160v160H80Zm240 0v-160h560v160H320Z"/>
                        </svg>
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div 
                        v-if="showDropdown"
                        class="absolute bg-white border border-gray-200 rounded-lg shadow-xl min-w-48 max-h-64 overflow-y-auto"
                        :style="{
                            top: '100%',
                            right: '0',
                            zIndex: 9999
                        }">
                        <div class="py-1">
                            <!-- All categories in dropdown for extra small screens -->
                            <div class="sm:hidden">
                                <button 
                                    v-for="category in categories" 
                                    :key="`dropdown-${category.id}`"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-700 transition-colors duration-150 text-sm"
                                    :class="selectedCategory === category.id ? 'font-medium bg-blue-50 text-blue-600' : ''"
                                    @click.stop="handleCategorySelect(category.id)">
                                    {{ category.name }}
                                </button>
                            </div>
                            
                            <!-- Remaining categories for small screens -->
                            <div class="hidden sm:block md:hidden">
                                <button 
                                    v-for="category in categories.slice(1)" 
                                    :key="`dropdown-sm-${category.id}`"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-700 transition-colors duration-150 text-sm"
                                    :class="selectedCategory === category.id ? 'font-medium bg-blue-50 text-blue-600' : ''"
                                    @click.stop="handleCategorySelect(category.id)">
                                    {{ category.name }}
                                </button>
                            </div>
                            
                            <!-- Remaining categories for medium screens -->
                            <div class="hidden md:block lg:hidden">
                                <button 
                                    v-for="category in categories.slice(3)" 
                                    :key="`dropdown-md-${category.id}`"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-700 transition-colors duration-150 text-sm"
                                    :class="selectedCategory === category.id ? 'font-medium bg-blue-50 text-blue-600' : ''"
                                    @click.stop="handleCategorySelect(category.id)">
                                    {{ category.name }}
                                </button>
                            </div>
                            
                            <!-- Remaining categories for large screens -->
                            <div class="hidden lg:block xl:hidden">
                                <button 
                                    v-for="category in categories.slice(4)" 
                                    :key="`dropdown-lg-${category.id}`"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-700 transition-colors duration-150 text-sm"
                                    :class="selectedCategory === category.id ? 'font-medium bg-blue-50 text-blue-600' : ''"
                                    @click.stop="handleCategorySelect(category.id)">
                                    {{ category.name }}
                                </button>
                            </div>
                            
                            <!-- Remaining categories for xl screens -->
                            <div class="hidden xl:block">
                                <button 
                                    v-for="category in categories.slice(6)" 
                                    :key="`dropdown-xl-${category.id}`"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-700 transition-colors duration-150 text-sm"
                                    :class="selectedCategory === category.id ? 'font-medium bg-blue-50 text-blue-600' : ''"
                                    @click.stop="handleCategorySelect(category.id)">
                                    {{ category.name }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <!--Berita Terkini-->
        <div class="">
            <h1
                class="w-full text-left text-xl font-bold text-accent2 mt-5 mb-4"
            >
                Berita Terkini
            </h1>
            <article>
                <!-- News Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 w-full mb-4">
                    <!-- Loading State - Show skeleton cards -->
                    <template v-if="newsLoading">
                        <NewsCard
                            v-for="n in 12"
                            :key="`skeleton-${n}`"
                            :loading="true"
                            :href="'#'"
                            :isHome="false"
                            :news="{}"
                        />
                    </template>

                    <!-- Error State -->
                    <div v-else-if="error" class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg">{{ error }}</p>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="news.length === 0" class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg">Berita tidak ditemukan.</p>
                    </div>

                    <!-- Actual News Content -->
                    <template v-else>
                        <NewsCard
                            v-for="newsItem in news"
                            :key="newsItem.id"
                            :loading="false"
                            :href="`/berita/${newsItem.slug}`"
                            :isHome="false"
                            :news="newsItem"
                        />
                    </template>
                </div>

                <!-- Pagination -->
                <Pagination
                    v-if="news.length > 0 && totalPages > 1"
                    :current-page="pagination?.current_page || 1"
                    :last-page="totalPages"
                    @page-changed="handlePageChange"
                />
            </article>
        </div>
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
