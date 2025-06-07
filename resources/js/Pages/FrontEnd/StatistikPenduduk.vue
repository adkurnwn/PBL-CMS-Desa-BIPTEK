<script setup>
import PendudukLineChart from '@/Components/PendudukLineChart.vue';
import PendudukPieChart from '@/Components/PendudukPieChart.vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import { useApiCache } from "@/Utility/useApiCache";
import { ref, onMounted } from 'vue';

const demografi = ref({});

const fetchData = async () => {
    const demografiApi = useApiCache("demografiData", "/api/demografi/preview");
    await Promise.all([
        demografiApi.fetchWithCache(),
    ]);
    demografi.value = demografiApi.data.value || {};
};
onMounted(() => {
    fetchData();
});
</script>

<template>
  <UserLayout>
      <div class="bg-type1 max-w-screen-3xl mx-auto mt-24 px-6 sm:px-8 xl:px-8 py-10 sm:py-20 xl:py-20 font-merriweather">
        <div class="flex flex-col items-center justify-center sm:mb-6">
          <h1 class="text-lg sm:text-xl xl:text-2xl font-bold text-accent1">Informasi Demografi Desa</h1>
        </div>
        <div class="flex flex-col items-center justify-center sm:mb-8">
          <PendudukLineChart />
        </div>
        <div class="flex flex-col items-center justify-center sm:mb-6">
          <h1 class="text-lg sm:text-xl xl:text-2xl font-bold text-accent1">Komposisi Penduduk Berdasarkan Kategori Demografis Tahun {{ demografi.year }}</h1>
        </div>
        <div class="flex flex-wrap lg:grid lg:grid-cols-4 gap-5 mt-3">
          <!-- Grid 1 -->
          <div class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-full h-40 text-center">
              <svg class="flex ml-auto" xmlns="http://www.w3.org/2000/svg" height="48px"
                  viewBox="0 -960 960 960" width="48px" fill="#DEA521">
                  <path
                      d="M40-160v-160q0-34 23.5-57t56.5-23h131q20 0 38 10t29 27q29 39 71.5 61t90.5 22q49 0 91.5-22t70.5-61q13-17 30.5-27t36.5-10h131q34 0 57 23t23 57v160H640v-91q-35 25-75.5 38T480-200q-43 0-84-13.5T320-252v92H40Zm440-160q-38 0-72-17.5T351-386q-17-25-42.5-39.5T253-440q22-37 93-58.5T480-520q63 0 134 21.5t93 58.5q-29 0-55 14.5T609-386q-22 32-56 49t-73 17ZM160-440q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T280-560q0 50-34.5 85T160-440Zm640 0q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T920-560q0 50-34.5 85T800-440ZM480-560q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-680q0 50-34.5 85T480-560Z" />
              </svg>
              <p class="text-3xl font-thin text-left ml-3 mt-3">
                  {{ demografi.penduduk }}
              </p>
              <p class="text-lg text-left ml-3">Penduduk</p>
          </div>
          <!-- Grid 2 -->
          <div class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-full h-40 text-center">
              <svg class="flex ml-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"
                  width="48px" fill="#DEA521">
                  <path
                      d="M720.08-727q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM660-80v-336q0-30-15-54t-44-35l41-120q8-25 29.5-40t48.5-15q27 0 48.5 15t29.5 40l102 295H790v250H660ZM495-500q-25 0-42.5-17.5T435-560q0-25 17.5-42.5T495-620q25 0 42.5 17.5T555-560q0 25-17.5 42.5T495-500ZM220.08-727q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM150-80v-290H80v-250q0-24.75 17.63-42.38Q115.25-680 140-680h160q24.75 0 42.38 17.62Q360-644.75 360-620v250h-70v290H150Zm290 0v-170h-50v-166q0-18.33 12.83-31.17Q415.67-460 434-460h122q18.33 0 31.17 12.83Q600-434.33 600-416v166h-50v170H440Z" />
              </svg>
              <p class="text-3xl font-thin text-left ml-3 mt-3">
                  {{ demografi.kepala_keluarga }}
              </p>
              <p class="text-lg text-left ml-3">Kepala Keluarga</p>
          </div>
          <!-- Grid 3 -->
          <div class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-full h-40 text-center">
              <svg class="flex ml-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"
                  width="48px" fill="#DEA521">
                  <path
                      d="M800-800v220h-60v-117L556-514q21 29 32.5 63.5T600-380q0 92-64 156t-156 64q-92 0-156-64t-64-156q0-92 64-156t156-64q36 0 70 11.5t63 32.5l184-184H580v-60h220ZM380.2-540Q314-540 267-493.2t-47 113q0 66.2 46.8 113.2t113 47q66.2 0 113.2-46.8t47-113q0-66.2-46.8-113.2t-113-47Z" />
              </svg>
              <p class="text-3xl font-thin text-left ml-3 mt-3">
                  {{ demografi.penduduk_laki_laki }}
              </p>
              <p class="text-lg text-left ml-3">Penduduk Laki-Laki</p>
          </div>
          <!-- Grid 4 -->
          <div class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-full h-40 text-center">
              <svg class="flex ml-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"
                  width="48px" fill="#DEA521">
                  <path
                      d="M450-120v-80h-80v-60h80v-102q-82-11-136-73.71-54-62.7-54-145.29 0-91.42 64.5-155.21Q389-800 480-800t155.5 63.79Q700-672.42 700-581q0 82.59-54 145.29Q592-373 510-362v102h80v60h-80v80h-60Zm29.8-300q66.2 0 113.2-46.8t47-113q0-66.2-46.8-113.2t-113-47Q414-740 367-693.2t-47 113q0 66.2 46.8 113.2t113 47Z" />
              </svg>
              <p class="text-3xl font-thin text-left ml-3 mt-3">
                  {{ demografi.penduduk_perempuan }}
              </p>
              <p class="text-lg text-left ml-3">Penduduk Perempuan</p>
          </div>
        </div>
        <div class="flex flex-col items-center justify-center sm:mb-8">
          <PendudukPieChart />
        </div>
      </div>
      
  </UserLayout>
</template>