<!-- Homeview.vue -->
<template>
  <UserLayout>
    <div class="flex flex-col items-center justify-center h-screen bg-green-100">
      <h1 class="text-4xl font-bold text-green-600">Latest News</h1>
      <a href="/" class="mt-4 text-green-500 underline">Back to Home Page</a>
    </div>
  </UserLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

</script>

<style scoped>
h1 {
  color: #4f46e5;
}
</style>
