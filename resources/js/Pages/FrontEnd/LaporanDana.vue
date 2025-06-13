<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import Pagination from '@/Components/FrontendComponent/Pagination.vue';
import { ref, onMounted, computed } from 'vue';
import useApiFetch from '@/Utility/useApiFetch';
import useFilter from '@/Utility/useFilter';
import { useMeta } from '@/Utility/useMeta';
import {Head } from "@inertiajs/vue3";

// Reactive meta description
const metaDescriptionText = ref('Akses dan unduh laporan dana desa yang transparan dan akuntabel. Pantau penggunaan anggaran dana desa secara berkala.');

// Meta tags setup
const {
  metaTitle,
  metaDescription,
  metaImage,
  canonicalUrl,
  metaType,
  metaSiteName
} = useMeta({
  title: 'Laporan Dana Desa',
  description: metaDescriptionText,
  type: 'website',
  url: `${window.location.origin}/dana-desa`,
});

// Function to update description
const updateMetaData = (layoutName) => {
  metaDescriptionText.value = `Akses dan unduh laporan dana ${layoutName} yang transparan dan akuntabel. Pantau penggunaan anggaran dana desa secara berkala.`;
  return null; // Return null to not render anything
};

const laporanDanaApi = useApiFetch();

// Search inputs
const searchQuery = ref('');
const selectedYear = ref('');

const totalPages = computed(() => {
  if (!laporanDanaApi.pagination.value) return [];
  return Array.from({ length: laporanDanaApi.pagination.value.last_page }, (_, i) => i + 1);
});

const fetchLaporanDanaData = async ({
  search = '',
  year = null,
  page = 1,
} = {}) => {
  const params = new URLSearchParams();

  if (search) params.append('search', search);
  if (year && year !== '') {
    params.append('year', year);
  }
  params.append('page', page);

  await laporanDanaApi.fetchData(`/api/dana-desa?${params.toString()}`);
};

const {
  searchQuery: filterSearchQuery,
  handleSearch,
  goToPage,
} = useFilter(fetchLaporanDanaData);

// Handle search input
const handleSearchInput = () => {
  filterSearchQuery.value = searchQuery.value.trim();
  handleSearch();
};

// Handle Enter key press
const handleKeyPress = (event) => {
  if (event.key === 'Enter') {
    handleSearchInput();
  }
};

// Handle year selection
const handleYearChange = () => {
  fetchLaporanDanaData({
    search: filterSearchQuery.value,
    year: selectedYear.value,
    page: 1
  });
};

// Generate year options
const yearOptions = ref([]);

