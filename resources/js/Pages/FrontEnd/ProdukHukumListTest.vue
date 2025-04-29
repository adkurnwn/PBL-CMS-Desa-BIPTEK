<template>
  <UserLayout>
    <div class="min-h-screen bg-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col items-center justify-center mb-12">
          <h1 class="text-4xl font-bold text-blue-600">Produk Hukum</h1>
          <p class="mt-2 text-gray-600">Dokumen hukum dan peraturan desa</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <p class="text-red-600">{{ error }}</p>
        </div>

        <!-- Documents Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div 
            v-for="dokumen in produkHukums" 
            :key="dokumen.id"
            class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300"
          >
            <div class="p-6">
              <!-- Category Badge -->
              <span v-if="dokumen.category" 
                    class="inline-block px-3 py-1 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full mb-4">
                {{ dokumen.category.name }}
              </span>

              <!-- Title -->
              <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
                {{ dokumen.title }}
              </h2>

              <!-- Description -->
              <p class="text-gray-600 mb-4 line-clamp-3">
                {{ dokumen.description }}
              </p>

              <!-- Metadata and Actions -->
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">
                  {{ formatDate(dokumen.date) }}
                </span>
                <div class="flex space-x-2">
                  <a v-if="dokumen.file"
                     :href="`/storage/${dokumen.file}`"
                     target="_blank"
                     class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                  >
                    Download
                  </a>
                  <Link 
                    :href="`/produk-hukum-test/${dokumen.id}`"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md text-blue-600 bg-blue-50 hover:bg-blue-100"
                  >
                    Detail
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="produkHukums.length === 0" class="col-span-full text-center py-12">
            <p class="text-gray-500 text-lg">Belum ada dokumen yang tersedia.</p>
          </div>
        </div>

        <!-- Pagination (if available from API) -->
        <div v-if="pagination && pagination.last_page > 1" class="mt-8 flex justify-center">
          <!-- Add pagination controls here -->
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const produkHukums = ref([]);
const loading = ref(true);
const error = ref(null);
const pagination = ref(null);

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const fetchProdukHukums = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/api/produk-hukum');
    produkHukums.value = response.data.data;
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total
    };
  } catch (err) {
    error.value = 'Gagal memuat dokumen';
    console.error('Error fetching documents:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchProdukHukums();
});
</script>

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