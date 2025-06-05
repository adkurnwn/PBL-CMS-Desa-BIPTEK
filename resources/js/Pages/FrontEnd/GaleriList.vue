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

// Data dummy album
const albums = ref([
  {
    id: 1,
    title: 'MOU Pao-Pao x ICMI',
    images: [
      'https://via.placeholder.com/150x90?text=Main',
      'https://via.placeholder.com/80x40?text=1',
      'https://via.placeholder.com/80x40?text=2'
    ],
    date: '12 April 2025'
  },
    { id: 2, title: 'Title Here', images: [
      'https://via.placeholder.com/150x90?text=Main',
      'https://via.placeholder.com/80x40?text=1',
      'https://via.placeholder.com/80x40?text=2'
    ], date: 'dd mm yyyy'
  },
  { id: 3, title: 'Title Here', images: [
      'https://via.placeholder.com/150x90?text=Main',
      'https://via.placeholder.com/80x40?text=1',
      'https://via.placeholder.com/80x40?text=2'
    ], date: 'dd mm yyyy'
  },
  { id: 4, title: 'Title Here', images: [
      'https://via.placeholder.com/150x90?text=Main',
      'https://via.placeholder.com/80x40?text=1',
      'https://via.placeholder.com/80x40?text=2'
    ], date: 'dd mm yyyy'
  },
  { id: 5, title: 'Title Here', images: [
      'https://via.placeholder.com/150x90?text=Main',
      'https://via.placeholder.com/80x40?text=1',
      'https://via.placeholder.com/80x40?text=2'
    ], date: 'dd mm yyyy'
  },
  { id: 6, title: 'Title Here', images: [
      'https://via.placeholder.com/150x90?text=Main',
      'https://via.placeholder.com/80x40?text=1',
      'https://via.placeholder.com/80x40?text=2'
    ], date: 'dd mm yyyy'
  },
]);

const pageAlbumDesa = ref(1);
const totalPages = 2;
const pageGaleriTerkini = ref(1);
const totalPagesGaleriTerkini = 2;
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
            <article class="w-full mt-5 px-20 mb-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                    v-for="album in albums"
                    :key="album.id"
                    class="bg-white rounded-lg shadow p-3 flex flex-col"
                    >
                    <!-- Judul dan ikon -->
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-accent1 text-lg line-clamp-2">{{ album.title }}</span>
                        <span class="material-symbols-outlined text-accent1 text-2xl">gallery_thumbnail</span>
                    </div>
                    <!-- Gambar utama dan 2 gambar kecil -->
                    <div class="flex gap-2 mb-2">
                        <img
                        :src="album.images[0]"
                        alt="main"
                        class="rounded object-cover w-1/2 h-24 text-gray-500"
                        />
                        <div class="flex flex-col gap-2 w-1/2 text-gray-500">
                        <img
                            v-for="(img, i) in album.images.slice(1, 3)"
                            :key="i"
                            :src="img"
                            alt="thumb"
                            class="rounded object-cover h-11 w-full"
                        />
                        </div>
                    </div>
                    <!-- Tanggal -->
                    <div class="text-xs text-right text-gray-500 mt-auto">
                        {{ album.date }}
                    </div>
                </div>
            </div>
            <!-- Pagination -->
            <div class="flex justify-center mt-5 gap-2">
                <button
                    class="w-8 h-8 rounded-full flex items-center justify-center bg-accent1 text-type1 text-xs"
                    :disabled="pageAlbumDesa === 1"
                    @click="pageAlbumDesa--"
                >&lt;</button>
                <button
                    v-for="p in totalPages"
                    :key="p"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs"
                    :class="pageAlbumDesa === p ? 'bg-accent1 text-type1' : 'border text-accent1 bg-type1'"
                    @click="pageAlbumDesa = p"
                >{{ p }}</button>
                <button
                    class="w-8 h-8 rounded-full flex items-center justify-center bg-accent1 text-type1 text-xs"
                    :disabled="pageAlbumDesa === totalPages"
                    @click="pageAlbumDesa++"
                >&gt;</button>
            </div>
            </article>
            <!--Galeri Terkini-->
            <article class="w-full px-20 flex flex-col pb-8 lg:pb-24 bg-type1 font-merriweather space-y-5">
              <h1 class="text-xl font-bold text-gray-800">
                Galeri Terkini
              </h1>
                <div class="flex flex-row w-full h-full justify-between space-x-5">
                    <img
                        src="../../../images/GALERI_DESA2.png"
                        alt="Gambar 1"
                        class="w-3/5 h-[150px] sm:h-[225px] md:h-[300px] rounded-2xl object-cover"
                    />
                    <img
                        src="../../../images/GALERI_DESA2.png"
                        alt="Gambar 2"
                        class="w-2/5 h-[150px] sm:h-[225px] md:h-[300px] rounded-2xl object-cover"
                    />
                </div>
                <div class="flex flex-col sm:flex-row w-full justify-between space-x-5">
                    <img
                        src="../../../images/GALERI_DESA2.png"
                        alt="Gambar 3"
                        class="w-full sm:w-1/3 h-[200px] lg:w-96 rounded-xl object-cover"
                    />
                    <img
                        src="../../../images/GALERI_DESA2.png"
                        alt="Gambar 4"
                        class="w-full sm:w-1/3 h-[200px] lg:w-96 rounded-xl object-cover"
                    />
                    <img
                        src="../../../images/GALERI_DESA2.png"
                        alt="Gambar 5"
                        class="w-full sm:w-1/3 h-[200px] lg:w-96 rounded-xl object-cover"
                    />
                </div>
                <!-- Pagination -->
                <div class="flex justify-center mt-5 gap-2">
                    <button
                        class="w-8 h-8 rounded-full flex items-center justify-center bg-accent1 text-type1 text-xs"
                        :disabled="pageGaleriTerkini === 1"
                        @click="pageGaleriTerkini--"
                    >&lt;</button>
                    <button
                        v-for="p in totalPagesGaleriTerkini"
                        :key="p"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-xs"
                        :class="pageGaleriTerkini === p ? 'bg-accent1 text-type1' : 'border text-accent1 bg-type1'"
                        @click="pageGaleriTerkini = p"
                    >{{ p }}</button>
                    <button
                        class="w-8 h-8 rounded-full flex items-center justify-center bg-accent1 text-type1 text-xs"
                        :disabled="pageGaleriTerkini === totalPagesGaleriTerkini"
                        @click="pageGaleriTerkini++"
                    >&gt;</button>
                </div>
            </article>
        </main>
    </UserLayout>
</template>