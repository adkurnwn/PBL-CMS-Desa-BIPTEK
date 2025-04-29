<script setup>
import EventCard from "@/Components/EventCard.vue";
import NewsCard from "@/Components/NewsCard.vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import { ref, onMounted } from "vue";

const informasi = ref({});
const loading = ref(false);

const fetchInformasi = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/api/informasi");
        informasi.value = response.data;
        loading.value = false;
    } catch (error) {
        console.error("Error fetching informasi data:", error);
        loading.value = false;
    }
};

onMounted(() => {
    fetchInformasi();
});
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
                <div class="flex flex-col w-full justify-between space-y-4">
                    <div
                        class="flex flex-row w-full h-full justify-between space-x-2"
                    >
                        <img
                            v-if="informasi.gallery"
                            :src="`/storage/${informasi.gallery.images[0].path}`"
                            alt=""
                            class="w-3/5 h-[150px] sm:h-[225px] md:h-[400px] rounded-3xl object-cover"
                        />
                        <img
                            v-if="informasi.gallery"
                            :src="`/storage/${informasi.gallery.images[1].path}`"
                            alt=""
                            class="w-2/5 h-[150px] sm:h-[225px] md:h-[400px] rounded-3xl object-cover"
                        />
                    </div>
                    <div
                        class="flex flex-col sm:flex-row w-full justify-between space-y-3 sm:space-y-0 sm:-m-1"
                    >
                        <img
                            v-if="informasi.gallery"
                            :src="`/storage/${informasi.gallery.images[2].path}`"
                            alt=""
                            class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-3xl object-cover sm:mr-2"
                        />
                        <img
                            v-if="informasi.gallery"
                            :src="`/storage/${informasi.gallery.images[3].path}`"
                            alt=""
                            class="w-full sm:w-1/3 h-[200px] md:h-[300px] rounded-3xl object-cover sm:mr-2"
                        />
                        <img
                            v-if="informasi.gallery"
                            :src="`/storage/${informasi.gallery.images[4].path}`"
                            alt=""
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
                    <div v-else-if="events.length === 0" class="text-center w-full">
                        <p>Tidak ada event yang akan datang</p>
                    </div>
                    <template v-else>
                        <EventCard
                        v-for="eventItem in informasi.events"
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
                        v-for="newsItem in informasi.news"
                        :key="newsItem.id"
                        :isHome="true"
                        :news="newsItem"
                    />
                </div>
                <a
                    href="/berita-test"
                    class="text-lg md:text-xl font-semibold text-secondary my-4 ml-2 text-right justify-end"
                >
                    Selengkapnya >>
                </a>
            </article>
        </main>
    </UserLayout>
</template>

<style scoped></style>
