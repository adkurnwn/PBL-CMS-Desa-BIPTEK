<script setup>
import EventCard from "@/Components/EventCard.vue";
import NewsCard from "@/Components/NewsCard.vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import { ref, onMounted } from "vue";
import { useApiCache } from "@/Composables/UseApiCache";

const gallery = ref({});
const events = ref([]);
const news = ref([]);
const loading = ref(true);
const fetchData = async () => {
    const galleryApi = useApiCache("galleryData", "/api/gallery/highlight");
    const eventsApi = useApiCache("eventsData", "/api/events/preview");
    const newsApi = useApiCache("newsData", "/api/news/preview?limit=6");
    try {
        const [galleryRes, eventsRes, newsRes] = await Promise.all([
            galleryApi.fetchWithCache(),
            eventsApi.fetchWithCache(),
            newsApi.fetchWithCache(),
        ]);
        gallery.value = galleryRes;
        events.value = eventsRes;
        news.value = newsRes;
    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
    }
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
                class="w-full px-8 flex flex-col pt-32 pb-8 lg:pb-24 bg-type1 font-merriweather"
            >
                <div
                    v-if="loading"
                    class="flex justify-center items-center h-[500px]"
                >
                    <p>Loading gallery...</p>
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
                class="w-full px-8 sm:px-14 lg:px-8 flex flex-col py-6 md:py-10 bg-type1 font-merriweather"
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
                    class="flex flex-col md:flex-row justify-center md:space-x-4 m-4 space-y-2 md:space-y-0"
                >
                    <div v-if="loading" class="text-center w-full">
                        <p>Loading events...</p>
                    </div>
                    <div
                        v-else-if="events.length === 0"
                        class="text-center w-full"
                    >
                        <p>Tidak ada event yang akan datang</p>
                    </div>
                    <template v-else>
                        <EventCard
                            v-for="eventItem in events"
                            :key="eventItem.id"
                            :isHome="true"
                            :event="eventItem"
                        />
                    </template>
                </div>
            </article>

            <!-- Berita Terbaru-->
            <article
                class="w-full px-8 sm:px-14 lg:px-8 flex flex-col py-6 md:py-10 bg-type1 font-merriweather"
            >
                <h1
                    class="text-xl md:text-3xl font-semibold text-accent2 my-3 ml-2"
                >
                    Berita Terbaru
                </h1>
                <div
                    class="flex flex-col sm:grid sm:grid-cols-2 lg:grid-cols-3 justify-center space-y-1 md:space-y-0"
                >
                    <NewsCard
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

<style scoped></style>
