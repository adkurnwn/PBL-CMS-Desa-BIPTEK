<template>
  <div class="relative w-full ">
      <div class="flip-card" :class="{ 'is-flipped': isFlipped }">
          <div class="flip-card-inner">
              <!-- Front of card -->
              <div class="flip-card-front bg-accent2 rounded-lg shadow-2xl">
                  <div class="px-7 md:px-9 py-7">
                      <div class="flex flex-col">
                          <h2 class="text-2xl md:text-3xl font-semibold text-secondary">{{ formattedDate }}</h2>
                          <h2 class="text-2xl md:text-3xl font-semibold text-secondary">{{ year }}</h2>
                          <h3 class="text-lg md:text-xl font-semibold text-white line-clamp-2 items-center justify-center mt-4 mb-2">{{ event.title }}</h3>
                      </div>
                      <div class="flex flex-col justify-between">
                          <div class="flex flex-col text-lg space-y-1">
                              <div class="flex flex-row space-x-4">
                                  <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="currentColor" class="text-secondary md:h-[24px] md:w-[24px]">
                                      <path d="M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z"/>
                                  </svg>
                                  <h2 class="text-xs md:text-sm text-type1 font-extralight mt-0.5">{{ event.location }}</h2>
                              </div>
                              <div class="flex flex-row space-x-4">
                                  <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="currentColor" class="text-secondary md:h-[24px] md:w-[24px]">
                                      <path d="M480-80q-75 0-140.5-28.5t-114-77q-48.5-48.5-77-114T120-440q0-75 28.5-140.5t77-114q48.5-48.5 114-77T480-800q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-440q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-80Zm0-360Zm112 168 56-56-128-128v-184h-80v216l152 152ZM224-866l56 56-170 170-56-56 170-170Zm512 0 170 170-56 56-170-170 56-56ZM480-160q117 0 198.5-81.5T760-440q0-117-81.5-198.5T480-720q-117 0-198.5 81.5T200-440q0 117 81.5 198.5T480-160Z"/>
                                  </svg>
                                  <h2 class="text-xs md:text-sm text-type1 font-extralight mt-0.5">{{ formatTime }}</h2>
                              </div>
                          </div>
                      </div>
                      <div class="lg:absolute md:bottom-0 md:right-0 flex flex-row space-x-4 md:p-3 px-4 hover:scale-105 transition-transform">
                          <button @click="flipCard" class="bg-accent1 text-white text-sm mx-auto px-2 py-2 mt-4 rounded-md">
                              Atur Pengingat
                          </button>
                      </div>
                  </div>
              </div>

              <!-- Back of card -->
              <div class="flip-card-back bg-accent2 rounded-lg shadow-2xl">
                  <div class="px-7 md:px-9 py-7 h-full flex flex-col">
                      <h3 class="text-xl font-semibold text-secondary mb-4">Atur Email Pengingat</h3>
                      
                      <!-- Description section with inline button -->
                      <div class="flex-grow">
                          <div class="flex items-start gap-2">
                              <p class="text-white text-sm text-justify" :class="descriptionClass">
                                  {{ event.description }}
                              </p>
                              <button 
                                  @click="toggleDescription"
                                  class="text-accent1 text-sm hover:underline whitespace-nowrap"
                              >
                                  {{ showFullDescription ? 'Sembunyikan' : 'Lihat lebih' }}
                              </button>
                          </div>
                      </div>

                      <!-- Form section - fixed at bottom -->
                      <div class="mt-4" v-show="!showFullDescription">
                          <form @submit.prevent="submitEmail" class="space-y-4">
                              <div>
                                  <input 
                                      type="email" 
                                      v-model="email"
                                      placeholder="Masukkan email Anda"
                                      class="w-full px-3 py-2 rounded-lg bg-white text-accent2"
                                      required
                                  >
                              </div>
                              <div class="flex space-x-2">
                                  <button 
                                      type="submit" 
                                      class="bg-accent1 text-white text-sm px-8 py-2 rounded-lg"
                                      :class="{ 'opacity-50 cursor-not-allowed': isToday || loading }"
                                      :disabled="isToday || loading"
                                  >
                                      {{ loading ? 'Mengirim...' : (isToday ? 'Tidak Tersedia' : 'Kirim') }}
                                  </button>
                                  <button 
                                      type="button"
                                      @click="flipCard"
                                      class="bg-secondary text-white text-sm px-8 py-2 rounded-lg"
                                  >
                                      Batalkan
                                  </button>
                              </div>
                          </form>
                      </div>

                      <!-- Message section - fixed at bottom -->
                      <div class="mt-4">
                          <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>
                          <p v-if="success" class="text-green-500 text-sm">{{ success }}</p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  event: {
      type: Object,
      required: true
  }
});

const formattedDate = computed(() => {
  const date = new Date(props.event.date);
  const day = date.toLocaleDateString('id-ID', { weekday: 'short' });
  const dayOfMonth = date.getDate();
  const month = date.toLocaleDateString('id-ID', { month: 'short' });
  return `${day}, ${dayOfMonth} ${month}`;
});

const year = computed(() => {
  return new Date(props.event.date).getFullYear();
});

const formatTime = computed(() => {
  const time = props.event.time;
  if (!time) return '';
  return time.substring(0, 5) + ' - Selesai'; // Convert "13:00:00" to "13:00 - Selesai"
});

const isFlipped = ref(false);
const email = ref('');
const loading = ref(false);
const error = ref('');
const success = ref('');

const flipCard = () => {
  isFlipped.value = !isFlipped.value;
  error.value = '';
  success.value = '';
  email.value = '';
};

const isToday = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return props.event.date === today;
});

const submitEmail = async () => {
  if (isToday.value) return;
  
  loading.value = true;
  error.value = '';
  success.value = '';
  
  try {
      await axios.post(`/api/events/${props.event.id}/reminder`, {
          email: email.value
      });
      success.value = 'Email pengingat berhasil diatur!';
      setTimeout(() => {
          flipCard();
      }, 2000);
  } catch (e) {
      error.value = e.response?.data?.message || 'Terjadi kesalahan';
  } finally {
      loading.value = false;
  }
};

const showFullDescription = ref(false);
const descriptionClass = computed(() => ({
  'line-clamp-1': !showFullDescription.value
}));

const toggleDescription = () => {
  showFullDescription.value = !showFullDescription.value;
};
</script>

<style scoped>
.flip-card {
  perspective: 1000px;
  height: 100%;
  min-height: 250px;
}

.flip-card-inner {
  position: relative;
  width: 100%;
  height: 100%;
  text-align: left;
  transition: transform 0.6s;
  transform-style: preserve-3d;
}

.flip-card.is-flipped .flip-card-inner {
  transform: rotateY(180deg);
}

.flip-card-front,
.flip-card-back {
  position: absolute;
  width: 100%;
  height: 100%;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  border-radius: 0.5rem;
}

.flip-card-back {
  transform: rotateY(180deg);
}

.cursor-not-allowed {
  cursor: not-allowed;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.flex-grow {
  flex-grow: 1;
}
</style>