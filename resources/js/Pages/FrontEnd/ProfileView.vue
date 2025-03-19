<!-- Profileview.vue -->
<template>
  <UserLayout>
    <!-- Sejarah Desa -->
    <div class="flex flex-col justify-center h-screen font-merriweather space-y-1 bg-green-100">
      <img src="../../../images/profile.jpg" alt="Logo" class="absolute z-0 w-screen h-screen" />
      <div class="absolute bg-black z-0 w-full h-screen opacity-60"></div>
      <h1 class="text-type1 text-3xl md:text-4xl font-bold text-center md:text-left md:ml-[80px] pt-[130px] z-10">Sejarah Desa</h1>
      <p class="text-type1 font-thin text-lg md:text-2xl text-center md:text-justify max-w-2xl md:max-w-5xl ml-[35px] md:ml-[80px] pt-5 z-10">Desa Pao-Pao berasal dari nama sebuah Gunung yang berada di Dusun Aroppoe Desa Tellumpanua yang merupakan bekas wilayah Desa Pao-Pao yakni Gunung Pao-Pao. Sejak Kelahirannya Desa Pao-Pao terdiri dari 7 Dusun yakni Dusun Lompengeng, Maralleng, Pucue, Bonto Penno, Polejiwa Aroppoe, Maddo, namun pada tahun 1988 Desa Pao-Pao dimekarkan dan lahirlah Desa Tellumpanua dengan 3 wilayah yakni Dusun Aroppoe, Polejiwa dan Maddo, sehingga sampai sekarang Wilayah Desa Pao-Pao terdiri dari 4 Dusun yakni Dusun Lompengeng, Maralleng, Pucue dan Bonto Penno.</p>
    </div>
    <!-- Visi & Misi -->
    <div class="flex flex-col justify-center w-screen h-screen font-merriweather space-y-1 bg-type1">
      <img src="../../../images/FOTO_KADES.png" alt="Logo" class="absolute z-0 max-w-xs md:max-w-md -mt-60 md:mt-16 ml-[220px] md:ml-28" />
      <div class="absolute right-0 mr-24 text-semiBlack">
        <h1 class="font-bold text-accent1 text-2xl md:text-4xl max-w-30 md:max-w-30 mt-[530px] md:mt-12 text-center">Visi</h1>
        <p class="text-md md:text-2xl text-semiBlack max-w-lg md:max-w-3xl text-center mt-1 md:mt-3 italic">“Desa Pao-Pao yang Maju, Mandiri, dan Bernafaskan Keagamaan”</p>
        <h1 class="font-bold text-accent1 text-2xl md:text-4xl max-w-30 md:max-w-30 mt-6 md:mt-14 text-center">Misi</h1>
        <div class="text-justify mt-1 md:mt-3">
          <ol class="list-decimal text-semiBlack pl-5 text-md md:text-2xl max-w-lg md:max-w-3xl space-y-3 md:space-y-6 italic">
            <li>Mewujudkan tata kelola pemerintahan desa yang baik, bersih (good and clean governance) serta layanan publik yang prima berbasis teknologi informasi.</li>
            <li>Meningkatkan keterpenuhan sarana dan prasarana pengembangan wilayah, ekonomi, sosial dan lingkungan hidup di desa serta keterpenuhan hak-hak dasar masyarakat.</li>
            <li>Meningkatkan pemanfaatan potensi ekonomi untuk kemandirian masyarakat.</li>
          </ol>
        </div>
      </div>
    </div>
    <!-- Struktur Pemerintahan -->
    <div class="flex flex-col w-screen h-screen font-merriweather bg-type1">
      <h1 class="font-bold text-accent1 text-3xl md:text-4xl max-w-30 ml-[40px] md:ml-[80px] m-[130px] md:m-20">Struktur Pemerintahan</h1>
      <p class="text-md md:text-2xl text-semiBlack max-w-2xl md:max-w-7xl ml-10 md:ml-20 -mt-[120px] md:-mt-[65px] italic">Susunan organisasi yang bertanggung jawab dalam mengelola administrasi dan pelayanan publik di desa.</p>
      <div class="flex justify-center grid-cols-2 gap-4 md:gap-9 p-[350px] md:p-6 md:h-[424px] w-[1370px] -ml-[310px] md:ml-12 bg-transparant -mt-[340px] md:-mt-[0px]">
         <!-- Card 1 -->  
         <div class="bg-white rounded-xl shadow-lg text-center">
          <img src="../../../images/SO_PEMERINTAHAN_DESA.png" alt="Gambar 1" class="w-full rounded-lg">
          <p class="mt-1 md:mt-3 text-accent1 text-xs md:text-xl font-semibold">Struktur Organisasi Pemerintahan Desa</p>
        </div>
        <!-- Card 2 -->
        <div class="bg-white rounded-xl shadow-lg text-center">
          <img src="../../../images/SO_BPD.png" alt="Gambar 2" class="w-full rounded-lg">
          <p class="mt-1 md:mt-3 text-accent1 text-xs md:text-xl font-semibold">Struktur Organisasi Badan Permusyawaratan Desa</p>
        </div>
      </div>
    </div>
    <!-- Potensi & Keunggulan -->
    <div class="flex flex-col w-screen h-screen font-merriweather bg-type1 -mt-80 md:-mt-20">
      <h1 class="font-bold text-accent1 text-3xl md:text-4xl max-w-30 ml-[40px] md:ml-[80px] m-20 mt-[90px] md:mt-[79px]">Potensi & Keunggulan Desa</h1>
      <p class="text-md md:text-2xl text-semiBlack max-w-2xl md:max-w-7xl ml-10 md:ml-20 -mt-[69px] md:-mt-[65px] italic">Potensi dan aset penting dalam mendorong pertumbuhan desa dan kesejahteraan masyarakat.</p>
      <div class="flex justify-center gap-2 md:gap-7 p-6 bg-transparant h-[318px] w-[1366px] -ml-[307px] md:ml-12 -mt-3 md:-mt-1">
        <!-- Card 1 -->
        <div class="rounded-xl">
          <img src="../../../images/POTENSI_1.png" alt="Gambar 1" class="w-[220px] md:w-full">
        </div>
        <!-- Card 2 -->
        <div class="rounded-xl">
          <img src="../../../images/POTENSI_2.png" alt="Gambar 2" class="w-[220px] md:w-full">
        </div>
        <!-- Card 3 -->
        <div class="rounded-xl">
          <img src="../../../images/POTENSI_3.png" alt="Gambar 2" class="w-[220px] md:w-full">
        </div>
      </div>
      <p class="flex items-center ml-[592px] md:ml-[1240px] text-secondary text-md md:text-xl -mt-[140px] md:-mt-[2px]">Selengkapnya
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#DEA521">
          <path d="M411-481 213-679l42-42 240 240-240 240-42-42 198-198Zm253 0L466-679l42-42 240 240-240 240-42-42 198-198Z"/>
        </svg>
      </p>
    </div>
    <!-- Infrastruktur Desa -->
    <div class="flex flex-col w-screen h-screen font-merriweather bg-type1 -mt-[438px] md:-mt-[200px]">
      <h1 class="font-bold text-accent1 text-3xl md:text-4xl max-w-30 md:max-w-30 ml-[40px] md:ml-[80px] m-[130px] md:m-20">Infrastruktur Desa</h1>
      <p class="text-md md:text-2xl text-semiBlack max-w-2xl md:max-w-7xl ml-10 md:ml-20 -mt-[118px] md:-mt-16 italic">Informasi tentang sarana dan prasarana yang tersedia di desa yang mendukung kehidupan masyarakat.</p>
      <div class="grid grid-cols-2 gap-1 md:gap-3 p-[340px] bg-transparant md:w-[1366px] -ml-[300px] md:-ml-[266px] -mt-[327px] md:-mt-[319px]">
        <!-- Card 1 -->
        <div class="rounded-xl">
          <img src="../../../images/JEMBATAN.png" alt="Gambar 1" class="w-[200px] md:w-full rounded-lg">
        </div>
        <!-- Card 2 -->
        <div class="rounded-xl">
          <img src="../../../images/GEDUNG_SERBA_GUNA.png" alt="Gambar 2" class="w-[200px] md:w-full rounded-lg">
        </div>
        <!-- Card 3 -->
        <div class="rounded-xl">
          <img src="../../../images/PASAR.png" alt="Gambar 2" class="w-[200px] md:w-full rounded-lg">
        </div>
        <!-- Card 4 -->
          <div class="rounded-xl">
          <img src="../../../images/BALAI_DESA.png" alt="Gambar 2" class="w-[200px] md:w-full rounded-lg">
        </div>
      </div>
      <div class="ml-[430px] md:ml-[808px] text-accent2 -mt-[596px] md:-mt-[806px]">
        <ol class="list-disc font-semibold pl-4 text-sm md:text-2xl max-w-lg md:max-w-3xl space-y-1">
            <li>Puskesmas</li>
            <li>Balai Desa</li>
            <li>Gedung Serba Guna</li>
            <li>Pasar Desa</li>
            <li>Masjid</li>
            <li>Sekolah Dasar</li>
            <li>Sekolah Menengah Pertama</li>
            <li>Sekolah Menengah Atas</li>
            <li>Sekolah Menengah Kejuruan</li>
            <li>Jembatan Penghubung</li>
          </ol>
      </div>
    </div>       
    <!-- Data Geografi -->
    <div class="flex flex-col w-screen h-screen font-merriweather bg-type1 -mt-[285px] md:-mt-[15px]">
      <h1 class="font-bold text-accent1 text-3xl md:text-4xl max-w-30 md:max-w-30 ml-[40px] md:ml-[80px] m-[130px] md:m-20">Data Geografi</h1>
      <div class="flex flex-col space-y-2 w-96 ml-[42px] md:ml-20 -mt-[116px] md:-mt-16 text-lg md:text-xl">
      <div class="flex w-full">
        <p class="w-1/3 font-bold text-accent1">Koordinat</p>
        <p class="w-1/2 text-accent2 -ml-4 md:-ml-1">: -4.498, 119.600</p>
      </div>
      <div class="flex w-full">
        <p class="w-1/3 font-bold text-accent1">Luas Desa</p>
        <p class="w-1/2 text-accent2 -ml-4 md:-ml-1">: 9.300.000 m²</p>
      </div>
      </div>
      <div class="h-96">
        <iframe 
          class="w-[450px] md:w-[700px] h-[280px] md:h-full ml-10 md:ml-20 rounded-lg mt-5"
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15910.138432608619!2d119.61024555!3d-4.4971499999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbe258c1d785e89%3A0xcb84698ceea645b7!2sPao-Pao%2C%20Kec.%20Tanete%20Rilau%2C%20Kabupaten%20Barru%2C%20Sulawesi%20Selatan!5e0!3m2!1sid!2sid!4v1741847539077!5m2!1sid!2sid"  
          style="border:0;" 
          allowfullscreen="" 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
      <div class="ml-[500px] md:ml-[800px] text-accent1 text-md md:text-xl -mt-[365px] md:-mt-[367px]">
        <h1 class="font-bold">Batas Desa</h1>
        <p class="font-bold mt-2 md:mt-3">Utara</p> <span class="text-sm md:text-lg">Kelurahan Lalolang & Desa Tellumpanua</span>
        <p class="font-bold mt-2 md:mt-3">Barat</p> <span class="text-sm md:text-lg">Selat Makassar</span>
        <p class="font-bold mt-2 md:mt-3">Timur</p> <span class="text-sm md:text-lg">Kecamatan Ternate Riaja</span>
        <p class="font-bold mt-2 md:mt-3">Selatan</p> <span class="text-sm md:text-lg">Desa Corawali</span>
      </div>
      <div class="mt-[13px] md:mt-[100px] ml-10 md:ml-20 text-accent1 text-xs md:text-xl">
        <p class="font-bold">GJ22+QCF, Pao-Pao, Kec. Tanete Rilau, Kabupaten Barru, Sulawesi Selatan 90761</p>
      </div>
    </div>
    <!-- Data Demografi -->
    <div class="flex flex-col w-screen h-screen font-merriweather bg-type1 -mt-[220px] md:-mt-[40px]">
      <h1 class="font-bold text-accent1 text-3xl md:text-4xl max-w-30 md:max-w-30 ml-[40px] md:ml-[80px] m-[130px] md:m-20">Data Demografi</h1>
      <p class="text-md md:text-2xl text-accent1 max-w-2xl md:max-w-7xl ml-10 md:ml-20 -mt-[115px] md:-mt-16 italic">Menyajikan informasi mengenai aspek kependudukan yang memberikan gambaran mengenai kondisi sosial ekonomi masyarakat desa.</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-[260px] -ml-4 md:ml-6 mt-5">
          <!-- Card 1 -->
          <div class="bg-accent2 text-type1 rounded-xl shadow-md p-12 w-[310px] ml-14 -mt-1 flex flex-col">
            <svg class="ml-[175px] md:ml-[170px] -mt-7 md:w-20" xmlns="http://www.w3.org/2000/svg" 
            height="60px" 
            viewBox="0 -960 960 960" 
            width="60px" 
            fill="#DEA521">
            <path d="M40-160v-160q0-30.67 21.5-52t51.83-21.33H251q18 0 34.33 9 16.34 9 27.34 24.66 29 41 73.16 63.67Q430-273.33 480-273.33q50.33 0 94.5-22.67t73.5-63.67q11.67-15.66 27.5-24.66t33.5-9h137.67q30.66 0 52 21.33Q920-350.67 920-320v160H653.33v-109.67q-35.66 30.34-80.16 46.67-44.5 16.33-93.17 16.33-48.33 0-92.67-16.5Q343-239.67 306.67-270v110H40Zm440-166.67q-36 0-69-16.83T357-390q-16.33-23.67-39.83-37.83-23.5-14.17-50.84-17.5Q293-477.67 358-495.5t122-17.83q57 0 122.33 17.83 65.34 17.83 92 50.17-27 3.33-50.66 17.5-23.67 14.16-40 37.83Q583-360 550-343.33q-33 16.66-70 16.66ZM160-453.33q-46.67 0-80-33.34-33.33-33.33-33.33-80 0-47.66 33.33-80.5Q113.33-680 160-680q47.67 0 80.5 32.83 32.83 32.84 32.83 80.5 0 46.67-32.83 80-32.83 33.34-80.5 33.34Zm640 0q-46.67 0-80-33.34-33.33-33.33-33.33-80 0-47.66 33.33-80.5Q753.33-680 800-680q47.67 0 80.5 32.83 32.83 32.84 32.83 80.5 0 46.67-32.83 80-32.83 33.34-80.5 33.34Zm-320-120q-46.67 0-80-33.34-33.33-33.33-33.33-80 0-47.66 33.33-80.5Q433.33-800 480-800q47.67 0 80.5 32.83 32.83 32.84 32.83 80.5 0 46.67-32.83 80-32.83 33.34-80.5 33.34Z"/></svg>
            <p class="text-3xl font-thin">2.114</p>
            <p class="text-lg font-thin">Penduduk</p>
          </div>
          <!-- Card 2 -->
          <div class="bg-accent2 text-type1 rounded-xl shadow-md p-12 w-[310px] -ml-[15px] md:-ml-[50px] -mt-1 flex flex-col">
            <svg class="ml-[180px] -mt-7" xmlns="http://www.w3.org/2000/svg" 
            height="60px" 
            viewBox="0 -960 960 960" 
            width="60px" 
            fill="#DEA521">
            <path d="M720.08-727q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM660-80v-336q0-30-15-54t-44-35l41-120q8-25 29.5-40t48.5-15q27 0 48.5 15t29.5 40l102 295H790v250H660ZM495-500q-25 0-42.5-17.5T435-560q0-25 17.5-42.5T495-620q25 0 42.5 17.5T555-560q0 25-17.5 42.5T495-500ZM220.08-727q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM150-80v-290H80v-250q0-24.75 17.63-42.38Q115.25-680 140-680h160q24.75 0 42.38 17.62Q360-644.75 360-620v250h-70v290H150Zm290 0v-170h-50v-166q0-18.33 12.83-31.17Q415.67-460 434-460h122q18.33 0 31.17 12.83Q600-434.33 600-416v166h-50v170H440Z"/></svg>
            <p class="text-3xl font-thin">674</p>
            <p class="text-lg font-thin">Kepala Keluarga</p>
          </div>
          <!-- Card 3 -->
          <div class="bg-accent2 text-type1 rounded-xl shadow-md p-12 w-[310px] ml-[56px] md:-ml-[156px] -mt-1 flex flex-col">
            <svg class="ml-[180px] -mt-7" xmlns="http://www.w3.org/2000/svg" 
            height="60px" 
            viewBox="0 -960 960 960"
            width="60px" 
            fill="#DEA521">
            <path d="M800-800v220h-60v-117L556-514q21 29 32.5 63.5T600-380q0 92-64 156t-156 64q-92 0-156-64t-64-156q0-92 64-156t156-64q36 0 70 11.5t63 32.5l184-184H580v-60h220ZM380.2-540Q314-540 267-493.2t-47 113q0 66.2 46.8 113.2t113 47q66.2 0 113.2-46.8t47-113q0-66.2-46.8-113.2t-113-47Z"/></svg>
            <p class="text-3xl font-thin">1.049</p>
            <p class="text-lg font-thin">Penduduk Laki-Laki</p>
          </div>
          <!-- Card 4 -->
          <div class="bg-accent2 text-type1 rounded-xl shadow-md p-12 w-[310px] -ml-[15px] md:-ml-[260px] -mt-1 flex flex-col">
            <svg class="ml-[180px] -mt-7" xmlns="http://www.w3.org/2000/svg" 
            height="60px" 
            viewBox="0 -960 960 960" 
            width="60px" 
            fill="#DEA521">
            <path d="M450-120v-80h-80v-60h80v-102q-82-11-136-73.71-54-62.7-54-145.29 0-91.42 64.5-155.21Q389-800 480-800t155.5 63.79Q700-672.42 700-581q0 82.59-54 145.29Q592-373 510-362v102h80v60h-80v80h-60Zm29.8-300q66.2 0 113.2-46.8t47-113q0-66.2-46.8-113.2t-113-47Q414-740 367-693.2t-47 113q0 66.2 46.8 113.2t113 47Z"/></svg>
            <p class="text-3xl">1.065</p>
            <p class="text-lg font-thin">Penduduk Perempuan</p>
          </div>
        </div>
        <p class="flex items-center ml-[1240px] mt-5 text-secondary text-xl">Selengkapnya
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#DEA521">
          <path d="M411-481 213-679l42-42 240 240-240 240-42-42 198-198Zm253 0L466-679l42-42 240 240-240 240-42-42 198-198Z"/>
        </svg>
      </p>
    </div>
  </UserLayout>
</template>

<script setup>

import UserLayout from '@/Layouts/UserLayout.vue';

</script>

<style scoped>
</style>
