<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
  currentConfig: Object
});

const form = useForm({
  host: props.currentConfig?.host || '127.0.0.1',
  port: props.currentConfig?.port || '3306',
  database: props.currentConfig?.database || '',
  username: props.currentConfig?.username || '',
  password: props.currentConfig?.password || '',
});

const loading = ref(false);
const errorMessage = ref("");
const testStatus = ref(null);

const isFormValid = computed(() => {
  return form.host && form.port && form.database && form.username;
});

const testConnection = () => {
  if (!isFormValid.value) return;
  
  loading.value = true;
  testStatus.value = 'testing';
  
  // Using the same form data but not submitting it yet
  const testData = {
    host: form.host,
    port: form.port,
    database: form.database,
    username: form.username,
    password: form.password
  };
  
  // Make a POST request to test the connection
  fetch(route("install.setDatabase"), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },
    body: JSON.stringify(testData)
  })
  .then(response => response.json())
  .then(data => {
    loading.value = false;
    if (data.errors) {
      testStatus.value = 'failed';
      errorMessage.value = data.errors.connection || data.errors.error || "Koneksi ke database gagal.";
    } else {
      testStatus.value = 'success';
    }
  })
  .catch(error => {
    loading.value = false;
    testStatus.value = 'failed';
    errorMessage.value = "Terjadi kesalahan saat menguji koneksi database.";
    console.error(error);
  });
};

