<script setup>
import { ref, onMounted, computed } from 'vue';
import EventCard from '@/Components/EventCard.vue';
import NewsCard from '@/Components/NewsCard.vue';
import ProductCard from '@/Components/ProductCard.vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import useApiFetch from '@/Utility/useApiFetch';
import { useMeta } from '@/Utility/useMeta';
import { Link, Head, usePage } from '@inertiajs/vue3';
import { useImageErrorHandler } from '@/Utility/useImageErrorHandler';

const events = ref([]);
const news = ref([]);
const gallery = ref({});
const produkDesa = ref([]);
const { handleImageError, getImageSrc } = useImageErrorHandler();

// Reactive meta description and image
const metaDescriptionText = ref('Website resmi pemerintah desa - Sumber informasi terbaru tentang pemerintahan, berita, event, dan layanan publik desa.');
const metaImagePath = ref('default-home-image.jpg');

// Meta tags setup
const {
  metaTitle,
  metaDescription,
  metaImage,
  canonicalUrl,
  metaType,
  metaSiteName
} = useMeta({
  title: 'Beranda',
  description: metaDescriptionText,
  type: 'website',
  url: window.location.origin,
  image: metaImagePath
});

// Function to update description and image
const updateMetaData = (layoutName, heroImage) => {
  metaDescriptionText.value = `Selamat datang di website resmi Pemerintah ${layoutName}! Sumber informasi terbaru tentang pemerintahan, berita, event, dan layanan publik desa.`;
  if (heroImage) {
    metaImagePath.value = heroImage;
  }
  return null; // Return null to not render anything
};

// Get layout data from Inertia page props
const page = usePage();
const layout = computed(() => page.props.layout || {});

// Fetch data function
const fetchData = async () => {
  const eventApi = useApiFetch();
  const newsApi = useApiFetch();
  const galleryApi = useApiFetch();
  const produkApi = useApiFetch();

  const eventsLoading = ref(true);
  const newsLoading = ref(true);
  const galleryLoading = ref(true);
  const produkLoading = ref(true);

  await Promise.all([
    eventApi.fetchData('/api/events/preview'),
    newsApi.fetchData('/api/news/preview'),
    galleryApi.fetchData('/api/gallery/highlight'),
    produkApi.fetchData('/api/produk-desa/preview')
  ]);

  events.value = eventApi.data.value || [];
  news.value = newsApi.data.value || [];
  gallery.value = galleryApi.data.value || {};
  produkDesa.value = produkApi.data.value || [];

  eventsLoading.value = eventApi.loading.value;
  newsLoading.value = newsApi.loading.value;
  galleryLoading.value = galleryApi.loading.value;
  produkLoading.value = produkApi.loading.value;
};

