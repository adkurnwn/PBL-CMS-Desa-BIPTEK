<template>
  <UserLayout>
    <div class="min-h-screen bg-type1">
      <div class="max-w-3xl mx-auto py-20 px-4">
        <div v-if="loading" class="text-center py-8">Memuat detail produk...</div>
        <div v-else-if="error" class="text-center text-red-600 py-8">{{ error }}</div>
        <div v-else>
          <h1 class="text-2xl font-bold mb-4 text-accent1">{{ produk.nama }}</h1>
          <img
            v-if="produk.image && produk.image.path"
            :src="`/storage/${produk.image.path}`"
            :alt="produk.image.alt || produk.nama"
            class="mb-4 rounded-lg w-full max-h-96 object-cover"
          />
          <p class="mb-2"><strong>Kategori:</strong> {{ produk.name || '-' }}</p>
          <p class="mb-2"><strong>Deskripsi:</strong> {{ produk.description }}</p>
          <p class="mb-2"><strong>Harga:</strong> {{ produk.price ? `Rp${produk.price}` : '-' }}</p>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({ slug: String });

const produk = ref({});
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    const response = await axios.get(`/api/produk-desa/${props.slug}`);
    produk.value = response.data;
  } catch (e) {
    error.value = 'Gagal memuat detail produk';
  } finally {
    loading.value = false;
  }
});
</script>