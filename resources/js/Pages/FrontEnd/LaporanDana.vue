<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref } from 'vue';

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

function lihatDetail(id) {
  // Ganti dengan navigasi ke detail jika ada
  alert('Lihat detail laporan dengan ID: ' + id);
}
</script>

<template>
  <UserLayout>
    <div class="min-h-screen bg-type1">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 xl:px-8 py-40 font-merriweather">
        <div class="flex flex-col items-center justify-center mb-12">
          <h1 class="text-2xl font-bold text-accent1">Laporan Dana Desa</h1>
        </div>
        <div class="flex items-center justify-between mt-4 mb-8">
          <h1 class="text-xl text-accent2 flex items-center">
            <span class="material-symbols-outlined mr-2 text-[1.3em] align-middle">description</span>
            Laporan Dana Desa
          </h1>
           <!-- Filter Bar -->
          <div class="flex flex-wrap gap-2 text-sm">
            <!-- Nomor Peraturan -->
            <div class="relative">
              <input
                type="text"
                placeholder="Cari laporan dana desa..."
                class="border-gray-300 rounded px-3 py-2 bg-type1 text-gray-300 text-xs focus:outline-none focus:ring-0 w-80"
              />
              <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-300 text-base pointer-events-none">search</span>
            </div>
              <!-- Tahun Penetapan -->
              <select class="border rounded px-3 py-2 bg-type1 text-type2 text-xs focus:outline-none focus:ring-0 w-40">
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
                  <span class="text-accent1 mr-3 text-sm flex-shrink-0">{{ idx + 1 }}.</span>
                  <div class="text-sm font-medium text-accent1 truncate">
                    {{ laporan.judul }}
                  </div>
                </div>
                <!-- Metadata -->
                <div class="flex flex-wrap items-center text-xs text-filterBorder gap-x-4 gap-y-1 mb-1 ml-6">
                  <span>
                    <span class="material-symbols-outlined align-middle text-sm mr-1">calendar_today</span>
                    Tanggal: {{ laporan.tanggal }}
                  </span>
                  <span>
                    <span class="material-symbols-outlined align-middle text-sm mr-1">visibility</span>
                    Dilihat {{ laporan.views || 0 }} kali
                  </span>
                  <span>
                    <span class="material-symbols-outlined align-middle text-sm mr-1">file_save</span>
                    Diunduh {{ laporan.downloads || 0 }} kali
                  </span>
                </div>
              </div>
              <!-- Tombol Download -->
              <div class="ml-4">
                <a
                  v-if="laporan.file"
                  :href="`/storage/${laporan.file}`"
                  target="_blank"
                  class="inline-flex items-center justify-center w-8 h-8 rounded text-accent1 bg-secondary hover:bg-yellow-500"
                  title="Download"
                  @click.stop
                >
                  <span class="material-symbols-outlined">download</span>
                </a>
              </div>
            </div>
          <!-- Empty State -->
          <div v-if="laporanDana.length === 0" class="col-span-full text-center py-12">
            <p class="text-gray-500 text-lg">Belum ada laporan dana yang tersedia.</p>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>