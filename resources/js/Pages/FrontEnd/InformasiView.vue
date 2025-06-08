<script setup>
import EventCard from "@/Components/EventCard.vue";
import NewsCard from "@/Components/NewsCard.vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import { ref, onMounted } from "vue";
import useApiFetch from "@/Utility/useApiFetch";
import { useMeta } from "@/Utility/useMeta";
import { Link, Head } from "@inertiajs/vue3";

const gallery = ref({});
const events = ref([]);
const news = ref([]);
const galleryTimeout = ref(false);

// Meta tags setup
const {
    metaTitle,
    metaDescription,
    metaImage,
    canonicalUrl,
    metaType,
    metaSiteName
} = useMeta({
    title: 'Informasi & Berita',
    description: 'Pusat informasi terkini tentang galeri desa, event terdekat, dan berita terbaru dari pemerintah desa.',
    type: 'website',
    url: `${window.location.origin}/informasi`,
    image: 'default-info-image.jpg'
});

const fetchData = async () => {
    const galleryLoading = ref(true);
    const eventLoading = ref(true);
    const newsLoading = ref(true);

    const galleryApi = useApiFetch();
    const eventsApi = useApiFetch();
    const newsApi = useApiFetch();

    // Set timeout for gallery loading
    setTimeout(() => {
        galleryTimeout.value = true;
    }, 10000);

    await Promise.all([
        galleryApi.fetchData("/api/gallery/highlight"),
        eventsApi.fetchData("/api/events/preview"),
        newsApi.fetchData("/api/news/preview?limit=6"),
    ]);
    gallery.value = galleryApi.data.value || {};
    events.value = eventsApi.data.value || [];
    news.value = newsApi.data.value || [];
    galleryLoading.value = galleryApi.loading.value;
    eventLoading.value = eventsApi.loading.value;
    newsLoading.value = newsApi.loading.value;
};

onMounted(fetchData);
</script>

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

    <UserLayout>
        <header class="max-w-screen-3xl mx-auto w-full bg-type1">
            <!-- Galeri Desa -->
            <article class="px-[6.56%] w-full flex flex-col pt-32 4xl:pt-44 pb-8 xl:pb-16 bg-type1 font-merriweather">
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
        </header>

        <main class="max-w-screen-3xl mx-auto w-full bg-type1 px-[6.56%]">
            <!-- Event Terdekat-->
            <article class="w-full flex flex-col py-6  bg-type1 font-merriweather">
                <h1 class="text-xl md:text-3xl font-semibold text-accent2">
                    Event Terdekat
                </h1>
                <h2 class="text-sm md:text-xl text-black my-2 md:my-4 mb-4">
                    <p class="italic">
                        Informasi tentang event atau kegiatan terdekat Desa
                    </p>
                </h2>
                <div
                    class="flex flex-col sm:grid sm:grid-cols-2 xl:flex xl:flex-row justify-center  min-h-full md:space-y-0 gap-3">
                    <div v-if="eventLoading" class="text-center w-full">
                        <p>Memuat events...</p>
                    </div>
                    <div v-else-if="events.length === 0" class="text-center w-full">
                        <p>Tidak ada event yang akan datang</p>
                    </div>
                    <template v-else>
                        <EventCard v-for="eventItem in events.slice(0, 3)" :key="eventItem.id" :isHome="true"
                            :event="eventItem" />
                    </template>
                </div>
                <a href="/event"
                    class="text-lg md:text-xl font-semibold text-secondary my-4 ml-2 text-right justify-end">
                    Selengkapnya >>
                </a>
            </article>

            <!-- Berita Terbaru-->
            <article class="w-full flex flex-col xl:py-4 mb-10 bg-type1 font-merriweather">
                <h1 class="text-xl md:text-3xl font-semibold text-accent2 mb-3">
                    Berita Terbaru
                </h1>
                <div class="flex flex-col sm:grid sm:grid-cols-2 lg:grid-cols-3 justify-center gap-4 xl:gap-2">
                    <div v-if="newsLoading" class="text-center w-full">
                        <p>Memuat berita...</p>
                    </div>
                    <div v-else-if="news.length === 0" class="text-center w-full">
                        <p>Tidak ada berita terbaru</p>
                    </div>
                    <NewsCard v-else v-for="newsItem in news" :href="`/berita/${newsItem.slug}`" :key="newsItem.id"
                        :news="newsItem" />
                </div>
                <a href="/berita"
                    class="text-lg md:text-xl font-semibold text-secondary my-4 ml-2 text-right justify-end">
                    Selengkapnya >>
                </a>
            </article>
        </main>
    </UserLayout>
</template>
