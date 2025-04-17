<template>
  <div>
    <main class="bg-type1 min-h-screen">
      <!-- Navbar -->
      <div class="fixed top-0 left-0 w-full z-50">
        <Navbar />
      </div>

      <!-- Page Content -->
      <slot />

    </main>
  </div>
</template>

<script setup>
