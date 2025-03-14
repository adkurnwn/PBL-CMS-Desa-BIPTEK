<!-- Homeview.vue -->
<template>
  <UserLayout>
    <div class="relative flex flex-col items-center justify-center h-screen bg-red-100">
      <img src="../../../images/profile.jpg" alt="Logo" class="absolute z-0 w-screen h-screen" />
      <div class="absolute bg-black z-0 w-full h-screen opacity-60">
        
      </div>
      <div class="absolute z-10">
        <h1 class=" justify-center items-center text-center text-4xl font-bold ">Welcome to the Home Page</h1>
      </div>
      
    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

</script>

<style scoped>
h1 {
  color: #8681dc;
}
</style>
