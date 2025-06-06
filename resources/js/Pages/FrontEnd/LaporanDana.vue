<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, computed } from 'vue';

const selectedDate = ref('');
const laporanDana = ref([
  {
    id: 1,
    judul: 'Realisasi APB Desa Tahun Anggaran 2024',
    tanggal: '14 Januari 2025',
    views: 12,
    downloads: 3,
    file: 'laporan-realisasi-2024.pdf',
  },
  {
    id: 2,
    judul: 'APB Desa Pao-Pao Tahun Anggaran 2024',
    tanggal: '14 Januari 2025',
    views: 8,
    downloads: 2,
    file: 'laporan-penggunaan-2023.pdf',
  },
  {
    id: 3,
    judul: 'Realisasi APB Desa Tahun Anggaran 2023',
    tanggal: '14 Januari 2024',
    views: 15,
    downloads: 5,
    file: '',
  },
]);
const pagination = ref({
  current_page: 1,
  last_page: 2,
});

const totalPages = computed(() => pagination.value.last_page);

function goToPage(page: number) {
  if (page < 1 || page > pagination.value.last_page) return;
  pagination.value.current_page = page;
}

function lihatDetail(id) {
  // Ganti dengan navigasi ke detail jika ada
  alert('Lihat detail laporan dengan ID: ' + id);
}
</script>

<template>
  <UserLayout>
    <div class="bg-type1">
      <div class="max-w-7xl mx-auto mt-20 px-6 sm:px-8 xl:px-8 py-10 sm:py-20 xl:py-20 font-merriweather">
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
            <!-- Nomor Peraturan -->
            <div class="relative">
              <input
                type="text"
                placeholder="Cari laporan dana desa..."
                class="border-gray-300 rounded px-3 py-1 sm:py-2 xl:py-2 bg-type1 text-gray-300 text-xxs sm:text-xs focus:outline-none focus:ring-0 w-60 sm:w-96 xl:w-80"
              />
              <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-300 text-xs sm:text-base pointer-events-none">search</span>
            </div>
              <!-- Tahun Penetapan -->
              <select class="border rounded px-3 py-1 sm:py-2 xl:py-2 bg-type1 text-type2 text-xxs sm:text-xs focus:outline-none focus:ring-0 w-20 sm:w-52 xl:w-40">
                <option>Tahun</option>
              </select>
          </div>
        </div>
        <!--Document Grid-->
        <div>
            <div 
              v-for="(laporan, idx) in laporanDana"
              :key="laporan.id"
              class="flex items-center bg-white rounded-lg shadow px-4 py-1 mb-3 overflow-hidden hover:shadow-xl transition-shadow duration-300 cursor-pointer group"
              @click="lihatDetail(laporan.id)"
              style="text-decoration: none;"
            >
              <!-- Nomor Urut & Judul -->
              <div class="flex-1 min-w-0 mt-1">
                <div class="flex items-center mb-1">
                  <span class="text-accent1 mr-3 text-xxs sm:text-sm flex-shrink-0">{{ idx + 1 }}.</span>
                  <div class="text-xxs sm:text-sm font-medium text-accent1 truncate">
                    {{ laporan.judul }}
                  </div>
                </div>
                <!-- Metadata -->
                <div class="flex sm:flex-wrap sm:items-center text-xxxs sm:text-xs text-filterBorder gap-x-2 sm:gap-x-4 gap-y-1 mb-1 ml-6">
                  <span>
                    <span class="material-symbols-outlined align-middle text-xxxs sm:text-xs">calendar_today</span>
                    Tanggal: {{ laporan.tanggal }}
                  </span>
                  <span>
                    <span class="material-symbols-outlined align-middle text-xxxs sm:text-xs">visibility</span>
                    Dilihat {{ laporan.views || 0 }} kali
                  </span>
                  <span>
                    <span class="material-symbols-outlined align-middle text-xxxs sm:text-xs">file_save</span>
                    Diunduh {{ laporan.downloads || 0 }} kali
                  </span>
                </div>
              </div>
              <!-- Tombol Download -->
              <div class="mt-1 sm:mt-0 ml-4">
                <a
                  v-if="laporan.file"
                  :href="`/storage/${laporan.file}`"
                  target="_blank"
                  class="inline-flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 rounded text-accent1 bg-secondary hover:bg-yellow-500"
                  title="Download"
                  @click.stop
                >
                  <span class="material-symbols-outlined">download</span>
                </a>
              </div>
            </div>
          <!-- Empty State -->
          <div v-if="laporanDana.length === 0" class="col-span-full text-center py-12">
            <p class="text-gray-500 text-sm sm:text-base xl:text-lg">Belum ada laporan dana yang tersedia.</p>
          </div>
        </div>
        <!-- Pagination -->
        <div
          v-if="pagination && pagination.last_page > 1"
          class="flex justify-center mt-5 gap-2"
        >
          <button
            @click="goToPage(pagination.current_page - 1)"
            class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center bg-accent1 text-type1 text-xs"
            :disabled="pagination.current_page === 1"
          >
            &lt;
          </button>
          <span
            v-for="p in totalPages"
            :key="p"
            class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs cursor-pointer"
            :class="pagination.current_page === p ? 'bg-accent1 text-type1' : 'border text-accent1 bg-type1'"
            @click="goToPage(p)"
          >{{ p }}</span>
          <button
            @click="goToPage(pagination.current_page + 1)"
            class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center bg-accent1 text-type1 text-xs"
            :disabled="pagination.current_page === pagination.last_page"
          >
            &gt;
          </button>
        </div>
      </div>
    </div>
  </UserLayout>
</template>