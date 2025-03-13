<!-- Homeview.vue -->
<template>
  <UserLayout>
    <header class="relative flex flex-col items-center justify-center w-full h-screen bg-red-100">
      <img src="../../../images/profile.jpg" alt="Logo" class="absolute z-0 w-full h-screen object-cover" />
      <div class="absolute bg-black z-0 w-full h-screen opacity-60">
        
      </div>
      <div class="absolute z-10 font-merriweather text-type1 mt-4">
        <h1 class=" justify-center items-center text-center text-4xl md:text-6xl font-semibold">Selamat Datang</h1>
        <h2 class=" justify-center items-center text-center text-md md:text-[24px] font-thin mt-1 md:mt-4">Sumber informasi terbaru tentang pemerintahan di Desa Pao-Pao</h2>
      </div>
    </header>
    <main>
      <!-- Bagian Selamat Datang -->
      <article class="relative w-full  flex flex-col-reverse md:flex-row items-center justify-center font-merriweather bg-type1 md:px-56 py-10 pt-4 md:pt-40"> 
        `<!-- Text Section -->
        <section class="flex flex-col  w-2/3 space-y-3 md:mx-8">
          <h1 class="text-xl md:text-3xl font-semibold text-accent2 text-center md:text-left">
            Selamat datang di website resmi Pemerintah Desa Pao Pao!
          </h1>
          <div class="space-y-3 md:text-xl text-semiBlack text-justify font-thin">
            <p>
              Kami berkomitmen untuk menghadirkan layanan yang transparan, informatif, dan bermanfaat bagi seluruh masyarakat serta para pengunjung yang ingin mengenal desa kami lebih dekat.
            </p>
            <p>
              Temukan informasi seputar sejarah desa, visi dan misi pemerintahan, potensi unggulan, berita terbaru, serta layanan publik.
            </p>
          </div>
        </section>

        <!-- Image Section -->
        <figure class="w-48 h-48 my-12 md:w-80 md:h-80 flex items-center justify-center rounded-full">
          <img src="../../../images/profile.jpg" alt="Desa Pao Pao" class=" h-full object-cover rounded-full" />
        </figure>
      </article>


      <!-- Berita Terbaru-->
      <article class="w-full px-4 md:px-32 flex flex-col py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2 my-3 ml-2">
          Berita Terbaru
        </h1>
        <!-- <NewsCard
          v-for="news in newsList"
          :key="news.id"
          :image="news.image"
          :title="news.title"
          :description="news.description"></NewsCard> -->
        <div class="flex flex-col md:flex-row justify-center space-y-4 md:space-y-0 md:space-x-4">
          <NewsCard />
          <NewsCard />
          <NewsCard />
        </div>
        <h1 class="text-lg md:text-xl font-semibold text-secondary my-4 ml-2 text-right justify-end">
          Selengkapnya >>
        </h1>

      </article>


      <!-- Event Terdekat-->
      <article class="w-full px-8 md:px-32 flex flex-col py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2">
          Event Terdekat
        </h1>
        <h2 class="text-sm md:text-xl  text-black my-4">
          <p class="italic">Informasi tentang event atau kegiatan terdekat Desa</p>
        </h2>
        <div class="flex flex-col md:flex-row justify-center md:space-x-4 space-y-2 md:space-y-0">
          <EventCard />
          <EventCard />
          <EventCard />
        </div>
      </article>


      <!-- Galeri Desa -->
      <article class="w-full px-32 flex flex-col py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2">
          Galeri Desa
        </h1>
        <h2 class="text-sm md:text-xl  text-black my-4">
          <p class="italic">Foto atau Video seputar Desa </p>
        </h2>
        <div class="flex flex-col w-full justify-between space-y-4">
          <div class="flex flex-row w-full justify-between space-x-4 ">
            <img src="../../../images/berita.png" alt="" class="w-3/5 h-[400px] rounded-lg object-cover" />
            <img src="../../../images/berita.png" alt="" class="w-2/5 h-[400px] rounded-lg object-cover" />
          </div>
          <div class="flex flex-row w-full justify-between ">
            <img src="../../../images/berita.png" alt="" class="w-1/3 h-[300px] rounded-2xl object-cover pr-2" />
            <img src="../../../images/berita.png" alt="" class="w-1/3 h-[300px] rounded-2xl object-cover pr-2" />
            <img src="../../../images/berita.png" alt="" class="w-1/3 h-[300px] rounded-2xl object-cover " />

          </div>
            
        </div>
      </article>
      

      <!-- Produk Unggulam-->
      <article class="w-full px-8 md:px-32 flex flex-col py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2">
          Produk Unggulan
        </h1>
        <h2 class="text-sm md:text-xl  text-black my-4">
          <p class="italic">Produk unggulan desa yang berasal dari UMKM dan sumber daya alam desa</p>
        </h2>
        <div class="grid grid-cols-2 md:flex md:flex-row justify-center md:space-x-8 -m-2">
          <ProductCard />
          <ProductCard />
          <ProductCard />
          <ProductCard />
        </div>
        <h1 class="text-lg md:text-xl font-semibold text-secondary my-4 ml-2 text-right justify-end">
          Selengkapnya >>
        </h1>
      </article>


      <!-- Produk Hukum-->
      <article class="w-full px-8 md:px-32 flex flex-col py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2">
          Layanan Publik
        </h1>
        <h2 class="text-sm md:text-xl  text-black my-4">
          <p class="italic">Produk unggulan desa yang berasal dari UMKM dan sumber daya alam desa</p>
        </h2>
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 justify-between py-4 md:px-28">
          <div class="flex w-full md:w-1/3 space-x-6 px-3">
            <div class="flex px-8 py-8 flex-col bg-secondary rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-colors duration-300 h-">
              <div>
                <img src="../../../images/rate_review.png" alt="" class="w-[50px] h-[50px]" />
              </div>
              <div class="mt-2 space-y-1 ">
                <h2 class="px-1 text-xl font-semibold text-accent1">Feedback & Saran</h2>
                <h3 class="px-1 text-base font-normal  text-accent1 leading-5">Ajukan feedback dan saran mu untuk kami dapat memperbaiki diri</h3>
              </div>
            </div>
          </div>
          <div class="flex w-full md:w-1/3 space-x-6 px-3">
            <div class="flex px-8 py-8 flex-col bg-secondary rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-colors duration-300 h-">
              <div>
                <img src="../../../images/gavel.png" alt="" class="w-[50px] h-[50px]" />
              </div>
              <div class="mt-2 space-y-1 ">
                <h2 class="px-1 text-xl font-semibold text-accent1">Produk Hukum</h2>
                <h3 class="px-1 text-base font-normal  text-accent1 leading-5">Ajukan feedback dan saran mu untuk kami dapat memperbaiki diri</h3>
              </div>
            </div>
          </div>
          <div class="flex w-full md:w-1/3 space-x-6 px-3">
            <div class="flex px-8 py-8 flex-col bg-secondary rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-colors duration-300 h-">
              <div>
                <img src="../../../images/request_page.png" alt="" class="w-[50px] h-[50px]" />
              </div>
              <div class="mt-2 space-y-1 ">
                <h2 class="px-1 text-xl font-semibold text-accent1">Laporan Dana Desa</h2>
                <h3 class="px-1 text-base font-normal  text-accent1 leading-5">Ajukan feedback dan saran mu untuk kami dapat memperbaiki diri</h3>
              </div>
            </div>
          </div>
        </div>
      </article>
    </main>
  </UserLayout>
</template>

<script setup>
import EventCard from '@/Components/EventCard.vue';
import NewsCard from '@/Components/NewsCard.vue';
import ProductCard from '@/Components/ProductCard.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

</script>

<style scoped>

</style>
