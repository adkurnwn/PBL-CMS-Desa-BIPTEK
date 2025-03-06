<template>
  <div>
    
    <main>
      <div class="fixed top-0 left-0 w-full z-50">
        <Navbar />
      </div>

      <slot />
      <!-- <Footer /> -->
    </main>
    
  </div>
</template>

<script setup>
import Navbar from '../Components/Navbar.vue'
import Footer from '../Components/Footer.vue'
</script>
