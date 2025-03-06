<!-- Homeview.vue -->
<template>
  <UserLayout>
    <div class="relative flex flex-col items-center justify-center h-screen bg-red-100">
      <img src="../../../images/profile.jpg" alt="Logo" class="absolute z-0 w-screen h-screen object-cover" />
      <div class="absolute bg-black z-0 w-full h-screen opacity-60">
        
      </div>
      <div class="absolute z-10 font-merriweather text-type1 mt-4">
        <h1 class=" justify-center items-center text-center text-4xl md:text-6xl font-semibold">Selamat Datang</h1>
        <h2 class=" justify-center items-center text-center text-md md:text-[24px] font-thin mt-1 md:mt-4">Sumber informasi terbaru tentang pemerintahan di Desa Pao-Pao</h2>
      </div>
      
    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

</script>

<style scoped>

</style>
