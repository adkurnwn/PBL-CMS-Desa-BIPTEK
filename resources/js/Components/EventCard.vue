<template>
  <div class="relative w-full ">
    <div class="flip-card" :class="{ 'is-flipped': isFlipped, 'is-past': isPastEvent }">
      <div class="flip-card-inner ">
        <!-- Front of card -->
        <div class="flip-card-front bg-accent2 rounded-lg shadow-2xl" :class="{ 'past-event': isPastEvent }">
          <div class="px-7 xl:px-9 py-7">
            <div class="flex flex-col sm:flex-row xl:flex-col  ">
              <h2 class="text-2xl sm:text-xl xl:text-3xl font-semibold text-secondary"
                :class="{ 'text-gray-400': isPastEvent }">{{ formattedDate }}</h2>
              <h2 class="text-2xl sm:text-xl xl:text-3xl font-semibold text-secondary sm-0 sm:ml-1 xl:ml-0"
                :class="{ 'text-gray-400': isPastEvent }">{{ year }}</h2>

            </div>
            <div class="flex flex-col justify-between">
              <h3 class="text-xl font-semibold text-white line-clamp-2 items-center justify-center sm:mt-2 xl:mt-4 h-14"
                :class="{ 'text-gray-300': isPastEvent }">{{ event.title }}</h3>
              <div class="flex flex-col text-lg space-y-1">
                <div class="flex flex-row space-x-2 sm:mt-5 xl:mt-0">
                  <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960"
                    width="18px" fill="currentColor" class="text-secondary md:h-[24px] md:w-[24px]"
                    :class="{ 'text-gray-400': isPastEvent }">
                    <path
                      d="M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z" />
                  </svg>
                  <h2 class="text-xs sm:text-sm line-clamp-1 text-type1 font-extralight mt-0.5 "
                    :class="{ 'text-gray-500': isPastEvent }">{{ event.location }}</h2>
                </div>
                <div class="flex flex-row space-x-2">
                  <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960"
                    width="18px" fill="currentColor" class="text-secondary md:h-[24px] md:w-[24px]"
                    :class="{ 'text-gray-400': isPastEvent }">
                    <path
                      d="M480-80q-75 0-140.5-28.5t-114-77q-48.5-48.5-77-114T120-440q0-75 28.5-140.5t77-114q48.5-48.5 114-77T480-800q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-440q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-80Zm0-360Zm112 168 56-56-128-128v-184h-80v216l152 152ZM224-866l56 56-170 170-56-56 170-170Zm512 0 170 170-56 56-170-170 56-56ZM480-160q117 0 198.5-81.5T760-440q0-117-81.5-198.5T480-720q-117 0-198.5 81.5T200-440q0 117 81.5 198.5T480-160Z" />
                  </svg>
                  <h2 class="text-xs sm:text-sm text-type1 font-extralight mt-0.5"
                    :class="{ 'text-gray-500': isPastEvent }">{{ formatTime }}</h2>
                </div>
              </div>
            </div>
            <div class="absolute bottom-0 right-0 flex flex-row space-x-4 p-3 hover:scale-105 transition-transform">
              <button @click="flipCard" class="bg-accent1 text-white text-sm mx-auto px-2 py-2 mt-4 rounded-md"
                :class="{ 'bg-gray-700': isPastEvent }">
                {{ isPastEvent ? 'Event Berakhir' : 'Atur Pengingat' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Back of card -->
        <div class="flip-card-back bg-accent2 rounded-lg shadow-2xl" :class="{ 'past-event': isPastEvent }">
          <div class="px-7 md:px-9 py-7 h-full flex flex-col">
            <h3 class="text-xl font-semibold text-secondary mb-4" :class="{ 'text-gray-400': isPastEvent }">
              {{ isPastEvent ? 'Deskripsi Event' : 'Atur Email Pengingat' }}
            </h3>

            <!-- Description section with inline button -->
            <div class="flex-grow">
              <div class="flex flex-col h-full">
                <p ref="descriptionText" class="text-white text-sm text-justify flex-1 break-words"
                  :class="[{ 'line-clamp-1': !showFullDescription, 'line-clamp-4': showFullDescription, 'text-gray-300': isPastEvent }]">
                  {{ event.description || 'Tidak ada deskripsi tersedia.' }}
                </p>

                <button v-if="!isPastEvent && isDescriptionLong" @click="toggleDescription"
                  class="text-accent1 text-sm hover:underline mt-auto self-start">
                  {{ showFullDescription ? 'Sembunyikan' : 'Lihat lebih' }}
                </button>
              </div>
            </div>

            <!-- Form section - only show for future events -->
            <div v-if="!isPastEvent" class="mt-4" v-show="!showFullDescription">
              <form @submit.prevent="submitEmail" class="space-y-4">
                <div>
                  <input type="email" v-model="email" placeholder="Masukkan email Anda"
                    class="w-full px-3 py-2 rounded-lg bg-white text-accent2" required>
                </div>
                <div class="flex space-x-2">
                  <button type="submit" class="bg-accent1 text-white text-sm px-8 py-2 rounded-lg"
                    :class="{ 'opacity-50 cursor-not-allowed': isToday || loading }" :disabled="isToday || loading">
                    {{ loading ? 'Mengirim...' : (isToday ? 'Tidak Tersedia' : 'Kirim') }}
                  </button>
                  <button type="button" @click="flipCard" class="bg-secondary text-white text-sm px-8 py-2 rounded-lg">
                    Batalkan
                  </button>
                </div>
              </form>
            </div>

            <!-- Back button for past events -->
            <div v-if="isPastEvent" class="mt-4">
              <button type="button" @click="flipCard" class="bg-gray-700 text-white text-sm px-8 py-2 rounded-lg">
                Kembali
              </button>
            </div>

            <!-- Message section - fixed at bottom -->
            <div v-if="!isPastEvent" class="mt-4">
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
import { computed, ref, onMounted, nextTick } from 'vue';
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

const isPastEvent = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return props.event.date < today;
});

const submitEmail = async () => {
  if (isToday.value || isPastEvent.value) return;

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
const descriptionText = ref(null);
const showToggleButton = ref(false);

// Add a computed property to check if description is long enough to need the toggle button
const isDescriptionLong = computed(() => {
  return props.event.description && props.event.description.length > 50;
});

const toggleDescription = () => {
  showFullDescription.value = !showFullDescription.value;
};
</script>

<style scoped>
.flip-card {
  perspective: 1000px;
  min-height: 200px; /* Set a reasonable min-height for mobile */
  height: 100%;
  display: flex;
  flex-direction: column;
}

.flip-card-inner {
  position: relative;
  width: 100%;
  min-height: 200px; /* Match the min-height of .flip-card */
  height: 100%;
  text-align: left;
  transition: transform 0.6s;
  transform-style: preserve-3d;
  display: flex;
  flex-direction: column;
}


.flip-card.is-flipped .flip-card-inner {
  transform: rotateY(180deg);
}

.flip-card-front {
  position: absolute;
  width: 100%;

  height: 100%;
  min-height: 250px;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  border-radius: 0.5rem;
}

.flip-card-back {
  width: 100%;
  height: 100%;
  min-height: 250px;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  border-radius: 0.5rem;
}

.flip-card-back {
  width: 100%;
  height: 100%;
  min-height: 250px;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  border-radius: 0.5rem;
  top: 0;
  left: 0;
  display: flex;
  flex-direction: column;
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

.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.flex-grow {
  flex-grow: 1;
}

.flip-card.is-past {
  filter: grayscale(100%);
}

.past-event {
  background-color: #4a5568 !important;
}
</style>