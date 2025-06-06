<template>
    <UserLayout>
        <!-- Event Terdekat-->
      <article class="w-full mx-auto max-w-screen-4xl px-8 sm:px-12 lg:px-40 flex flex-col py-4 mt-20 md:py-10 bg-type1 font-merriweather">
        <h1 class="flex text-xl md:text-3xl font-semibold text-accent2 justify-center mt-8">
          Agenda Desa
        </h1>
        <!-- Search Bar -->
        <div class="relative flex justify-center items-center max-w-7xl w-full mx-auto mb-4">
            <div class="relative w-full sm:max-w-md xl:max-w-2xl">
                <!-- Search Input -->
                <input
                type="text"
                v-model="searchQuery"
                placeholder="Cari Informasi atau Kegiatan..."
                class="w-full pl-4 pr-12 py-2 mt-6 border-2 border-accent1 bg-type1 shadow-lg rounded-full italic font-light"
                />

                <!-- Search Icon Button Inside Input -->
                <button
                class="absolute right-4 top-11 transform -translate-y-1/2 mt-0.5 text-accent1"
                @click="handleSearch"
                >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    height="24px"
                    viewBox="0 -960 960 960"
                    width="24px"
                    fill="244031"
                >
                    <path
                    d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"
                    />
                </svg>
                </button>
            </div>

            <!-- Filter Button remains outside -->
            <button
                @click="openFilter"
                class="mt-6 ml-4 w-11 h-11 border-2 border-accent1 shadow-lg rounded-full text-accent1 hover:text-white transition"
            >
                <svg
                class="ml-2"
                xmlns="http://www.w3.org/2000/svg"
                height="24px"
                viewBox="0 0 960 960"
                width="24px"
                fill="244031"
                >
                <path
                    d="M520-600v-80h120v-160h80v160h120v80H520Zm120 480v-400h80v400h-80Zm-400 0v-160H120v-80h320v80H320v160h-80Zm0-320v-400h80v400h-80Z"
                />
                </svg>
            </button>
            </div>

        <div v-if="loading" class="flex justify-center">
        <p>Loading events...</p>
        </div>
        <div v-else-if="events.length === 0" class="flex justify-center">
        <p>No upcoming events</p>
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 justify-center gap-4 mt-4">
        <EventCard 
            v-for="event in events" 
            :key="event.id"
            :event="event"
        />
        </div>
    </article>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import EventCard from '@/Components/EventCard.vue';
import { ref, onMounted } from 'vue';
import useApiFetch from '@/Utility/useApiFetch';

const events = ref([]);
const loading = ref(true);
const searchQuery = ref('');

const fetchData = async ({ search = "" } = {}) => {
    loading.value = true;
    const eventsApi = useApiFetch();
    const params = new URLSearchParams();
    if (search) params.append("search", search);
    await eventsApi.fetchData(`/api/events?${params.toString()}`);
    events.value = eventsApi.data.value || [];
    loading.value = eventsApi.loading.value;
};

const handleSearch = () => {
    fetchData({ search: searchQuery.value });
};

onMounted(() => fetchData());
</script>

