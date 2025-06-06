<script setup>
import EventCard from "@/Components/EventCard.vue";
import NewsCard from "@/Components/NewsCard.vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import { ref, onMounted } from "vue";
import useApiFetch from "@/Utility/useApiFetch";
import { Link } from "@inertiajs/vue3";

const gallery = ref({});
const events = ref([]);
const news = ref([]);

const fetchData = async () => {
    const galleryLoading = ref(true);
    const eventLoading = ref(true);
    const newsLoading = ref(true);

    const galleryApi = useApiFetch();
    const eventsApi = useApiFetch();
    const newsApi = useApiFetch();

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
    <UserLayout>
        <header
            class="relative flex flex-col items-center justify-center max-w-fit mx-auto w-full bg-type1"
        >
            <!-- Galeri Desa -->
            <article
                class="w-full px-8 flex flex-col pt-32 4xl:pt-44 pb-8 xl:pb-16 bg-type1 font-merriweather"
            >
                <div
                    v-if="galleryLoading"
                    class="flex justify-center items-center h-[500px]"
                >
                    <p>Memuat galeri...</p>
                </div>

                <div
                    v-else-if="!gallery.images || gallery.images.length < 5"
                    class="flex justify-center items-center h-[500px]"
                >
                    <p>Gallery images not available</p>
                </div>

                <div
                    v-else
                    class="flex flex-col w-full justify-between space-y-4"
                >
                    <div
                        class="flex flex-row w-full h-full justify-between space-x-2"
                    >
                        <img
                            :src="`/storage/${gallery.images[0].path}`"
                            :alt="gallery.images[0].alt"
                            class="w-3/5 h-[150px] sm:h-[225px] md:h-[400px] rounded-3xl object-cover"
                        />
                        <img
                            :src="`/storage/${gallery.images[1].path}`"
                            :alt="gallery.images[1].alt"
                            class="w-2/5 h-[150px] sm:h-[225px] md:h-[400px] rounded-3xl object-cover"
                        />
                    </div>
                    <div
                        class="flex flex-col sm:flex-row w-full justify-between space-y-3 sm:space-y-0 sm:-m-1"
                    >
                        <img
                            :src="`/storage/${gallery.images[2].path}`"
                            :alt="gallery.images[2].alt"
                            class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-3xl object-cover sm:mr-2"
                        />
                        <img
                            :src="`/storage/${gallery.images[3].path}`"
                            :alt="gallery.images[3].alt"
                            class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-3xl object-cover sm:mr-2"
                        />
                        <img
                            :src="`/storage/${gallery.images[4].path}`"
                            :alt="gallery.images[4].alt"
                            class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-3xl object-cover"
                        />
                    </div>
                </div>
            </article>
        </header>

        <main class="max-w-7xl mx-auto w-full">
            <!-- Event Terdekat-->
            <article
                class="w-full px-8 sm:px-10 lg:px-8 flex flex-col py-6  bg-type1 font-merriweather"
            >
                <h1 class="text-xl md:text-3xl font-semibold text-accent2">
                    Event Terdekat
                </h1>
                <h2 class="text-sm md:text-xl text-black my-2 md:my-4 mb-4">
                    <p class="italic">
                        Informasi tentang event atau kegiatan terdekat Desa
                    </p>
                </h2>
                <div
                    class="flex flex-col sm:flex-row justify-center  min-h-full md:space-y-0 gap-3"
                >
                    <div v-if="eventLoading" class="text-center w-full">
                        <p>Memuat events...</p>
                    </div>
                    <div
                        v-else-if="events.length === 0"
                        class="text-center w-full"
                    >
                        <p>Tidak ada event yang akan datang</p>
                    </div>
                    <template v-else>
                        <EventCard
                            v-for="eventItem in events.slice(0, 3)"
                            :key="eventItem.id"
                            :isHome="true"
                            :event="eventItem"
                        />
                    </template>
                </div>
                <Link href="/event" class="text-lg md:text-xl xl:text-2xl font-semibold text-secondary hover:text-secondary/75 transition-colors duration-500 my-6 xl:my-8ml-2 text-right justify-end">
                    Selengkapnya &gt;&gt;
                </Link>
            </article>

            <!-- Berita Terbaru-->
            <article
                class="w-full px-8 sm:px-10 lg:px-8 flex flex-col xl:py-4 mb-10 bg-type1 font-merriweather"
            >
                <h1
                    class="text-xl md:text-3xl font-semibold text-accent2 mb-3 ml-2"
                >
                    Berita Terbaru
                </h1>
                <div
                    class="flex flex-col sm:grid sm:grid-cols-2 lg:grid-cols-3 justify-center gap-4 xl:gap-2"
                >
                    <div v-if="newsLoading" class="text-center w-full">
                        <p>Memuat berita...</p>
                    </div>
                    <div
                        v-else-if="news.length === 0"
                        class="text-center w-full"
                    >
                        <p>Tidak ada berita terbaru</p>
                    </div>
                    <NewsCard
                        v-else
                        v-for="newsItem in news"
                        :href="`/berita/${newsItem.slug}`"
                        :key="newsItem.id"
                        :news="newsItem"
                    />
                </div>
                <a
                    href="/berita"
                    class="text-lg md:text-xl font-semibold text-secondary my-4 ml-2 text-right justify-end"
                >
                    Selengkapnya >>
                </a>
            </article>
        </main>
    </UserLayout>
</template>
