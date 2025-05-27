<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  photo_profile: null,
});

const loading = ref(false);
const errorMessage = ref("");
const previewUrl = ref(null);

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  
  // Validate file type
  const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
  if (!validTypes.includes(file.type)) {
    form.errors.photo_profile = 'Please select a valid image file (JPG, JPEG, or PNG)';
    return;
  }
  
  // Validate file size (max 2MB)
  const maxSize = 2 * 1024 * 1024; // 2MB in bytes
  if (file.size > maxSize) {
    form.errors.photo_profile = 'File size exceeds 2MB limit';
    return;
  }

  // Create preview URL
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
  }
  previewUrl.value = URL.createObjectURL(file);
  form.photo_profile = file;
};

const removePhoto = () => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
  }
  previewUrl.value = null;
  form.photo_profile = null;
};

const submit = () => {
  loading.value = true;
  errorMessage.value = "";
  
  form.post(route("install.setAdmin"), {
    onSuccess: () => {
      loading.value = false;
    },
    onError: (errors) => {
      loading.value = false;
      if (errors.error) {
        errorMessage.value = errors.error;
      }
    }
  });
};
</script>

<template>
  <div class="min-h-screen bg-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <h1 class="text-4xl font-extrabold text-accent1 tracking-tight mb-2">
          Buat Akun Administrator
        </h1>
        <h2 class="text-xl text-gray-600">
          Langkah 4/5: Buat pengguna admin untuk mengelola website
        </h2>
      </div>

      <!-- Content -->
      <div class="bg-white shadow overflow-hidden rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="text-center mb-8">
            <svg xmlns="http://www.w3.org/2000/svg" height="80" viewBox="0 -960 960 960" width="80" class="mx-auto text-accent1 fill-current">
              <path d="M400-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18h14q6 0 12 2-8 18-13.5 37.5T404-360h-4q-71 0-127.5 18T180-306q-9 5-14.5 14t-5.5 20v32h252q6 21 16 41.5t22 38.5H80Zm560 40-12-60q-12-5-22.5-10.5T584-204l-58 18-40-68 46-40q-2-14-2-26t2-26l-46-40 40-68 58 18q11-7 21.5-12.5T628-460l12-60h80l12 60q12 5 22.5 10.5T776-436l58-18 40 68-46 40q2 14 2 26t-2 26l46 40-40 68-58-18q-11 7-21.5 12.5T732-180l-12 60h-80Zm40-120q33 0 56.5-23.5T760-320q0-33-23.5-56.5T680-400q-33 0-56.5 23.5T600-320q0 33 23.5 56.5T680-240Z"/>
            </svg>
            <h3 class="mt-4 text-2xl font-medium text-gray-900">
              Akun Administrator
            </h3>
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <!-- Error message -->
            <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
              {{ errorMessage }}
            </div>

            <!-- Admin user form -->
            <div class="md:grid md:grid-cols-2 md:gap-6">
              <div class="space-y-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                  <div class="mt-1">
                    <input 
                      id="name" 
                      v-model="form.name" 
                      type="text" 
                      required 
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                      placeholder="Nama Admin"
                    />
                  </div>
                  <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                  <div class="mt-1">
                    <input 
                      id="email" 
                      v-model="form.email" 
                      type="email" 
                      required 
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                      placeholder="admin@example.com"
                    />
                  </div>
                  <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <div>
                  <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                  <div class="mt-1">
                    <input 
                      id="password" 
                      v-model="form.password" 
                      type="password" 
                      required 
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                      placeholder="Minimal 8 karakter"
                    />
                  </div>
                  <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <div>
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                  <div class="mt-1">
                    <input 
                      id="password_confirmation" 
                      v-model="form.password_confirmation" 
                      type="password" 
                      required 
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                      placeholder="Ulangi password"
                    />
                  </div>
                </div>
              </div>

              <div>
                <!-- Photo Profile Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Foto Profil</label>
                  <div 
                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md cursor-pointer hover:bg-gray-50"
                    @click="!previewUrl && document.getElementById('photo-upload').click()"
                  >
                    <div v-if="!previewUrl" class="space-y-1 text-center">
                      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label for="photo-upload" class="relative cursor-pointer rounded-md font-medium text-accent1 hover:text-accent1/80 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-accent1">
                          <span>Upload foto</span>
                          <input id="photo-upload" name="photo_profile" type="file" class="sr-only" @change="handleFileChange" accept="image/jpeg,image/png,image/jpg" />
                        </label>
                        <p class="pl-1">atau drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">PNG, JPG, JPEG hingga 2MB</p>
                    </div>
                    <div v-else class="relative w-full h-64">
                      <img :src="previewUrl" alt="Preview" class="w-full h-full object-cover rounded-md" />
                      <button 
                        type="button" 
                        @click.stop="removePhoto" 
                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 focus:outline-none"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  <p v-if="form.errors.photo_profile" class="mt-2 text-sm text-red-600">{{ form.errors.photo_profile }}</p>
                </div>
              </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-accent1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-gray-700">
                    Pengguna admin memiliki akses penuh untuk mengelola seluruh konten dan pengaturan website desa.
                  </p>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Footer/Actions -->
        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between">
          <Link
            href="/install/database"
            class="inline-flex justify-center rounded-md border border-gray-300 py-2 px-4 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent1 focus:ring-offset-2"
          >
            Kembali
          </Link>
          <button
            type="button"
            @click="submit"
            :disabled="loading || form.processing"
            class="inline-flex justify-center rounded-md border border-transparent bg-accent1 py-2 px-6 text-base font-medium text-white shadow-sm hover:bg-accent1/90 focus:outline-none focus:ring-2 focus:ring-accent1 focus:ring-offset-2 disabled:opacity-70"
          >
            <span v-if="loading || form.processing">
              <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Sedang Memproses...
            </span>
            <span v-else>Buat Admin & Lanjutkan</span>
          </button>
        </div>
      </div>

      <!-- Installation progress -->
      <div class="mt-8">
        <div class="flex justify-between">
          <div class="w-1/5 text-center">
            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold">1</div>
            <div class="text-xs mt-2 text-gray-600">Selamat Datang</div>
          </div>
          <div class="w-1/5 text-center">
            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold">2</div>
            <div class="text-xs mt-2 text-gray-600">Persyaratan</div>
          </div>
          <div class="w-1/5 text-center">
            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold">3</div>
            <div class="text-xs mt-2 text-gray-600">Database</div>
          </div>
          <div class="w-1/5 text-center">
            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold">4</div>
            <div class="text-xs mt-2 text-gray-600 font-bold">Admin</div>
          </div>
          <div class="w-1/5 text-center">
            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold">5</div>
            <div class="text-xs mt-2 text-gray-400">Selesai</div>
          </div>
        </div>
        <div class="relative flex items-center justify-center mt-2">
          <div class="h-1 w-full bg-gray-200">
            <div class="h-1 bg-accent1" style="width: 75%"></div>
          </div>
        </div>
      </div>

      <!-- Disclaimer -->
      <div class="text-center text-xs text-gray-500">
        <p>CMS Desa BIPTEK &copy; {{ new Date().getFullYear() }}</p>
        <p>Dibuat untuk membantu pembangunan digital desa di Indonesia</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.bg-accent1 {
  background-color: var(--color-accent1, #244031);
}

.text-accent1 {
  color: var(--color-accent1, #244031);
}

.hover\:bg-accent1\/90:hover {
  background-color: var(--color-accent1-90, rgba(36, 64, 49, 0.9));
}

.focus\:ring-accent1:focus {
  --tw-ring-color: var(--color-accent1, #244031);
}

.focus\:border-accent1:focus {
  border-color: var(--color-accent1, #244031);
}
</style>