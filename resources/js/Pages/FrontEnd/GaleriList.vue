<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import UserLayout from '@/Layouts/UserLayout.vue';
import { Link } from '@inertiajs/vue3';
import useApiFetch from "@/Utility/useApiFetch";
import Search from '@/Components/FrontendComponent/Search.vue';
import Pagination from '@/Components/FrontendComponent/Pagination.vue';

const gallery = ref([]);
const album = ref([]);
const galleryTimeout = ref(false);
const albumImages = ref({}); // Store individual album images
const galeriTerkiniAlbums = ref([]); // Store albums for Galeri Terkini (always from page 1)
const galleryLoading = ref(true);
const categories = ref([]); // Store categories
const selectedCategory = ref(null); // Store selected category
const showDropdown = ref(false); // Control dropdown visibility
const pagination = ref([]);

const fetchData = async () => {
    const galleryApi = useApiFetch();
    pagination.value = galleryApi; // Reset pagination

    // Set timeout for gallery loading
    setTimeout(() => {
        galleryTimeout.value = true;
    }, 10000);

    try {
        // Build API URL with pagination and filters
        let apiUrl = `/api/gallery?page=${pageAlbumDesa.value}`;
        if (searchQuery.value) {
            apiUrl += `&search=${encodeURIComponent(searchQuery.value)}`;
        }
        if (selectedCategory.value) {
            apiUrl += `&category=${selectedCategory.value}`;
        }

        await galleryApi.fetchData(apiUrl);

        // Handle different possible response structures
        let responseData = galleryApi.data.value;

        // Check if response is wrapped in another object
        if (responseData && typeof responseData === 'object') {
            // Try direct access first
            if (responseData.success && responseData.data && Array.isArray(responseData.data)) {
                gallery.value = responseData.data;
            }
            // Try if data is nested deeper
            else if (responseData.data && responseData.data.success && responseData.data.data) {
                gallery.value = responseData.data.data;
            }
            // Try if it's just an array
            else if (Array.isArray(responseData)) {
                gallery.value = responseData;
            }
            else {
                gallery.value = [];
            }
        } else {
            gallery.value = [];
        }

        // Update pagination using the correct structure
        if (responseData?.meta) {
            totalPagesAlbumDesa.value = responseData.meta.last_page;
        } else if (galleryApi.pagination && galleryApi.pagination.last_page) {
            totalPagesAlbumDesa.value = galleryApi.pagination.last_page;
        }
        
        galleryLoading.value = false;
    } catch (error) {
        galleryLoading.value = false;
        gallery.value = [];
    }
};

// Function to fetch gallery data for Galeri Terkini (always page 1)
const fetchGaleriTerkiniData = async () => {
    const galeriTerkiniApi = useApiFetch();
    try {
        // Always fetch from page 1 for Galeri Terkini, no filters applied
        await galeriTerkiniApi.fetchData('/api/gallery?page=1');
        
        let responseData = galeriTerkiniApi.data.value;
        
        // Handle different possible response structures
        if (responseData && typeof responseData === 'object') {
            if (responseData.success && responseData.data && Array.isArray(responseData.data)) {
                galeriTerkiniAlbums.value = responseData.data.slice(0, 5); // Take first 5 albums
            }
            else if (responseData.data && responseData.data.success && responseData.data.data) {
                galeriTerkiniAlbums.value = responseData.data.data.slice(0, 5);
            }
            else if (Array.isArray(responseData)) {
                galeriTerkiniAlbums.value = responseData.slice(0, 5);
            }
            else {
                galeriTerkiniAlbums.value = [];
            }
        } else {
            galeriTerkiniAlbums.value = [];
        }
    } catch (error) {
        galeriTerkiniAlbums.value = [];
    }
};

// Function to fetch individual album images
const fetchAlbumImages = async (slug) => {
    if (albumImages.value[slug]) return; // Already fetched

    const albumApi = useApiFetch();
    try {
        await albumApi.fetchData(`/api/gallery/${slug}`);


        // Update this to match the actual API response structure
        if (albumApi.data.value?.images && Array.isArray(albumApi.data.value.images)) {
            albumImages.value[slug] = albumApi.data.value.images;
        }
    } catch (error) {
    }
};

