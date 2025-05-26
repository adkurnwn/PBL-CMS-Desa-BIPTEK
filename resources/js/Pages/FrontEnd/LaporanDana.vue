<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';

</script>

<template>
  <UserLayout>
    <div class="min-h-screen bg-type1">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-40 font-merriweather">
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
              <select class="border rounded px-3 py-2 bg-type1 text-gray-500 text-xs focus:outline-none focus:ring-0 w-40">
                <option>Tahun</option>
              </select>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>