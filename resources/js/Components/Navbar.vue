<template>
  <nav
    :class="[
      'w-full fixed top-0 z-50 flex items-center justify-between px-6 md:px-8 py-4 transition-colors duration-300',
      isScrolled || !isHomePage ? 'bg-green-800' : 'bg-transparent'
    ]"
  >
    <div class="flex items-center space-x-4">
      <img src="../../images/LOGO_KABUPATEN_KLATEN.png" alt="Logo" class="w-10 h-12 md:w-12 md:h-16" />
      <div>
        <h1 class="text-white font-bold text-base md:text-lg">Desa Karanganom</h1>
        <p class="text-white text-sm md:text-base">Kecamatan Klaten Utara, Klaten</p>
      </div>
    </div>
    
    <!-- Mobile Menu Button -->
    <button @click="toggleMenu" class="md:hidden text-white text-xl">
      ☰
    </button>

    <!-- Navigation Links -->
    <ul
      :class="[
        'md:flex md:space-x-8 text-white font-semibold',
        isMenuOpen ? 'block' : 'hidden',
        'absolute md:static top-16 left-0 w-full md:w-auto bg-green-800 md:bg-transparent md:flex flex-col md:flex-row space-y-4 md:space-y-0 text-center md:text-left p-4 md:p-0'
      ]"
    >
      <li><a href="/" :class="{ 'border-b-2 border-white': isHomePage }">Beranda</a></li>
      <li><a href="#">Profil Desa</a></li>
      <li><a href="/news">Informasi & Berita</a></li>
      <li><a href="#">Layanan Publik</a></li>
    </ul>
  </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isScrolled = ref(false);
const isMenuOpen = ref(false);

// Detect if the current page is the home page
const isHomePage = window.location.pathname === '/';

const handleScroll = () => {
  if (isHomePage) {
    isScrolled.value = window.scrollY > 50;
  }
};

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

onMounted(() => {
  if (isHomePage) {
    window.addEventListener('scroll', handleScroll);
  }
});

onUnmounted(() => {
  if (isHomePage) {
    window.removeEventListener('scroll', handleScroll);
  }
});
</script>

<style scoped>
nav {
  backdrop-filter: blur(10px);
}
</style>
