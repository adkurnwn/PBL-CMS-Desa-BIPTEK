<!-- Homeview.vue -->
<template>
  <UserLayout>
    <div class="flex flex-col items-center justify-center h-screen bg-green-100">
      <!-- <h1 class="text-4xl font-bold text-green-600">Profil Desa</h1> -->
      <img src="../../../images/profile.jpg" alt="Logo" class="absolute z-0 w-screen h-screen" />
      <div class="absolute bg-black z-0 w-full h-screen opacity-60"></div>
    </div>
  </UserLayout>
</template>

<script setup>

import UserLayout from '@/Layouts/UserLayout.vue';

</script>

<style scoped>
h1 {
  color: #4f46e5;
}
</style>