// Get album images for display
const getAlbumImages = (slug) => {
    return albumImages.value[slug] || [];
};

// Get image URL with proper base path
const getImageUrl = (path) => {
    return `/storage/${path}`;
};

// Format date for display
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// State untuk input search
const searchQuery = ref('')

// Fungsi pencarian
function handleSearch(query) {
    searchQuery.value = query;
    pageAlbumDesa.value = 1; // Reset to first page when searching
    fetchData();
}

// Function to handle search input changes
function handleSearchInput(query) {
    searchQuery.value = query;
}

const pageAlbumDesa = ref(1);
const totalPagesAlbumDesa = ref(1); // Make it reactive
const pageGaleriTerkini = ref(1);
const totalPagesGaleriTerkini = 2;

// Function to fetch categories
const fetchCategories = async () => {
    const categoriesApi = useApiFetch();
    try {
        await categoriesApi.fetchData("/api/categories?type=1");
        
        // The API response is directly an array, not wrapped in success/data structure
        if (Array.isArray(categoriesApi.data.value)) {
            categories.value = categoriesApi.data.value;
        } else if (categoriesApi.data.value?.success && categoriesApi.data.value?.data && Array.isArray(categoriesApi.data.value.data)) {
            categories.value = categoriesApi.data.value.data;
        } else {
            categories.value = [];
        }
    } catch (error) {
        categories.value = [];
    }
};

// Function to handle category selection
const handleCategorySelect = (categoryId = null) => {
    selectedCategory.value = categoryId;
    pageAlbumDesa.value = 1; // Reset to first page when category changes
    showDropdown.value = false; // Close dropdown after selection
    fetchData(); // Refetch data with new category filter
};

// Close dropdown when clicking outside
const handleOutsideClick = (event) => {
    const dropdownContainer = document.querySelector('.dropdown-container');
    if (dropdownContainer && !dropdownContainer.contains(event.target)) {
        showDropdown.value = false;
    }
};

// Function to toggle dropdown
const toggleDropdown = (event) => {
    event.stopPropagation();
    showDropdown.value = !showDropdown.value;
};

// Function to handle pagination
const handlePageChange = async (page) => {
    pageAlbumDesa.value = page;
    await fetchData();
    
    // Fetch images for new page albums after data is loaded
    if (gallery.value && gallery.value.length > 0) {
        for (let i = 0; i < gallery.value.length; i++) {
            if (gallery.value[i] && gallery.value[i].slug) {
                await fetchAlbumImages(gallery.value[i].slug);
            }
        }
    }
};

// Get album thumbnail URL
const getAlbumThumbnail = (album) => {
    if (album.thumbnail) {
        return `/storage/${album.thumbnail}`;
    }
    return '/images/default-album.jpg'; // fallback image
};

// Function to clear all filters
const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = null;
    pageAlbumDesa.value = 1;
    showDropdown.value = false;
    fetchData();
};

onMounted(async () => {
    // Add global click listener for closing dropdown
    document.addEventListener('click', handleOutsideClick);
    
    await Promise.all([
        fetchCategories(),
        fetchData(),
        fetchGaleriTerkiniData() // Fetch Galeri Terkini data
    ]);

    // Fetch images for ALL albums automatically
    if (gallery.value && gallery.value.length > 0) {
        for (let i = 0; i < gallery.value.length; i++) {
            if (gallery.value[i] && gallery.value[i].slug) {
                await fetchAlbumImages(gallery.value[i].slug);
            }
        }
    }
});

// Cleanup on unmount
onUnmounted(() => {
    document.removeEventListener('click', handleOutsideClick);
});
</script>

