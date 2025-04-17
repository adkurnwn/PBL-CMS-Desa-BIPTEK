<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
const uploadStatus = ref(''); // '', 'success', 'error'
const uploadMessage = ref('');

const showPopup = ref(false)

const togglePopup = () => {
  showPopup.value = !showPopup.value
}

const closePopup = () => {
  showPopup.value = false
}

// Form Data
const form = ref({
  nama: '',
  email: '',
  kategori: '',
  judul: '',
  isi: '',
  file: null,
})

function handleFileUpload(event) {
  const file = event.target.files[0];
  if (file) {
    // Validasi file
    const validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    const fileExtension = file.name.split('.').pop().toLowerCase();
    if (!validExtensions.includes(fileExtension)) {
      uploadStatus.value = 'error';
      uploadMessage.value = 'Format file tidak valid. Hanya JPG, JPEG, PNG, dan PDF yang diperbolehkan.';
      form.value.file = null;
      return;
    }
    if (file.size > 1 * 1024 * 1024) { // Limit 1MB
      uploadStatus.value = 'error';
      uploadMessage.value = 'Ukuran file terlalu besar (maks 1MB).';
      form.value.file = null;
      return;
    } else {
    uploadStatus.value = 'error'
    uploadMessage.value = 'Gagal mengunggah file.'
  }
    
    uploadStatus.value = 'success';
    uploadMessage.value = 'File berhasil diunggah!';
    form.value.file = file;
  }
}


// Submit logic (bisa diganti kirim ke backend)
const submitForm = () => {
  console.log('Form submitted:', form.value)
  showPopup.value = false
}

// ESC key to close popup
const handleKeyDown = (e) => {
  if (e.key === 'Escape') {
    closePopup()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeyDown)
})
</script>

<template>
  <!-- Overlay + Popup -->
  <div v-if="showPopup" class="fixed inset-0 z-40" @click.self="closePopup">
  <!-- Transitioned Form -->
  <Transition name="fade-slide">
    <div
      v-show="showPopup"
      class="absolute bottom-20 right-4 bg-white shadow-xl p-4 rounded-lg w-[90%] max-w-sm space-y-3 z-50"
    >
      <h2 class="text-lg font-semibold text-type2">Keluhan dan Saran</h2>
      <form @submit.prevent="submitForm" class="flex flex-col gap-2">
        <input v-model="form.nama" type="text" placeholder="Nama Pengirim" class="border p-2 rounded text-sm" />
        <input v-model="form.email" type="email" placeholder="Email" class="flex border p-2 rounded text-sm" />
        <div class="flex flex-col md:flex-row gap-2">
          <input v-model="form.judul" type="text" placeholder="Judul" class="border p-2 rounded text-sm" />
          <select v-model="form.kategori" class="flex w-full border p-2 rounded text-sm">
            <option value="" disabled selected>Pilih Kategori</option>
            <option value="keluhan">Keluhan</option>
            <option value="saran">Saran</option>
          </select>
        </div>

        
        <textarea v-model="form.isi" placeholder="Isi Pengaduan" rows="3" class="border p-2 rounded text-sm resize-none"></textarea>
        <div class="relative w-full">
          <div class="flex items-center gap-3 flex-wrap">
        <!-- Tombol Upload -->
        <label
          for="fileUpload"
          class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 border-2 text-accent1 rounded cursor-pointer hover:bg-opacity-90 transition"
        >
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor">
            <path d="M440-200h80v-167l64 64 56-57-160-160-160 160 57 56 63-63v167ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/>
          </svg>
          <span>Pilih File</span>
        </label>

        <!-- Input File (disembunyikan) -->
        <input
          type="file"
          id="fileUpload"
          @change="handleFileUpload"
          accept=".jpg,.jpeg,.png,.pdf"
          class="hidden"
        />

        <!-- Nama File di Sebelah Kanan -->
        <p v-if="form.file" class="text-sm text-gray-700">
          📎 <strong>{{ form.file.name }}</strong>
        </p>
      </div>

    </div>

    <!-- Status Upload -->
    <Transition name="fade">
      <div
        v-if="uploadStatus"
        :class="{
          'text-green-600 bg-green-100 border border-green-300': uploadStatus === 'success',
          'text-red-600 bg-red-100 border border-red-300': uploadStatus === 'error',
        }"
        class="mt-2 p-2 rounded text-sm"
      >
        {{ uploadMessage }}
      </div>
      </Transition>
          <button type="submit" class="bg-secondary text-white py-2 px-4 rounded hover:bg-opacity-80">
            Kirim
          </button>
        </form>
      </div>
    </Transition>
  </div>

  <!-- Floating Button -->
  <button
  @click="togglePopup" class="fixed bottom-4 right-4 z-50 bg-secondary opacity-90 rounded-full shadow-lg flex justify-center items-center w-14 h-14"
  >
    <button  class="text-sm text-black opacity-100">
      <img src="../../images/3p.png" alt="Buka Form" />
    </button>
  </button>
</template>

<style scoped>
/* Animasi fade-in dan fade-out yang elegan */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.5s ease;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

</style>