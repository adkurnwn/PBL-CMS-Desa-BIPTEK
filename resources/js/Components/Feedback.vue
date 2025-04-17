<template>
  <div>
    <!-- Overlay + Popup -->
    <div v-if="showPopup" class="fixed inset-0 z-40" @click.self="closePopup">
      <!-- Transitioned Form -->
      <Transition name="fade-slide">
        <div
          v-show="showPopup"
          class="absolute bottom-20 right-4 bg-white shadow-xl p-4 rounded-lg w-[90%] max-w-sm space-y-3 z-50"
        >
          <h2 class="text-lg font-semibold text-type2">Form Pengaduan</h2>
          <form @submit.prevent="submitForm" class="flex flex-col gap-2">
            <input 
              v-model="form.judul" 
              type="text" 
              placeholder="Judul" 
              class="border p-2 rounded text-sm" 
              required
            />
            <input 
              v-model="form.nama_user" 
              type="text" 
              placeholder="Nama" 
              class="border p-2 rounded text-sm" 
              required
            />
            <input 
              v-model="form.email" 
              type="email" 
              placeholder="Email" 
              class="border p-2 rounded text-sm" 
              required
            />
            <select 
              v-model="form.kategori" 
              class="border p-2 rounded text-sm"
              required
            >
              <option value="" disabled selected>Pilih Kategori</option>
              <option 
                v-for="kategori in kategoriOptions" 
                :key="kategori" 
                :value="kategori"
              >
                {{ kategori }}
              </option>
            </select>
            <textarea 
              v-model="form.isi" 
              placeholder="Isi Pengaduan" 
              rows="3" 
              class="border p-2 rounded text-sm resize-none"
              required
            ></textarea>
            <button 
              type="submit" 
              class="bg-secondary text-white py-2 px-4 rounded hover:bg-opacity-80"
              :disabled="loading"
            >
              {{ loading ? 'Mengirim...' : 'Kirim' }}
            </button>
          </form>
        </div>
      </Transition>
    </div>

    <!-- Floating Button -->
    <button
      @click="togglePopup" 
      class="fixed bottom-4 right-4 z-50 bg-secondary opacity-90 rounded-full shadow-lg flex justify-center items-center w-14 h-14"
    >
      <button class="text-sm text-black opacity-100">
        <img src="../../images/3p.png" alt="Buka Form" />
      </button>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'

const showPopup = ref(false)
const kategoriOptions = ref([])
const loading = ref(false)

const togglePopup = () => {
  showPopup.value = !showPopup.value
}

const closePopup = () => {
  showPopup.value = false
}

const form = ref({
  judul: '',
  nama_user: '',
  email: '',
  kategori: '',
  isi: ''
})

const fetchKategori = async () => {
  try {
    const response = await axios.get('/api/feedback/kategori')
    kategoriOptions.value = response.data
  } catch (error) {
    console.error('Error fetching kategori:', error)
  }
}

const submitForm = async () => {
  try {
    loading.value = true
    const response = await axios.post('/api/feedback', form.value)
    alert(response.data.message)
    form.value = {
      judul: '',
      nama_user: '',
      email: '',
      kategori: '',
      isi: ''
    }
    showPopup.value = false
  } catch (error) {
    alert(error.response?.data?.message || 'Terjadi kesalahan')
  } finally {
    loading.value = false
  }
}

const handleKeyDown = (e) => {
  if (e.key === 'Escape') {
    closePopup()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown)
  fetchKategori()
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeyDown)
})
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.5s ease;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
}
</style>