<template>
    <UserLayout>
        <!--Menu Bar-->
        <main class="flex max-w-screen-4xl mx-auto flex-col items-center justify-center min-h-screen pt-32 4xl:pt-44 pb-8 xl:pb-16 bg-type1 font-merriweather px-[6.56%]">
            <article
                class="flex mb-5 xl:mb-10 lg:px-[9%] flex-col items-center justify-center bg-type1 font-merriweather space-y-5 w-full">
                <!-- Judul -->
                <h1 class="xl:text-2xl text-xl font-semibold text-accent1 justify-center mt-8">Galeri Desa</h1>
                <!-- Search Bar -->
                <Search 
                    class="px-[10%] text-accent1"
                    :model-value="searchQuery"
                    @update:model-value="handleSearchInput"
                    @search="handleSearch"
                    placeholder="Cari album galeri..."
                ></Search>
                <!-- Kategori Galeri -->
                <div class="flex justify-center space-x-7 text-lg text-accent2 w-full max-w-screen">
                    <!-- Always visible "Semua" button -->
                    <button 
                        class="hover:underline whitespace-nowrap"
                        :class="selectedCategory === null ? 'font-medium' : ''"
                        @click="handleCategorySelect(null)">
                        Semua
                    </button>
                    
                    <!-- Categories visible on larger screens -->
                    <div class="hidden xl:flex space-x-7">
                        <button 
                            v-for="category in categories.slice(0, 8)" 
                            :key="category.id"
                            class="hover:underline whitespace-nowrap"
                            :class="selectedCategory === category.id ? 'font-medium' : ''"
                            @click="handleCategorySelect(category.id)">
                            {{ category.name }}
                        </button>
                    </div>
                    
                    <!-- Categories visible on large screens (limited to 5) -->
                    <div class="hidden lg:flex xl:hidden space-x-7">
                        <button 
                            v-for="category in categories.slice(0, 5)" 
                            :key="category.id"
                            class="hover:underline whitespace-nowrap"
                            :class="selectedCategory === category.id ? 'font-medium' : ''"
                            @click="handleCategorySelect(category.id)">
                            {{ category.name }}
                        </button>
                    </div>
                    
                    <!-- Categories visible on medium screens (limited to 4) -->
                    <div class="hidden md:flex lg:hidden space-x-7">
                        <button 
                            v-for="category in categories.slice(0, 4)" 
                            :key="category.id"
                            class="hover:underline whitespace-nowrap"
                            :class="selectedCategory === category.id ? 'font-medium' : ''"
                            @click="handleCategorySelect(category.id)">
                            {{ category.name }}
                        </button>
                    </div>
                    
                    <!-- Categories visible on small screens (limited to 3) -->
                    <div class="flex md:hidden space-x-7">
                        <button 
                            v-for="category in categories.slice(0, 1)" 
                            :key="category.id"
                            class="hover:underline whitespace-nowrap"
                            :class="selectedCategory === category.id ? 'font-medium' : ''"
                            @click="handleCategorySelect(category.id)">
                            {{ category.name }}
                        </button>
                    </div>
                    
                    <!-- Dropdown button -->
                    <div class="relative dropdown-container">
                        <button 
                            @click="toggleDropdown"
                            class="flex items-center space-x-1 hover:bg-gray-100 p-2 rounded transition-colors duration-200 bg-transparent">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"
                                fill="#3B614A">
                                <path
                                    d="M80-160v-160h160v160H80Zm240 0v-160h560v160H320ZM80-400v-160h160v160H80Zm240 0v-160h560v160H320ZM80-640v-160h160v160H80Zm240 0v-160h560v160H320Z" />
                            </svg>
                            <span class="hidden sm:inline text-sm text-accent2"></span>
                            
                        </button>
                        
                        <!-- Dropdown menu -->
                        <div 
                            v-if="showDropdown"
                            class="fixed bg-white border border-gray-200 rounded-lg shadow-xl min-w-48 max-h-64 overflow-y-auto"
                            :style="{
                                top: '100%',
                                right: '0',
                                zIndex: 9999,
                                position: 'absolute'
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
                                        v-for="category in categories.slice(3)" 
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
                                        v-for="category in categories.slice(4)" 
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
                                        v-for="category in categories.slice(5)" 
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
                                        v-for="category in categories.slice(8)" 
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
            </article>
            <article class="relative flex-grow items-center self-start">
                <h1 class="text-xl font-bold text-accent1">
                    Album Desa
                </h1>
            </article>
            <article class="w-full mt-5 mb-10">
                <!-- Loading state for album cards -->
                <div v-if="galleryLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="n in 6" :key="`skeleton-${n}`" class="bg-white rounded-lg shadow p-3 min-h-48 flex flex-col">
                        <!-- Skeleton title and icon -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="h-6 bg-gray-300 rounded animate-pulse w-3/4"></div>
                            <div class="w-8 h-8 bg-gray-300 rounded animate-pulse"></div>
                        </div>
                        
                        <!-- Skeleton images -->
                        <div class="flex gap-2 mb-2">
                            <div class="w-3/5 h-32 rounded bg-gray-300 animate-pulse"></div>
                            <div class="flex flex-col gap-2 w-2/5">
                                <div class="w-full rounded bg-gray-300 animate-pulse" style="height: 60px;"></div>
                                <div class="w-full rounded bg-gray-300 animate-pulse" style="height: 60px;"></div>
                            </div>
                        </div>
                        
                        <!-- Skeleton date -->
                        <div class="h-4 bg-gray-300 rounded animate-pulse w-1/3 mt-auto ml-auto"></div>
                    </div>
                </div>
                
                <!-- No gallery found message -->
                <div v-else-if="gallery.length === 0" class="flex flex-col items-center justify-center py-16">
                    <svg xmlns="http://www.w3.org/2000/svg" height="80px" viewBox="0 -960 960 960" width="80px" fill="#9CA3AF" class="mb-4">
                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm40-80h480L570-480 450-320l-90-120-120 160Zm-40 80v-560 560Z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Galeri Ditemukan</h3>
                    <p class="text-gray-500 text-center max-w-md">
                        Maaf, tidak ada album galeri yang sesuai dengan pencarian atau kategori yang dipilih.
                    </p>
                    <button 
                        v-if="searchQuery || selectedCategory"
                        @click="clearFilters"
                        class="mt-4 px-4 py-2 bg-accent2 text-white rounded-lg hover:bg-accent2/90 transition-colors duration-200">
                        Tampilkan Semua Galeri
                    </button>
                </div>
                
                <!-- Actual album cards -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="album in gallery" :key="album.slug" :href="`/galeri/${album.slug}`"
                        class="bg-white rounded-lg shadow p-3 min-h-48 flex flex-col hover:shadow-lg transition-shadow duration-300">
                        <!-- Judul dan ikon -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-accent1 text-lg line-clamp-1" :title="album.title">{{ album.title }}</span>
                            <span class="material-symbols-outlined text-accent1 text-2xl">gallery_thumbnail</span>
                        </div>

                        <!-- Gambar utama dan 2 gambar kecil -->
                        <div class="flex gap-2 mb-2">
                            <!-- Check if album images are loaded -->
                            <template v-if="getAlbumImages(album.slug).length > 0">
                                <!-- Main image (first image) with hover effect -->
                                <div class="w-3/5 h-32 relative group cursor-pointer overflow-hidden rounded">
                                    <img :src="getImageUrl(getAlbumImages(album.slug)[0].path)"
                                        :alt="getAlbumImages(album.slug)[0].alt" 
                                        class="w-full h-full object-cover transition-all duration-300 group-hover:brightness-75" />
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <span class="text-white text-sm font-semibold">Selengkapnya</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 w-2/5">
                                    <!-- Second image with hover effect -->
                                    <template v-if="getAlbumImages(album.slug).length > 1">
                                        <div class="w-full relative group cursor-pointer overflow-hidden rounded" style="height: 60px;">
                                            <img :src="getImageUrl(getAlbumImages(album.slug)[1].path)"
                                                :alt="getAlbumImages(album.slug)[1].alt"
                                                class="w-full h-full object-cover transition-all duration-300 group-hover:brightness-75" />
                                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                <span class="text-white text-xs font-semibold">Selengkapnya</span>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div
                                            class="rounded bg-gray-200 w-full flex items-center justify-center text-xs text-gray-400"
                                            style="height: 60px;">
                                            No Image
                                        </div>
                                    </template>

                                    <!-- Third image with hover effect -->
                                    <template v-if="getAlbumImages(album.slug).length > 2">
                                        <div class="w-full relative group cursor-pointer overflow-hidden rounded" style="height: 60px;">
                                            <img :src="getImageUrl(getAlbumImages(album.slug)[2].path)"
                                                :alt="getAlbumImages(album.slug)[2].alt"
                                                class="w-full h-full object-cover transition-all duration-300 group-hover:brightness-75" />
                                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                <span class="text-white text-xs font-semibold">Selengkapnya</span>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div
                                            class="rounded bg-gray-200 w-full flex items-center justify-center text-xs text-gray-400"
                                            style="height: 60px;">
                                            No Image
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <!-- Show album thumbnail as fallback while loading individual images -->
                            <template v-else-if="album.thumbnail">
                                <div class="w-3/5 h-32 relative group cursor-pointer overflow-hidden rounded">
                                    <img :src="getAlbumThumbnail(album)" :alt="album.alt" 
                                         class="w-full h-full object-cover transition-all duration-300 group-hover:brightness-75" />
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <span class="text-white text-sm font-semibold">Selengkapnya</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 w-2/5">
                                    <div class="w-full rounded bg-gray-300 animate-pulse" style="height: 60px;"></div>
                                    <div class="w-full rounded bg-gray-300 animate-pulse" style="height: 60px;"></div>
                                </div>
                            </template>
                            <!-- Loading state while fetching album images -->
                            <template v-else>
                                <div class="w-3/5 h-32 rounded bg-gray-300 animate-pulse"></div>
                                <div class="flex flex-col gap-2 w-2/5">
                                    <div class="w-full rounded bg-gray-300 animate-pulse" style="height: 60px;"></div>
                                    <div class="w-full rounded bg-gray-300 animate-pulse" style="height: 60px;"></div>
                                </div>
                            </template>
                        </div>
                        <!-- Tanggal -->
                        <div class="text-xs text-right text-gray-500 mt-auto">
                            {{ formatDate(album.created_at) }}
                        </div>
                    </Link>
                </div>
                <!-- Pagination -->
                <Pagination
                    v-if="!galleryLoading && gallery.length > 0"
                    :current-page="pagination?.pagination?.current_page || pageAlbumDesa"
                    :last-page="pagination?.pagination?.last_page || totalPagesAlbumDesa"
                    @page-changed="handlePageChange"
                ></Pagination>
            </article>
            <!--Galeri Terkini-->
            <article class="w-full flex flex-col pb-8 lg:pb-24 bg-type1 font-merriweather space-y-5">
                <h1 class="text-xl font-bold text-accent1">
                    Galeri Terkini
                </h1>
                
                <div v-if="galleryLoading || galeriTerkiniAlbums.length === 0" class="flex flex-col w-full justify-between space-y-4">
                    <div class="flex flex-row w-full h-full justify-between space-x-5">
                        <div class="w-3/5 h-[150px] sm:h-[225px] md:h-[300px] rounded-2xl bg-gray-300 animate-pulse"></div>
                        <div class="w-2/5 h-[150px] sm:h-[225px] md:h-[300px] rounded-2xl bg-gray-300 animate-pulse"></div>
                    </div>
                    <div class="flex flex-col sm:flex-row w-full justify-between space-x-5">
                        <div class="w-full sm:w-1/3 h-[200px] lg:w-96 rounded-xl bg-gray-300 animate-pulse"></div>
                        <div class="w-full sm:w-1/3 h-[200px] lg:w-96 rounded-xl bg-gray-300 animate-pulse"></div>
                        <div class="w-full sm:w-1/3 h-[200px] lg:w-96 rounded-xl bg-gray-300 animate-pulse"></div>
                    </div>
                </div>
                
                <div v-else class="flex flex-col w-full justify-between space-y-5">
                    <div class="flex flex-row w-full h-full justify-between space-x-5">
                        <!-- First album thumbnail (large) -->
                        <Link v-if="galeriTerkiniAlbums[0]" :href="`/galeri/${galeriTerkiniAlbums[0].slug}`" 
                              class="w-3/5 relative group cursor-pointer overflow-hidden rounded-2xl">
                            <img :src="getAlbumThumbnail(galeriTerkiniAlbums[0])" :alt="galeriTerkiniAlbums[0].alt"
                                 class="w-full h-[150px] sm:h-[225px] md:h-[300px] object-cover transition-all duration-300 group-hover:brightness-75" />
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span class="text-white text-lg md:text-xl font-semibold">{{ galeriTerkiniAlbums[0].title }}</span>
                            </div>
                        </Link>
                        
                        <!-- Second album thumbnail (medium) -->
                        <Link v-if="galeriTerkiniAlbums[1]" :href="`/galeri/${galeriTerkiniAlbums[1].slug}`"
                              class="w-2/5 relative group cursor-pointer overflow-hidden rounded-2xl">
                            <img :src="getAlbumThumbnail(galeriTerkiniAlbums[1])" :alt="galeriTerkiniAlbums[1].alt"
                                 class="w-full h-[150px] sm:h-[225px] md:h-[300px] object-cover transition-all duration-300 group-hover:brightness-75" />
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span class="text-white text-lg md:text-xl font-semibold">{{ galeriTerkiniAlbums[1].title }}</span>
                            </div>
                        </Link>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row w-full justify-between space-y-3 sm:space-y-0 sm:space-x-5">
                        <!-- Third album thumbnail -->
                        <Link v-if="galeriTerkiniAlbums[2]" :href="`/galeri/${galeriTerkiniAlbums[2].slug}`"
                              class="w-full sm:w-1/3 h-[200px] lg:w-96 relative group cursor-pointer overflow-hidden rounded-xl">
                            <img :src="getAlbumThumbnail(galeriTerkiniAlbums[2])" :alt="galeriTerkiniAlbums[2].alt"
                                 class="w-full h-full object-cover transition-all duration-300 group-hover:brightness-75" />
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span class="text-white text-lg md:text-xl font-semibold">{{ galeriTerkiniAlbums[2].title }}</span>
                            </div>
                        </Link>
                        
                        <!-- Fourth album thumbnail -->
                        <Link v-if="galeriTerkiniAlbums[3]" :href="`/galeri/${galeriTerkiniAlbums[3].slug}`"
                              class="w-full sm:w-1/3 h-[200px] lg:w-96 relative group cursor-pointer overflow-hidden rounded-xl">
                            <img :src="getAlbumThumbnail(galeriTerkiniAlbums[3])" :alt="galeriTerkiniAlbums[3].alt"
                                 class="w-full h-full object-cover transition-all duration-300 group-hover:brightness-75" />
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span class="text-white text-lg md:text-xl font-semibold">{{ galeriTerkiniAlbums[3].title }}</span>
                            </div>
                        </Link>
                        
                        <!-- Fifth album thumbnail -->
                        <Link v-if="galeriTerkiniAlbums[4]" :href="`/galeri/${galeriTerkiniAlbums[4].slug}`"
                              class="w-full sm:w-1/3 h-[200px] lg:w-96 relative group cursor-pointer overflow-hidden rounded-xl">
                            <img :src="getAlbumThumbnail(galeriTerkiniAlbums[4])" :alt="galeriTerkiniAlbums[4].alt"
                                 class="w-full h-full object-cover transition-all duration-300 group-hover:brightness-75" />
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span class="text-white text-lg md:text-xl font-semibold">{{ galeriTerkiniAlbums[4].title }}</span>
                            </div>
                        </Link>
                    </div>
                </div>
            </article>
        </main>
    </UserLayout>
</template>