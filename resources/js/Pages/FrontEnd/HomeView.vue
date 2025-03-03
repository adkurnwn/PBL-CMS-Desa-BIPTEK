<!-- Homeview.vue -->
<template>
  <UserLayout>
    <div class="flex flex-col items-center justify-center h-screen bg-red-100">
      <h1 class="text-4xl font-bold text-blue-600">Welcome to the Home Page</h1>
      <a href="/news" class="mt-4 text-blue-500 underline">Go to News Page</a>
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
