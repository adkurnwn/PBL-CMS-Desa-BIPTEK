<script setup lang="ts">
import { ref } from 'vue'
import UserLayout from '@/Layouts/UserLayout.vue';

// State untuk input search
const searchQuery = ref('')

// Fungsi pencarian
function handleSearch() {
  // logika pencarian di sini
  alert('Cari: ' + searchQuery.value)
}

// Fungsi filter
function openFilter() {
  // logika buka filter di sini
  alert('Filter dibuka')
}
</script>

<template>
    <UserLayout>
        <!--Menu Bar-->
        <main class="flex flex-col items-center justify-center min-h-screen bg-type1 font-merriweather">
            <article class="flex flex-col items-center justify-center min-h-screen bg-type1 font-merriweather py-60 -mt-20">
                <!-- Judul -->
                <h1 class="xl:text-2xl sm:text-xl text-md font-semibold text-accent2">Galeri Desa</h1> 
                <!-- Search Bar -->
                <div class="relative flex flex-grow items-center max-w-7xl mx-auto">  
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Cari Kegiatan atau Album..."
                    style="width: 800px;"
                    class="items-center max-w-xl mr-5 border-2 border-accent1 bg-type1 shadow-lg rounded-full italic font-ligh" 
                    />
                    <!-- Button Ikon Search -->
                    <button
                    class="absolute right-8 transform text-accent1"
                    @click="handleSearch">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#244031"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
                    </button>
                </div>
                <!-- Kategori Galeri -->
                <div class="flex relative items-center space-x-7 overflow-x-auto text-lg text-accent1">
                    <button class="hover:underline font-medium">Semua</button>
                    <button class="hover:underline">Populer</button>
                    <button class="hover:underline">Wisata</button>
                    <button class="hover:underline">Acara</button>
                    <button class="hover:underline">UMKM</button>
                    <button class="hover:underline">Pertanian</button>
                    <button class="hover:underline">Kesehatan</button>
                    <button class="hover:underline">Pendidikan</button>
                    <button class="ml-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" 
                    height="24px" 
                    viewBox="0 -960 960 960" 
                    width="24px" 
                    fill="#244031">
                    <path d="M80-160v-160h160v160H80Zm240 0v-160h560v160H320ZM80-400v-160h160v160H80Zm240 0v-160h560v160H320ZM80-640v-160h160v160H80Zm240 0v-160h560v160H320Z"/></svg>
                    </button>
                </div>
            </article> 
            <article  class="relative flex-grow items-center self-start ml-20 -mt-56">
                <!--Berita Terkini-->
                <h1 class="text-xl font-bold text-gray-800">
                    Album Desa
                </h1>
            </article> 
        </main>
    </UserLayout>
</template>