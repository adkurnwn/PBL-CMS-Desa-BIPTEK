<template>
    <Head title="Agenda Desa" />
    <UserLayout>
    <main>
        <!-- Event Terdekat-->
      <article class="w-full mx-auto max-w-screen-4xl px-[6.56%] flex flex-col py-4 mt-20 md:py-10 bg-type1 font-merriweather">
        <h1 class="flex text-xl md:text-3xl font-semibold text-accent2 justify-center mt-8">
          Agenda Desa
        </h1>
        <!-- Search Bar -->
        <div class="relative flex w-full flex-grow items-center justify-center max-w-7xl mx-auto mb-4">
            <div class="flex-grow mt-6 xl:max-w-2xl sm:max-w-md">
                <Search 
                    v-model="searchQuery"
                    type="with-filter"
                    @search="handleSearch"
                    @filter="openFilter"
                />
            </div>
        </div>
        <div v-if="loading" class="flex justify-center">
          <p>Loading events...</p>
        </div>
        <div v-else-if="events.length === 0" class="flex justify-center">
          <p>No upcoming events</p>
        </div>
        <div v-else class="md:flex flex-wrap lg:grid grid-cols-3 justify-center gap-4 mt-4 space-y-4 lg:space-y-0">
          <EventCard 
            v-for="event in events" 
            :key="event.id"
            :event="event"
          />
        </div>
      </article>
      </main>
    </UserLayout>
</template>
  
<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import EventCard from '@/Components/EventCard.vue';
import Search from '@/Components/FrontendComponent/Search.vue';
import { ref, onMounted } from 'vue';
import useApiFetch from '@/Utility/useApiFetch';
import { Head } from '@inertiajs/vue3';

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

const handleSearch = (query) => {
    searchQuery.value = query;
    fetchData({ search: query });
};

onMounted(() => fetchData());
</script>

