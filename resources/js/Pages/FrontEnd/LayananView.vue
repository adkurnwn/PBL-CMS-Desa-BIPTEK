<!-- Homeview.vue -->
<template>
  <UserLayout>      
    <header class="flex flex-col xl:flex-row max-w-7xl mx-auto w-full justify-center items-center bg-type1 font-merriweather">
      <!--Layanan Publik-->
      <article class="flex xl:flex-row flex-col w-full px-10 sm:px-24 md:px-28 xl:px-32 xl:space-x-5 xl:mt-60 sm:mt-40 mt-32">
        <img src="../../../images/LAYANAN_1.png" alt="Logo" class="h-auto w-full xl:max-w-lg md:max-w-4xl sm:max-w-xl"/>
        <div class="xl:max-w-lg md:max-w-4xl sm:max-w-xl text-justify xl:space-y-3 md:space-y-3 sm:space-y-2 space-y-2 xl:mt-8 sm:mt-3 mt-2">
          <h1 class="text-accent2 font-bold xl:text-3xl sm:text-2xl text-xl">Layanan Desa Pao-Pao</h1>
          <p class="text-accent1 xl:text-xl sm:text-lg text-xs">Kami berkomitmen untuk memberikan layanan yang transparan, informatif, dan bermanfaat bagi seluruh masyarakat Desa Pao-Pao.</p>
          <p class="text-accent1 xl:text-xl sm:text-lg text-xs">Masyarakat dapat menemukan informasi mengenai Laporan Dana Desa, Produk Hukum dan Produk Unggulan Desa.</p>
        </div>
      </article>
    </header>
    <main class="flex items-center justify-center max-w-7xl mx-auto w-full font-merriweather">
    <!--Produk Unggulan--> 
    <article class="=w-full px-10 sm:px-24 md:px-28 xl:px-32 flex flex-col py-6 lg:py-10 bg-type1 font-merriweather xl:mt-20 md:mt-16 sm:mt-12 mt-7">
      <h1 class="xl:text-3xl sm:text-2xl text-xl font-semibold text-accent2">Produk Unggulan</h1>
        <h2 class="xl:text-xl sm:text-lg text-xs text-semiBlack xl:my-3 sm:my-2 my-2 mb-4">
          <p class="italic">Produk unggulan desa yang berasal dari UMKM dan sumber daya alam desa</p>
        </h2>
        <div class="grid grid-cols-2 gap-1 xl:flex xl:flex-row justify-center -m-2">
          <ProductCard />
          <ProductCard />
          <ProductCard />
          <ProductCard />
        </div>
        <h1 class="flex text-secondary text-md lg:text-xl justify-end mt-5">Selengkapnya
          <svg class="mt-1 w-5 h-5 md:w-6 md:h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="24px" fill="#DEA521">
            <path d="M411-481 213-679l42-42 240 240-240 240-42-42 198-198Zm253 0L466-679l42-42 240 240-240 240-42-42 198-198Z"/>
          </svg>
        </h1>
    </article>
    <article>
      
    </article>

    </main>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

</script>

<style scoped>

</style>
