<template>
  <UserLayout>
    <div class="min-h-screen bg-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col items-center justify-center mb-12">
          <h1 class="text-4xl font-bold text-blue-600">Informasi & Berita</h1>
          <p class="mt-2 text-gray-600">Berita dan informasi terkini seputar desa</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <p class="text-red-600">{{ error }}</p>
        </div>

        <!-- News Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <Link
            v-for="item in news"
            :key="item.id"
            :href="`/berita-test/${item.slug}`"
            class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300"
          >
            <!-- Image -->
            <div v-if="item.images?.length > 0" class="h-48 overflow-hidden">
              <img
                :src="`/storage/${item.images[0].path}`"
                :alt="item.images[0].alt"
                class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300"
              >
            </div>

            <div class="p-6">
              <!-- Category Badge -->
              <span v-if="item.category"
                    class="inline-block px-3 py-1 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full mb-4">
                {{ item.category.name }}
              </span>

              <!-- Title -->
              <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
                {{ item.title }}
              </h2>

              <!-- Content Preview -->
              <p class="text-gray-600 mb-4 line-clamp-3">
                {{ item.content }}
              </p>

              <!-- Metadata -->
              <div class="flex items-center justify-between text-sm text-gray-500">
                <div class="flex items-center space-x-2">
                  <span>{{ formatDate(item.created_at) }}</span>
                  <span>•</span>
                  <span>{{ item.user?.name || 'Admin' }}</span>
                </div>
                <span>{{ item.views }} views</span>
              </div>
            </div>
          </Link>

          <!-- Empty State -->
          <div v-if="news.length === 0" class="col-span-full text-center py-12">
            <p class="text-gray-500 text-lg">Belum ada berita yang tersedia.</p>
          </div>
        </div>

        <!-- Pagination (if needed) -->
        <div v-if="pagination && pagination.last_page > 1" class="mt-8 flex justify-center">
          <!-- Add pagination controls here -->
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const news = ref([]);
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

const fetchNews = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/api/news');
    news.value = response.data.data;
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total
    };
  } catch (err) {
    error.value = 'Gagal memuat berita';
    console.error('Error fetching news:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchNews();
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