const generateYearOptions = () => {
  const currentYear = new Date().getFullYear();
  const years = [];
  for (let i = 0; i < 10; i++) {
    years.push(currentYear - i);
  }
  yearOptions.value = years;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("id-ID", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

function lihatDetail(slug) {
  window.location.href = `/dana-desa/${slug}`;
}

// Handle pagination
const handlePageChange = (page) => {
  goToPage(page);
};

// Handle download
const handleDownload = async (slug, event) => {
  event.stopPropagation(); // Prevent navigation to detail page
  
  try {
    const response = await fetch(`/api/dana-desa/${slug}/download`);
    const data = await response.json();
    
    if (data.success) {
      // Create a temporary link element and trigger download
      const link = document.createElement('a');
      link.href = data.file_url;
      link.download = ''; // This will use the filename from the server
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    } else {
      console.error('Download failed:', data.message);
    }
  } catch (error) {
    console.error('Download error:', error);
  }
};

onMounted(async () => {
  await fetchLaporanDanaData();
  generateYearOptions();
});
</script>

<template>
  <Head>
    <title>{{ metaTitle }}</title>
    <meta name="description" :content="metaDescription" />
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" :content="metaType" />
    <meta property="og:title" :content="metaTitle" />
    <meta property="og:description" :content="metaDescription" />
    <meta property="og:url" :content="canonicalUrl" />
    <meta property="og:site_name" :content="metaSiteName" />
    <meta property="og:image" :content="metaImage" />
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="metaTitle" />
    <meta name="twitter:description" :content="metaDescription" />
    <meta name="twitter:image" :content="metaImage" />
    
    <!-- Canonical URL -->
    <link rel="canonical" :href="canonicalUrl" />
  </Head>

  <UserLayout v-slot="{layout}">
    <!-- Call function when template renders -->
    {{ updateMetaData(layout.name) }}
    
    <div class="bg-type1">
      <div class="max-w-7xl mx-auto mt-20 px-4 sm:px-8 xl:px-8 pt-12 lg:pt-20 font-merriweather">
        <div class="flex flex-col items-center justify-center sm:mb-10 xl:mb-12">
          <h1 class="text-lg sm:text-xl xl:text-2xl font-bold text-accent1">Laporan Dana Desa</h1>
        </div>
        <div class="flex flex-col xl:flex-row xl:items-center justify-between mt-4 mb-8">
          <h1 class="text-sm sm:text-base xl:text-xl text-accent2 flex">
            <span class="material-symbols-outlined mr-2 text-sm sm:text-base xl:text-xl align-middle">description</span>
            Laporan Dana Desa
          </h1>
          <!-- Filter Bar -->
          <div class="flex flex-wrap gap-2 text-sm mt-2 sm:mt-4 xl:mt-0 sm:justify-center xl:justify-end">
            <!-- Search Input -->
            <div class="relative">
              <input v-model="searchQuery" @keypress="handleKeyPress" @blur="handleSearchInput" type="text"
                placeholder="Cari laporan dana desa..."
                class="border-gray-300 rounded px-3 py-1 sm:py-2 xl:py-2 bg-type1 text-type2 text-xxs sm:text-xs focus:outline-none focus:ring-0 w-60 sm:w-96 xl:w-80" />
              <span @click="handleSearchInput"
                class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-300 text-xs sm:text-base cursor-pointer hover:text-gray-400">search</span>
            </div>
            <!-- Tahun -->
            <select v-model="selectedYear" @change="handleYearChange"
              class="border rounded px-3 py-1 sm:py-2 xl:py-2 bg-type1 text-type2 text-xxs sm:text-xs focus:outline-none focus:ring-0 w-20 sm:w-52 xl:w-40">
              <option value="">Tahun</option>
              <option v-for="year in yearOptions" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="laporanDanaApi.loading.value" class="flex justify-center items-center py-20 sm:py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-accent1"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="laporanDanaApi.error.value" class="text-center py-12">
          <p class="text-grey-500">{{ laporanDanaApi.error.value }}</p>
        </div>

        <!--Document Grid-->
        <div v-else>
          <div v-for="(laporan, idx) in laporanDanaApi.data.value" :key="laporan.slug"
            class="flex items-center bg-white rounded-lg shadow px-4 py-1 mb-3 overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
            
            <!-- Clickable content area -->
            <a :href="`/dana-desa/${laporan.slug}`"
              class="flex-1 flex flex-col mt-2 truncate cursor-pointer"
              style="text-decoration: none;">
              <!-- Nomor & Judul -->
              <div class="flex items-center">
                <span class="text-accent1 text-xs sm:text-sm flex-shrink-0">{{ idx + 1 }}.</span>
                <span
                  class="text-xs sm:text-sm ml-3 xl:ml-5 truncate block max-w-[300px] sm:max-w-[500px] md:max-w-[420px] lg:max-w-[600px]"
                  :title="laporan.title"
                >
                  {{ laporan.title }}
                </span>
              </div>
              <div class="flex items-center gap-2 ml-5 sm:ml-6 xl:ml-8 mt-1 mb-2">
                <!-- Metadata -->
                <div class="flex flex-wrap items-center text-xs text-filterBorder gap-x-4 gap-y-1">
                  <span class="inline-block" style="width: 28ch;">
                    <span class="material-symbols-outlined align-middle text-xxs sm:text-sm mr-1">calendar_today</span>
                    Diunggah: {{ formatDate(laporan.date) }}
                  </span>
                  <span class="inline-block" style="width: 20ch;">
                    <span class="material-symbols-outlined align-middle text-xxs sm:text-sm mr-1">visibility</span>
                    Dilihat {{ laporan.views || 0 }} kali
                  </span>
                  <span>
                    <span class="material-symbols-outlined align-middle text-xxs sm:text-sm mr-1">file_save</span>
                    Diunduh {{ laporan.downloads || 0 }} kali
                  </span>
                </div>
              </div>
            </a>
            
            <!--Tombol Download - Outside the link-->
            <div class="ml-4">
              <button v-if="laporan.file"
                @click="handleDownload(laporan.slug, $event)"
                class="inline-flex items-center justify-center w-8 h-8 rounded text-accent1 bg-secondary hover:bg-yellow-500"
                title="Download">
                <span class="material-symbols-outlined">download</span>
              </button>
              <div v-else
                class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-500 text-white cursor-not-allowed"
                title="File tidak tersedia">
                <span class="material-symbols-outlined">download</span>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="!laporanDanaApi.data.value || laporanDanaApi.data.value.length === 0"
            class="col-span-full text-center py-12">
            <p class="text-gray-500 text-lg">
              Belum ada laporan dana yang tersedia.
            </p>
          </div>
        </div>
        <!-- Pagination -->
        <Pagination v-if="laporanDanaApi.pagination.value && laporanDanaApi.pagination.value.last_page > 1"
          :current-page="laporanDanaApi.pagination.value.current_page"
          :last-page="laporanDanaApi.pagination.value.last_page" @page-changed="handlePageChange" />
      </div>
    </div>
  </UserLayout>
</template>