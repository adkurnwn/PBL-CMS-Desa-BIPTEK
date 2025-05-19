<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { ref, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import NewsCard from "@/Components/NewsCard.vue";

const news = ref([]);
const loading = ref(true);
const error = ref(null);
const pagination = ref(null);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const fetchNews = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/api/news");
        news.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            total: response.data.total,
        };
    } catch (err) {
        error.value = "Gagal memuat berita";
        console.error("Error fetching news:", err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchNews();
});
</script>

<template>
    <UserLayout>
        <main
            class="flex flex-col items-center justify-center min-h-screen bg-type1 font-merriweather"
        >
            <!-- Judul -->
            <h1
                class="xl:text-2xl sm:text-xl text-md font-semibold text-accent2 mt-40"
            >
                Berita Desa
            </h1>
            <!-- Search Bar -->
            <div class="relative flex flex-grow items-center max-w-7xl mx-auto">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Cari Informasi atau Kegiatan..."
                    style="width: 800px"
                    class="flex-grow items-center mt-6 max-w-2xl py-2 mr-5 border-2 border-accent1 bg-type1 shadow-lg rounded-full italic font-ligh"
                />
                <!-- Button Ikon Search -->
                <button
                    class="absolute right-24 mt-6 transform text-accent1"
                    @click="handleSearch"
                >
                    <svg
                        class="-mr-2"
                        xmlns="http://www.w3.org/2000/svg"
                        height="24px"
                        viewBox="0 -960 960 960"
                        width="24px"
                        fill="#244031"
                    >
                        <path
                            d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"
                        />
                    </svg>
                </button>
                <!-- Button Filter Waktu Berita -->
                <button
                    @click="openFilter"
                    class="transform mt-6 w-11 h-11 border-2 border-accent1 shadow-lg rounded-full text-accent1 hover:text-white transition"
                >
                    <svg
                        class="ml-2"
                        xmlns="http://www.w3.org/2000/svg"
                        height="24px"
                        viewBox="http://www.w3.org/2000/svg"
                        width="24px"
                        fill="#244031"
                    >
                        <path
                            d="M520-600v-80h120v-160h80v160h120v80H520Zm120 480v-400h80v400h-80Zm-400 0v-160H120v-80h320v80H320v160h-80Zm0-320v-400h80v400h-80Z"
                        />
                    </svg>
                </button>
            </div>
            <!-- Kategori Berita -->
            <div
                class="flex relative items-center space-x-7 mt-7 overflow-x-auto text-lg text-accent1"
            >
                <button class="hover:underline font-medium">Semua</button>
                <button class="hover:underline">Populer</button>
                <button class="hover:underline">Wisata</button>
                <button class="hover:underline">Acara</button>
                <button class="hover:underline">UMKM</button>
                <button class="hover:underline">Pertanian</button>
                <button class="hover:underline">Kesehatan</button>
                <button class="hover:underline">Pendidikan</button>
                <button class="ml-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        height="24px"
                        viewBox="http://www.w3.org/2000/svg"
                        width="24px"
                        fill="#244031"
                    >
                        <path
                            d="M80-160v-160h160v160H80Zm240 0v-160h560v160H320ZM80-400v-160h160v160H80Zm240 0v-160h560v160H320ZM80-640v-160h160v160H80Zm240 0v-160h560v160H320Z"
                        />
                    </svg>
                </button>
            </div>
            <!--Berita Terkini-->
            <h1
                class="relative flex-grow items-center text-xl font-bold text-gray-800 self-start mb-1 mt-5 ml-24"
            >
                Berita Terkini
            </h1>
            <article>
                <!-- Loading State -->
                <div
                    v-if="loading"
                    class="flex justify-center items-center py-12"
                >
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"
                    ></div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="text-center py-12">
                    <p class="text-red-600">{{ error }}</p>
                </div>

                <!-- News Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6"
                >
                    <div v-for="item in news" :key="item.id" class="mb-6">
                        <NewsCard
                            :news="{
                                slug: item.slug,
                                title: item.title,
                                content: item.content,
                                created_at: item.created_at,
                                views: item.views,
                                comments_count: item.comments_count,
                                image: item.image,
                                category: item.category,
                                author: item.author,
                            }"
                            :href="`/berita-test/${item.slug}`"
                        />
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="news.length === 0"
                        class="col-span-full text-center py-12"
                    >
                        <p class="text-gray-500 text-lg">
                            Belum ada berita yang tersedia.
                        </p>
                    </div>
                </div>

                <!-- Pagination (if needed) -->
                <div
                    v-if="pagination && pagination.last_page > 1"
                    class="mt-8 flex justify-center"
                >
                    <!-- Add pagination controls here -->
                </div>
            </article>
        </main>
    </UserLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
