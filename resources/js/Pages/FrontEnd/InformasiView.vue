<!-- Homeview.vue -->
<template>
  <UserLayout>
    <header class="relative flex flex-col items-center justify-center max-w-fit mx-auto w-full bg-type1">
      <!-- Galeri Desa -->
      <article class="w-full px-8 flex flex-col pt-32 pb-8 md:pb-24 bg-type1 font-merriweather">
        <div class="flex flex-col w-full justify-between space-y-4">
          <div class="flex flex-row w-full justify-between space-x-2 ">
            <img src="../../../images/berita.png" alt="" class="w-3/5 h-[150px] md:h-[400px] rounded-lg object-cover md:mr-2" />
            <img src="../../../images/berita.png" alt="" class="w-2/5 h-[150px] md:h-[400px] rounded-lg object-cover" />
          </div>
          <div class="flex flex-col md:flex-row w-full justify-between space-y-3 md:space-y-0 ">
            <img src="../../../images/berita.png" alt="" class="w-full md:w-1/3 h-[200px] md:h-[300px] rounded-2xl object-cover md:mr-2" />
            <img src="../../../images/berita.png" alt="" class="w-full md:w-1/3 h-[200px] md:h-[300px] rounded-2xl object-cover md:mr-2" />
            <img src="../../../images/berita.png" alt="" class="w-full md:w-1/3 h-[200px] md:h-[300px] rounded-2xl object-cover " />

          </div>
            
        </div>
      </article>
    </header>

    <main class="max-w-7xl mx-auto w-full">
      <!-- Event Terdekat-->
      <article class="w-full px-8 flex flex-col py-6 md:py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2">
          Event Terdekat
        </h1>
        <h2 class="text-sm md:text-xl  text-black my-2 md:my-4 mb-4">
          <p class="italic">Informasi tentang event atau kegiatan terdekat Desa</p>
        </h2>
        <div class="flex flex-col md:flex-row justify-center md:space-x-4 space-y-2 md:space-y-0">
          <EventCard />
          <EventCard />
          <EventCard />
        </div>
      </article>

      <!-- Berita Terbaru-->
      <article class="w-full px-4  flex flex-col py-6 md:py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2 my-3 ml-2">
          Berita Terbaru
        </h1>
        <!-- <NewsCard
          v-for="news in newsList"
          :key="news.id"
          :image="news.image"
          :title="news.title"
          :description="news.description"></NewsCard> -->
        <div class="flex flex-col md:grid md:grid-cols-3 justify-center space-y-1 md:space-y-0  ">
          <NewsCard />
          <NewsCard />
          <NewsCard />
          <NewsCard />
          <NewsCard />
          <NewsCard />
        </div>
        <h1 class="text-lg md:text-xl font-semibold text-secondary my-4 ml-2 text-right justify-end">
          Selengkapnya >>
        </h1>
      </article>

    </main>


  </UserLayout>
</template>

<script setup>
import EventCard from '@/Components/EventCard.vue';
import NewsCard from '@/Components/NewsCard.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

</script>

<style scoped>

</style>