onMounted(() => {
  fetchData();
});
</script>
<!-- Homeview.vue -->
<template>
  <Head>
    <title>{{ metaTitle }}</title>
    <meta name="description" :content="metaDescription" />
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" :content="metaType" />
    <meta property="og:title" :content="metaTitle" />
    <meta property="og:description" :content="metaDescription" />
    <meta property="og:url" :content="canonicalUrl" />
    <meta property="og:site_name" :content="metaSiteName" />
    <meta property="og:image" :content="metaImage" />
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="metaTitle" />
    <meta name="twitter:description" :content="metaDescription" />
    <meta name="twitter:image" :content="metaImage" />
    
    <!-- Canonical URL -->
    <link rel="canonical" :href="canonicalUrl" />
  </Head>

  <UserLayout v-slot="{ layout }">
    <!-- Call function when template renders -->
    {{ updateMetaData(layout.name, layout.hero_image) }}
    
    <header class="relative flex flex-col items-center justify-center w-full h-screen bg-red-100">
      <img :src="getImageSrc(`/storage/${layout.hero_image}`)" alt="Hero Image"
        class="absolute z-0 w-full h-screen object-cover" @error="handleImageError(`/storage/${layout.hero_image}`)" />
      <div class="absolute bg-black z-0 w-full h-screen opacity-60">

      </div>
      <div class="absolute z-10 font-merriweather text-type1 mt-4 px-6 md:px-0">
        <h1
          class=" justify-center items-center text-center text-4xl md:text-5xl 3xl:text-6xl 4xl:text-7xl font-semibold">
          Selamat Datang</h1>
        <h2
          class=" justify-center items-center text-center text-md md:text-xl 3xl:text-2xl 4xl:text-3xl  font-thin mt-1 md:mt-4 3xl:mt-6">
          Sumber informasi terbaru tentang pemerintahan di {{ layout.name }}</h2>
      </div>
    </header>
    <main class="max-w-screen-3xl mx-auto w-full ">
      <!-- Bagian Selamat Datang -->
      <article
        class="relative w-full flex flex-col-reverse lg:flex-row items-center justify-center font-merriweather bg-type1 sm:px-[13.91%] py-4 md:mt-10 md:py-10">
        <!-- Text Section -->
        <section class="flex flex-col w-2/3 space-y-3 md:mx-8">
          <h1 class="xl:text-3xl sm:text-xl text-md font-semibold text-accent2 text-center lg:text-left">
            Selamat datang di website resmi Pemerintah {{ layout.name }}!
          </h1>
          <div class="space-y-3 xl:text-xl sm:text-lg text-xs  text-semiBlack text-justify font-thin">
            <p>
              Kami berkomitmen untuk menghadirkan layanan yang transparan, informatif, dan bermanfaat bagi seluruh
              masyarakat serta para pengunjung yang ingin mengenal desa kami lebih dekat.
            </p>
            <p>
              Temukan informasi seputar sejarah desa, visi dan misi pemerintahan, potensi unggulan, berita terbaru,
              serta layanan publik.
            </p>
          </div>
        </section>

        <!-- Image Section -->
        <figure class="w-48 h-48 my-5 lg:w-80 lg:h-80 flex items-center justify-center rounded-full">
          <img :src="getImageSrc(`/storage/${layout.circle_image}`)" alt="Hero Image"
            class=" h-full object-cover rounded-full" @error="handleImageError(`/storage/${layout.circle_image}`)"/>
        </figure>
      </article>


      <div class="px-[6.56%]">
        <!-- Berita Terbaru-->
        <article class="w-full flex flex-col py-4 md:py-10 bg-type1 font-merriweather">
          <h1 class="text-xl md:text-3xl font-semibold text-accent2 my-3 md:ml-2">
            Berita Terbaru
          </h1>

          <div class="flex flex-col lg:flex-row justify-center space-y-4 md:space-y-0 ">
            <div v-if="newsLoading" class="text-center w-full">
              <p>Memuat berita...</p>
            </div>
            <div v-else-if="news.length === 0" class="text-center w-full">
              <p>Tidak ada berita terbaru</p>
            </div>
            <NewsCard v-else v-for="newsItem in news" :key="newsItem.id" :href="`/berita/${newsItem.slug}`"
              :isHome="true" :news="newsItem" />

          </div>
          <a href="/berita" class="text-lg md:text-xl xl:text-2xl font-semibold text-secondary hover:text-secondary/75 transition-colors duration-500 my-6 ml-2 text-right justify-end">
            Selengkapnya >>
          </a>

        </article>


        <!-- Event Terdekat-->
        <article class="w-full flex flex-col py-4 md:py-10 bg-type1 font-merriweather">
          <h1 class="text-xl md:text-3xl font-semibold text-accent2">
            Event Terdekat
          </h1>
          <h2 class="text-sm md:text-xl text-black my-2 md:my-4 mb-4">
            <p class="italic">Informasi tentang event atau kegiatan terdekat Desa</p>
          </h2>
          <div class="flex flex-col sm:grid sm:grid-cols-2 xl:flex xl:flex-row justify-center lg:space-x-4 space-y-2 sm:space-y-0 gap-2 xl:gap-0">
            <div v-if="eventsLoading" class="text-center w-full">
              <p>Loading events...</p>
            </div>
            <div v-else-if="events.length === 0" class="text-center w-full">
              <p>Tidak ada event yang akan datang</p>
            </div>
            <template v-else>
              <EventCard v-for="event in events.slice(0, 3)" :key="event.id" :event="event" />
            </template>
          </div>
          <Link href="/event" class="text-lg md:text-xl xl:text-2xl font-semibold text-secondary hover:text-secondary/75 transition-colors duration-500 my-6 xl:my-8ml-2 text-right justify-end">
          Selengkapnya &gt;&gt;
          </Link>
        </article>

        <!-- Galeri Desa -->
        <article class="w-full flex flex-col py-6 md:py-10 bg-type1 font-merriweather">
                <div v-if="galleryLoading" class="flex flex-col w-full justify-between space-y-4">
                    <div class="flex flex-row w-full h-full justify-between space-x-3">
                        <div class="w-3/5 h-[150px] sm:h-[225px] md:h-[400px] rounded-lg md:rounded-xl xl:rounded-2xl bg-gray-400 animate-pulse"></div>
                        <div class="w-2/5 h-[150px] sm:h-[225px] md:h-[400px] rounded-lg md:rounded-xl xl:rounded-2xl bg-gray-400 animate-pulse"></div>
                    </div>
                    <div class="flex flex-col sm:flex-row w-full justify-between space-y-3 sm:space-y-0 sm:space-x-3">
                        <div class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-lg md:rounded-xl xl:rounded-2xl bg-gray-400 animate-pulse"></div>
                        <div class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-lg md:rounded-xl xl:rounded-2xl bg-gray-400 animate-pulse"></div>
                        <div class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-lg md:rounded-xl xl:rounded-2xl bg-gray-400 animate-pulse"></div>
                    </div>
                </div>

                <div v-else-if="!gallery.images || gallery.images.length === 0">
                    <div v-if="!galleryTimeout" class="flex flex-col w-full justify-between space-y-4">
                        <div class="flex flex-row w-full h-full justify-between space-x-3">
                            <div class="w-3/5 h-[150px] sm:h-[225px] md:h-[400px] rounded-lg md:rounded-xl xl:rounded-2xl bg-gray-400 animate-pulse"></div>
                            <div class="w-2/5 h-[150px] sm:h-[225px] md:h-[400px] rounded-lg md:rounded-xl xl:rounded-2xl bg-gray-400 animate-pulse"></div>
                        </div>
                        <div class="flex flex-col sm:flex-row w-full justify-between space-y-3 sm:space-y-0 sm:space-x-3">
                            <div class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-lg md:rounded-xl xl:rounded-2xl bg-gray-400 animate-pulse"></div>
                            <div class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-lg md:rounded-xl xl:rounded-2xl bg-gray-400 animate-pulse"></div>
                            <div class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-lg md:rounded-xl xl:rounded-2xl bg-gray-400 animate-pulse"></div>
                        </div>
                    </div>
                    <div v-else class="flex justify-center items-center h-[500px]">
                        <p>Gallery images not available</p>
                    </div>
                </div>

                <div v-else class="flex flex-col w-full justify-between space-y-4">
                    <div class="flex flex-row w-full h-full justify-between space-x-3"
                        v-if="gallery.images.length >= 1">
                        <Link href="/galeri-desa" v-if="gallery.images[0]"
                            :class="gallery.images[1] ? 'w-3/5' : 'w-full'"
                            class="relative group cursor-pointer overflow-hidden rounded-lg md:rounded-xl xl:rounded-2xl">
                        <img :src="`/storage/${gallery.images[0].path}`" :alt="gallery.images[0].alt"
                            class="w-full h-[150px] sm:h-[225px] md:h-[400px] object-cover transition-all duration-300 group-hover:brightness-50" />
                        <div
                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-white text-lg md:text-xl font-semibold">Selengkapnya</span>
                        </div>
                        </Link>
                        <Link href="/galeri-desa" v-if="gallery.images[1]"
                            class="w-2/5 relative group cursor-pointer overflow-hidden rounded-lg md:rounded-xl xl:rounded-2xl">
                        <img :src="`/storage/${gallery.images[1].path}`" :alt="gallery.images[1].alt"
                            class="w-full h-[150px] sm:h-[225px] md:h-[400px] object-cover transition-all duration-300 group-hover:brightness-50" />
                        <div
                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-white text-lg md:text-xl font-semibold">Selengkapnya</span>
                        </div>
                        </Link>
                    </div>
                    <div class="flex flex-col sm:flex-row w-full justify-between space-y-3 sm:space-y-0 sm:space-x-3"
                        v-if="gallery.images.length >= 3">
                        <Link href="/galeri-desa" v-if="gallery.images[2]"
                            :class="gallery.images.length === 3 ? 'w-full' : (gallery.images.length === 4 ? 'w-full sm:w-1/2' : 'w-full sm:w-1/3')"
                            class="relative group cursor-pointer overflow-hidden rounded-lg md:rounded-xl xl:rounded-2xl">
                        <img :src="`/storage/${gallery.images[2].path}`" :alt="gallery.images[2].alt"
                            class="w-full h-[200px] md:h-[300px] object-cover transition-all duration-300 group-hover:brightness-50" />
                        <div
                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-white text-lg md:text-xl font-semibold">Selengkapnya</span>
                        </div>
                        </Link>
                        <Link href="/galeri-desa" v-if="gallery.images[3]"
                            :class="gallery.images.length === 4 ? 'w-full sm:w-1/2' : 'w-full sm:w-1/3'"
                            class="relative group cursor-pointer overflow-hidden rounded-lg md:rounded-xl xl:rounded-2xl">
                        <img :src="`/storage/${gallery.images[3].path}`" :alt="gallery.images[3].alt"
                            class="w-full h-[200px] md:h-[300px] object-cover transition-all duration-300 group-hover:brightness-50" />
                        <div
                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-white text-lg md:text-xl font-semibold">Selengkapnya</span>
                        </div>
                        </Link>
                        <Link href="/galeri-desa" v-if="gallery.images[4]"
                            class="w-full sm:w-1/3 relative group cursor-pointer overflow-hidden rounded-lg md:rounded-xl xl:rounded-2xl">
                        <img :src="`/storage/${gallery.images[4].path}`" :alt="gallery.images[4].alt"
                            class="w-full h-[200px] md:h-[300px] object-cover transition-all duration-300 group-hover:brightness-50" />
                        <div
                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-white text-lg md:text-xl font-semibold">Selengkapnya</span>
                        </div>
                        </Link>
                    </div>
                </div>
            </article>


        <!-- Produk Unggulan-->
        <article class="w-full flex flex-col py-6 lg:py-10 bg-type1 font-merriweather">
          <h1 class="text-xl md:text-3xl font-semibold text-accent2">
            Produk Unggulan
          </h1>
          <h2 class="text-sm md:text-xl  text-black my-2 md:my-4 mb-4 ">
            <p class="italic">Produk unggulan desa yang berasal dari UMKM dan sumber daya alam desa</p>
          </h2>
          <div class="grid grid-cols-2 sm:flex sm:flex-row md:grid md:grid-cols-2 xl:flex xl:flex-row justify-center lg:space-x-4 -m-2 w-max-full">
            <div v-if="eventsLoading" class="text-center w-full col-span-2">
              <p>Memuat produk unggulan...</p>
            </div>
            <div v-else-if="produkDesa.length === 0" class="text-center w-full col-span-2">
              <p>Tidak ada produk unggulan</p>
            </div>
            <ProductCard v-else v-for="produkDesa in produkDesa" :key="produkDesa.id" :produkDesa="produkDesa" />
          </div>
          <a href="produk-unggulan"
            class="text-lg md:text-xl xl:text-2xl font-semibold text-secondary hover:text-secondary/75 transition-colors duration-500 my-6 xl:my-8 ml-2 text-right justify-end">
            Selengkapnya >>
          </a>
        </article>


        <!-- Layanan Publik-->
        <article class="w-full flex flex-col py-6 4xl:py-0 4xl:mb-8 bg-type1 font-merriweather">
          <h1 class="text-xl md:text-3xl font-semibold text-accent2">
            Layanan Publik
          </h1>
          <h2 class="text-sm md:text-xl  text-black my-2 md:my-4 mb-4 ">
            <p class="italic">Pusat layanan dan informasi resmi dari Pemerintah Desa untuk masyarakat.</p>
          </h2>
          <div class="flex flex-col sm:grid sm:grid-cols-2 lg:flex lg:flex-row space-y-2 sm:space-y-0 justify-between py-4 sm:gap-4  ">
            <a v-if="layout.produk_hukum" href="/produk-hukum"
              class="flex w-full sm:w-full lg:w-1/3 space-x-6  sm:mx-0">
              <div
                class="flex px-8 py-8 flex-col bg-secondary rounded-lg shadow-sm overflow-hidden hover:shadow-lg hover:bg-secondary/75 transition-colors duration-300 h-">
                <div>
                  <svg xmlns="http://w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="currentColor"
                    class="text-accent1">
                    <path
                      d="M160-120v-60h480v60H160Zm222-212L160-554l70-72 224 222-72 72Zm254-254L414-810l72-70 222 222-72 72Zm202 426L302-696l42-42 536 536-42 42Z" />
                  </svg>
                </div>

                <div class="mt-2 space-y-1 ">
                  <h2 class="px-1 text-xl font-semibold text-accent1">Produk Hukum</h2>
                  <h3 class="px-1 text-base font-normal  text-accent1 leading-5">Ajukan feedback dan saran mu untuk kami
                    dapat
                    memperbaiki diri</h3>
                </div>
              </div>
            </a>
            <a v-if="layout.dana_desa" href="/dana-desa" class="flex w-full sm:w-full lg:w-1/3 space-x-6  sm:mx-0">
              <div
                class="flex px-8 py-8 flex-col bg-secondary rounded-lg shadow-sm overflow-hidden hover:shadow-lg hover:bg-secondary/75 transition-colors duration-300 h-">
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                  fill="currentColor" class="text-accent1">
                  <path
                    d="M450-240h60v-40h60q12.75 0 21.38-8.63Q600-297.25 600-310v-130q0-12.75-8.62-21.38Q582.75-470 570-470H420v-70h180v-60h-90v-40h-60v40h-60q-12.75 0-21.37 8.62Q360-582.75 360-570v130q0 12.75 8.63 21.37Q377.25-410 390-410h150v70H360v60h90v40ZM220-80q-24 0-42-18t-18-42v-680q0-24 18-42t42-18h341l239 239v501q0 24-18 42t-42 18H220Zm0-60h520v-474L534-820H220v680Zm0 0v-680 680Z" />
                </svg>
                <div class="mt-2 space-y-1 ">
                  <h2 class="px-1 text-xl font-semibold text-accent1">Laporan Dana Desa</h2>
                  <h3 class="px-1 text-base font-normal  text-accent1 leading-5">Ajukan feedback dan saran mu untuk kami
                    dapat
                    memperbaiki diri</h3>
                </div>
              </div>
            </a>
            <a href="/statistik-penduduk" class="flex w-full sm:w-full lg:w-1/3 space-x-6 sm:mx-0">
              <div
                class="flex px-8 py-8 flex-col bg-secondary rounded-lg shadow-sm overflow-hidden hover:shadow-lg hover:bg-secondary/75 transition-colors duration-300 h-">
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                  fill="currentColor" class="text-accent1">
                  <path
                    d="M450-240h60v-40h60q12.75 0 21.38-8.63Q600-297.25 600-310v-130q0-12.75-8.62-21.38Q582.75-470 570-470H420v-70h180v-60h-90v-40h-60v40h-60q-12.75 0-21.37 8.62Q360-582.75 360-570v130q0 12.75 8.63 21.37Q377.25-410 390-410h150v70H360v60h90v40ZM220-80q-24 0-42-18t-18-42v-680q0-24 18-42t42-18h341l239 239v501q0 24-18 42t-42 18H220Zm0-60h520v-474L534-820H220v680Zm0 0v-680 680Z" />
                </svg>
                <div class="mt-2 space-y-1 ">
                  <h2 class="px-1 text-xl font-semibold text-accent1">Data Penduduk</h2>
                  <h3 class="px-1 text-base font-normal  text-accent1 leading-5">Statistik jumlah penduduk berdasarkan
                    kategori
                    tertentu.</h3>
                </div>
              </div>
            </a>
            <a href="/profile#struktur-pemerintahan" class="flex w-full sm:w-full lg:w-1/3 space-x-6 sm:mx-0">
              <div
                class="flex px-8 py-8 flex-col bg-secondary rounded-lg shadow-sm overflow-hidden hover:shadow-lg hover:bg-secondary/75 transition-colors duration-300 h-">
                  <svg class=" text-accent1" xmlns="http://www.w3.org/2000/svg" height="48px"
                    viewBox="0 -960 960 960" width="48px" fill="currentColor">
                    <path
                        d="M480-242q-67 0-129 23.5T235-149v9h490v-9q-54-46-116-69.5T480-242Zm0-60q74 0 139.5 24.5T740-211v-609H220v609q55-42 120.5-66.5T480-302Zm2-139q-32.5 0-55.25-22.75T404-519q0-32.5 22.75-55.25T482-597q32.5 0 55.25 22.75T560-519q0 32.5-22.75 55.25T482-441ZM220-80q-24 0-42-18t-18-42v-680q0-24 18-42t42-18h520q24 0 42 18t18 42v680q0 24-18 42t-42 18H220Zm262-301q58 0 98-40t40-98q0-58-40-98t-98-40q-58 0-98 40t-40 98q0 58 40 98t98 40Zm-2-138Z" />
                  </svg>
                <div class="mt-2 space-y-1 ">
                  <h2 class="px-1 text-xl font-semibold text-accent1">Struktur Organisasi</h2>
                  <h3 class="px-1 text-base font-normal  text-accent1 leading-5">Susunan kepengurusan dan perangkat desa
                    yang
                    bertugas melayani masyarakat.</h3>
                </div>
              </div>
            </a>
          </div>
        </article>
      </div>
    </main>
  </UserLayout>
</template>

<style scoped></style>