const submit = () => {
  if (!isFormValid.value) return;
  
  loading.value = true;
  errorMessage.value = "";
  
  form.post(route("install.setDatabase"), {
    onSuccess: () => {
      loading.value = false;
    },
    onError: (errors) => {
      loading.value = false;
      if (errors.connection) {
        errorMessage.value = errors.connection;
      } else if (errors.error) {
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
          Konfigurasi Database
        </h1>
        <h2 class="text-xl text-gray-600">
          Langkah 3/5: Atur koneksi database untuk website Anda
        </h2>
      </div>

      <!-- Content -->
      <div class="bg-white shadow overflow-hidden rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="text-center mb-8">
            <svg xmlns="http://www.w3.org/2000/svg" height="80" viewBox="0 -960 960 960" width="80" class="mx-auto text-accent1 fill-current">
              <path d="M480-120q-66 0-129.5-17.5T234-191q-43-36-68.5-69.5T140-320v-320q0-35 25.5-68.5T234-769q53-36 116.5-53.5T480-840q66 0 129.5 17.5T726-769q43 36 68.5 69.5T820-640v320q0 35-25.5 68.5T726-191q-53 36-116.5 53.5T480-120Zm0-80q42 0 81-8.5t73-22.5q34-14 58-31t33-34l-62-36-414 238q69 31 152 31t152-31q69 31 152 31t152-31q33-14 57-31t34-34l-62-36-414 238Zm0-160q42 0 81-8.5t73-22.5q34-14 58-31t33-34l-62-36-414 238q69 31 152 31t152-31q69 31 152 31t152-31q33-14 57-31t34-34l-62-36-414 238Zm0-160q42 0 81-8.5t73-22.5q34-14 58-31t33-34l-62-36-414 238q69 31 152 31t152-31q69 31 152 31t152-31q33-14 57-31t34-34v-160q0-17-12.5-38T711-675q-45-27-106-46t-125-19q-64 0-125 19t-106 46q-26 16-38.5 37T198-600v160q0 17 12.5 38t37.5 37q45 27 106 46t126 19Zm0-240q64 0 125 19t106 46q26 16 38.5 37t12.5 38q0 17-12.5 38T711-355q-45 27-106 46t-125 19q-64 0-125-19t-106-46q-26-16-38.5-37T198-440q0-17 12.5-38t37.5-37q45-27 106-46t126-19Zm0 80q-42 0-81 8.5T326-680q-34 14-58 31t-33 34l62 36 414-238q-69-31-152-31t-152 31q-69-31-152-31t-152 31q-33 14-57 31t-34 34l62 36 414-238Z"/>
            </svg>
            <h3 class="mt-4 text-2xl font-medium text-gray-900">
              Koneksi Database
            </h3>
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <!-- Error message -->
            <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
              {{ errorMessage }}
            </div>

            <!-- Success message when test connection works -->
            <div v-if="testStatus === 'success'" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
              Koneksi database berhasil! Anda dapat melanjutkan ke langkah berikutnya.
            </div>

            <!-- Database configuration form -->
            <div class="space-y-6">
              <!-- Host -->
              <div>
                <label for="host" class="block text-sm font-medium text-gray-700">Host Database</label>
                <div class="mt-1">
                  <input 
                    id="host" 
                    v-model="form.host" 
                    type="text" 
                    required 
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                    placeholder="localhost atau 127.0.0.1"
                  />
                </div>
                <p v-if="form.errors.host" class="mt-2 text-sm text-red-600">{{ form.errors.host }}</p>
              </div>

              <!-- Port -->
              <div>
                <label for="port" class="block text-sm font-medium text-gray-700">Port</label>
                <div class="mt-1">
                  <input 
                    id="port" 
                    v-model="form.port" 
                    type="text" 
                    required 
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                    placeholder="3306"
                  />
                </div>
                <p v-if="form.errors.port" class="mt-2 text-sm text-red-600">{{ form.errors.port }}</p>
              </div>

              <!-- Database Name -->
              <div>
                <label for="database" class="block text-sm font-medium text-gray-700">Nama Database</label>
                <div class="mt-1">
                  <input 
                    id="database" 
                    v-model="form.database" 
                    type="text" 
                    required 
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                    placeholder="cms_desa"
                  />
                </div>
                <p v-if="form.errors.database" class="mt-2 text-sm text-red-600">{{ form.errors.database }}</p>
                <p class="mt-1 text-xs text-gray-500">Database harus sudah dibuat sebelumnya.</p>
              </div>

              <!-- Username -->
              <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <div class="mt-1">
                  <input 
                    id="username" 
                    v-model="form.username" 
                    type="text" 
                    required 
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                    placeholder="root"
                  />
                </div>
                <p v-if="form.errors.username" class="mt-2 text-sm text-red-600">{{ form.errors.username }}</p>
              </div>

              <!-- Password -->
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1">
                  <input 
                    id="password" 
                    v-model="form.password" 
                    type="password" 
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                    placeholder="Database password"
                  />
                </div>
                <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</p>
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
                    Pastikan database sudah dibuat dan pengguna database memiliki hak akses penuh.
                  </p>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Footer/Actions -->
        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between">
          <Link
            href="/install/permissions"
            class="inline-flex justify-center rounded-md border border-gray-300 py-2 px-4 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent1 focus:ring-offset-2"
          >
            Kembali
          </Link>
          <div class="space-x-2 flex">
            <button
              type="button"
              @click="testConnection"
              :disabled="loading || !isFormValid"
              class="inline-flex justify-center rounded-md border border-accent1 py-2 px-4 text-base font-medium text-accent1 shadow-sm hover:bg-accent1/10 focus:outline-none focus:ring-2 focus:ring-accent1 focus:ring-offset-2 disabled:opacity-70"
            >
              <span v-if="testStatus === 'testing'">
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-accent1 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menguji...
              </span>
              <span v-else>Uji Koneksi</span>
            </button>
            
            <button
              type="button"
              @click="submit"
              :disabled="loading || form.processing || !isFormValid"
              class="inline-flex justify-center rounded-md border border-transparent bg-accent1 py-2 px-6 text-base font-medium text-white shadow-sm hover:bg-accent1/90 focus:outline-none focus:ring-2 focus:ring-accent1 focus:ring-offset-2 disabled:opacity-70"
            >
              <span v-if="loading || form.processing">
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
              </span>
              <span v-else>Simpan & Lanjutkan</span>
            </button>
          </div>
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
            <div class="text-xs mt-2 text-gray-600 font-bold">Database</div>
          </div>
          <div class="w-1/5 text-center">
            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold">4</div>
            <div class="text-xs mt-2 text-gray-400">Admin</div>
          </div>
          <div class="w-1/5 text-center">
            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold">5</div>
            <div class="text-xs mt-2 text-gray-400">Selesai</div>
          </div>
        </div>
        <div class="relative flex items-center justify-center mt-2">
          <div class="h-1 w-full bg-gray-200">
            <div class="h-1 bg-accent1" style="width: 50%"></div>
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

.hover\:bg-accent1\/10:hover {
  background-color: var(--color-accent1-10, rgba(36, 64, 49, 0.1));
}

.focus\:ring-accent1:focus {
  --tw-ring-color: var(--color-accent1, #244031);
}

.focus\:border-accent1:focus {
  border-color: var(--color-accent1, #244031);
}

.border-accent1 {
  border-color: var(--color-accent1, #244031);
}
</style>