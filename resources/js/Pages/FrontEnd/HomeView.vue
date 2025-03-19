<!-- Homeview.vue -->
<template>
  <UserLayout>
    <header class="relative flex flex-col items-center justify-center w-full h-screen bg-red-100">
      <img src="../../../images/profile.jpg" alt="Logo" class="absolute z-0 w-full h-screen object-cover" />
      <div class="absolute bg-black z-0 w-full h-screen opacity-60">
        
      </div>
      <div class="absolute z-10 font-merriweather text-type1 mt-4 px-6 md:px-0">
        <h1 class=" justify-center items-center text-center text-4xl md:text-6xl font-semibold">Selamat Datang</h1>
        <h2 class=" justify-center items-center text-center text-md md:text-[24px] font-thin mt-1 md:mt-4">Sumber informasi terbaru tentang pemerintahan di Desa Pao-Pao</h2>
      </div>
    </header>
    <main class="max-w-screen-2xl mx-auto w-full">
      <!-- Bagian Selamat Datang -->
      <article class="relative w-full  flex flex-col-reverse md:flex-row items-center justify-center font-merriweather bg-type1 md:px-56 py-10 pt-4 md:pt-40"> 
        <!-- Text Section -->
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
        <figure class="w-48 h-48 my-12 lg:w-80 lg:h-80 flex items-center justify-center rounded-full">
          <img src="../../../images/profile.jpg" alt="Desa Pao Pao" class=" h-full object-cover rounded-full" />
        </figure>
      </article>


      <!-- Berita Terbaru-->
      <article class="w-full px-4 md:px-32 flex flex-col py-4 md:py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2 my-3 ml-2">
          Berita Terbaru
        </h1>
        <!-- <NewsCard
          v-for="news in newsList"
          :key="news.id"
          :image="news.image"
          :title="news.title"
          :description="news.description"></NewsCard> -->
        <div class="flex flex-col md:flex-row justify-center space-y-4 md:space-y-0 ">
          <NewsCard />
          <NewsCard />
          <NewsCard />
        </div>
        <h1 class="text-lg md:text-xl font-semibold text-secondary my-4 ml-2 text-right justify-end">
          Selengkapnya >>
        </h1>

      </article>

      
      <!-- Event Terdekat-->
      <article class="w-full px-8 md:px-32 flex flex-col py-4 md:py-10 bg-type1 font-merriweather">
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

      <!-- Galeri Desa -->
      <article class="w-full px-8 md:px-32 flex flex-col py-6 md:py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2">
          Galeri Desa
        </h1>
        <h2 class="text-sm md:text-xl  text-black my-2 md:my-4 mb-4">
          <p class="italic">Foto atau Video seputar Desa </p>
        </h2>
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
      

      <!-- Produk Unggulan-->
      <article class="w-full px-8 md:px-32 flex flex-col py-6 md:py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2">
          Produk Unggulan
        </h1>
        <h2 class="text-sm md:text-xl  text-black my-2 md:my-4 mb-4 ">
          <p class="italic">Produk unggulan desa yang berasal dari UMKM dan sumber daya alam desa</p>
        </h2>
        <div class="grid grid-cols-2 md:flex md:flex-row justify-center md:space-x-8 -m-2">
          <ProductCard />
          <ProductCard />
          <ProductCard />
          <ProductCard />
        </div>
        <h1 class="text-lg md:text-xl font-semibold text-secondary my-6 ml-2 text-right justify-end">
          Selengkapnya >>
        </h1>
      </article>


      <!-- Layanan Publik-->
      <article class="w-full px-8 md:px-32 flex flex-col py-4 md:py-10 md:pb-16 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2">
          Layanan Publik
        </h1>
        <h2 class="text-sm md:text-xl  text-black my-2 md:my-4 mb-4 ">
          <p class="italic">Produk unggulan desa yang berasal dari UMKM dan sumber daya alam desa</p>
        </h2>
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 justify-between py-4 md:px-28">
          <div class="flex w-full md:w-1/3 space-x-6 px-3">
            <div class="flex px-8 py-8 flex-col bg-secondary rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-colors duration-300 h-">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="currentColor" class="text-accent1">
                  <path d="M240-400h123l199.08-200q8.96-9 13.44-20.5Q580-632 580-643t-4-22q-4-11-13-20l-36.8-37.19Q517-731 506-735.5t-23-4.5q-11 0-22 4t-20.04 13L240-523v123Zm280-243-37-37 37 37ZM300-460v-38l102-101 19 18 18 19-101 102h-38Zm121-121 18 19-37-37 19 18Zm14 181h285v-60H495l-60 60ZM80-80v-740q0-24 18-42t42-18h680q24 0 42 18t18 42v520q0 24-18 42t-42 18H240L80-80Zm134-220h606v-520H140v600l74-80Zm-74 0v-520 520Z"/>
                </svg>
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
                <svg xmlns=w3.org/2000/svg height="48px" viewBox="0 -960 960 960" width="48px" fill="currentColor" class="text-accent1">
                  <path d="M160-120v-60h480v60H160Zm222-212L160-554l70-72 224 222-72 72Zm254-254L414-810l72-70 222 222-72 72Zm202 426L302-696l42-42 536 536-42 42Z"/>"http://www.
                </svg>
              </div>
              <div class="mt-2 space-y-1 ">
                <h2 class="px-1 text-xl font-semibold text-accent1">Produk Hukum</h2>
                <h3 class="px-1 text-base font-normal  text-accent1 leading-5">Ajukan feedback dan saran mu untuk kami dapat memperbaiki diri</h3>
              </div>
            </div>
          </div>
          <div class="flex w-full md:w-1/3 space-x-6 px-3">
            <div class="flex px-8 py-8 flex-col bg-secondary rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-colors duration-300 h-">
              <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="currentColor" class="text-accent1">
                <path d="M450-240h60v-40h60q12.75 0 21.38-8.63Q600-297.25 600-310v-130q0-12.75-8.62-21.38Q582.75-470 570-470H420v-70h180v-60h-90v-40h-60v40h-60q-12.75 0-21.37 8.62Q360-582.75 360-570v130q0 12.75 8.63 21.37Q377.25-410 390-410h150v70H360v60h90v40ZM220-80q-24 0-42-18t-18-42v-680q0-24 18-42t42-18h341l239 239v501q0 24-18 42t-42 18H220Zm0-60h520v-474L534-820H220v680Zm0 0v-680 680Z"/>
              </svg>
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
