<template>
    <UserLayout>
        <!-- Event Terdekat-->
      <article class="w-full px-8 sm:px-20 lg:px-32 flex flex-col py-4 md:py-10 bg-type1 font-merriweather">
        <h1 class="text-xl md:text-3xl font-semibold text-accent2">
          Event Terdekat
        </h1>
        <h2 class="text-sm md:text-xl text-black my-2 md:my-4 mb-4">
          <p class="italic">Informasi tentang event atau kegiatan terdekat Desa</p>
        </h2>
        <div v-if="loading" class="flex justify-center">
          <p>Loading events...</p>
        </div>
        <div v-else-if="events.length === 0" class="flex justify-center">
          <p>No upcoming events</p>
        </div>
        <div v-else class="flex flex-col md:flex-row justify-center lg:space-x-4 space-y-2 md:space-y-0">
          <EventCardTest 
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
import EventCardTest from '@/Components/EventCard.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const events = ref([]);
const loading = ref(true);

const fetchEvents = async () => {
    try {
        const response = await axios.get('/api/events');
        events.value = response.data;
    } catch (error) {
        console.error('Error fetching events:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchEvents();
});
</script>

