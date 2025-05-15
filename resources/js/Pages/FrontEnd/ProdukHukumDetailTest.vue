<template>
  <UserLayout>
    <div class="min-h-screen bg-type1">
      <div class="max-w-screen-2xl mx-auto px-28 py-12">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <p class="text-red-600">{{ error }}</p>
        </div>

        <!-- Document Detail -->
        <div v-else class="mt-24 font-merriweather">
          <!-- Document Header -->
          <div class="flex space-x-2 items-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor" class="text-accent2">
              <path d="M280-280q17 0 28.5-11.5T320-320q0-17-11.5-28.5T280-360q-17 0-28.5 11.5T240-320q0 17 11.5 28.5T280-280Zm-40-160h80v-240h-80v240Zm200 160h280v-80H440v80Zm0-160h280v-80H440v80Zm0-160h280v-80H440v80ZM160-120q-33 0-56.5-23.5T80-200v-560q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v560q0 33-23.5 56.5T800-120H160Zm0-80h640v-560H160v560Zm0 0v-560 560Z"/>
            </svg>
            <h1 class="text-lg font- text-accent2 -mb-0.5 font-merriweather">
              Peraturan Desa
            </h1>
          </div>

          <!-- Title -->
          <h1 class="flex text-3xl w-full mx-auto justify-center items-center font-bold text-accent1 mb-4">
            Detail Produk Hukum
          </h1>

          <!-- Document Details -->
          <div class="flex flex-col gap-4 mb-8 font-merriweather font-medium text-accent1">
            <div class="flex flex-row items-center">
              <div class="flex flex-col w-1/5 font-bold">Kategori</div>
              <div class="flex flex-row w-4/5">
                <div class="flex flex-col mr-1">:</div>
                <p class="flex col-span-2">{{ dokumen.category?.name }}</p>
              </div>
            </div>
            <div class="flex flex-row items-start">
              <div class="flex flex-col w-1/5 font-bold">Judul</div>
              <div class="flex flex-row w-4/5">
                <div class="flex flex-col mr-1">:</div>
                <p class="flex col-span-2">{{ dokumen.title }}</p>
              </div>
            </div>
            <div class="flex flex-row items-start">
              <div class="flex flex-col w-1/5 font-bold">Tanggal</div>
              <div class="flex flex-row w-4/5">
                <div class="flex flex-col mr-1">:</div>
                <p class="flex col-span-2">{{ formatDate(dokumen.date) }}</p>
              </div>
            </div>
            <div class="flex flex-row items-start">
              <div class="flex flex-col w-1/5 font-bold">Deskripsi</div>
              <div class="flex flex-row w-4/5">
                <div class="flex flex-col mr-1">:</div>
                <p class="flex col-span-2">{{ dokumen.description }}</p>
              </div>
            </div>
            <div class="flex flex-row items-start">
              <div class="flex flex-col w-1/5 font-bold">Link</div>
              <div class="flex flex-row w-4/5">
                <div class="flex flex-col mr-1">:</div>
                <a v-if="dokumen.link" :href="dokumen.link" target="_blank" class="text-blue-600 hover:underline">
                  {{ dokumen.link }}
                </a>
                <p v-else class="flex col-span-2">Tidak Tersedia</p>
              </div>
            </div>
          </div>

          <!-- Document Actions -->
          <div class="flex space-x-4">
            <a v-if="dokumen.file"
               :href="`/storage/${dokumen.file}`"
               target="_blank"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
            >
              Download Document
            </a>
            <a v-if="dokumen.link"
               :href="dokumen.link"
               target="_blank"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              View External Link
            </a>
          </div>
        </div>

        <!-- Comments Section -->
        <Comment 
          type="produk-hukum"
          :identifier="id"
          :content="dokumen"
        />

      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import Comment from '@/Components/Comment.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  id: {
    type: String,
    required: true
  }
});

const dokumen = ref({});
const loading = ref(true);
const error = ref(null);

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const fetchDokumen = async () => {
  try {
    loading.value = true;
    const response = await axios.get(`/api/produk-hukum/${props.id}`);
    dokumen.value = response.data;
  } catch (err) {
    error.value = 'Gagal memuat dokumen';
    console.error('Error fetching document:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchDokumen();
});
</script>