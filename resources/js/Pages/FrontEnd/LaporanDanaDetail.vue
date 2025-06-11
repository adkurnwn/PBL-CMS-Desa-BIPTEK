<template>
  <Head>
    <title>{{ metaTitle }}</title>
    <meta name="description" :content="metaDescription" />
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" :content="metaType" />
    <meta property="og:title" :content="metaTitle" />
    <meta property="og:description" :content="metaDescription" />
    <meta property="og:url" :content="canonicalUrl" />
    <meta property="og:site_name" :content="metaSiteName" />
    <meta property="og:image" :content="metaImage" />
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="metaTitle" />
    <meta name="twitter:description" :content="metaDescription" />
    <meta name="twitter:image" :content="metaImage" />
    
    <!-- Article specific -->
    <meta property="article:published_time" :content="metaPublishedTime" v-if="metaPublishedTime" />
    <meta property="article:author" :content="metaAuthor" v-if="metaAuthor" />
    
    <!-- Canonical URL -->
    <link rel="canonical" :href="canonicalUrl" />
  </Head>

  <UserLayout v-slot="{layout}">
    <div class="min-h-screen bg-type1">
      <div class="max-w-screen-2xl mx-auto px-6 lg:px-28 py-6 lg:py-12">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <p class="text-red-600">{{ error }}</p>
        </div>

        <!-- Document Detail -->
          <div v-else class="mt-24 font-merriweather">
            <div class="flex space-x-2 items-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor" class="text-accent2">
                    <path d="M280-280q17 0 28.5-11.5T320-320q0-17-11.5-28.5T280-360q-17 0-28.5 11.5T240-320q0 17 11.5 28.5T280-280Zm-40-160h80v-240h-80v240Zm200 160h280v-80H440v80Zm0-160h280v-80H440v80Zm0-160h280v-80H440v80ZM160-120q-33 0-56.5-23.5T80-200v-560q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v560q0 33-23.5 56.5T800-120H160Zm0-80h640v-560H160v560Zm0 0v-560 560Z"/>
                </svg>
                <h1 class="lg:text-lg  text-accent2 -mb-0.5 font-merriweather ">
                    Dana Desa <span class="hidden md:inline">{{ layout.name }}</span> 
                </h1>
            </div>

            <!-- Title -->
            <h1 class="flex text-xl lg:text-3xl w-full mx-auto justify-start lg:justify-center items-center font-bold text-accent1 mb-4">
              Detail Laporan Dana Desa
            </h1>
            
            
            <!-- Title -->
            <!-- <h2 class="text-3xl font-bold text-accent1 mb-4">
              {{ dokumen.title }}
            </h2> -->

            <!-- MetaData -->
            <div>
              <div class="flex flex-col gap-2.5 lg:gap-4 mb-4lg:mb-8 font-merriweather font-medium text-accent1">
                
                <div class="flex flex-row items-start text-sm lg:text-base">
                  <div class="flex flex-col w-1/5 font-bold">Judul</div>
                    <div class="flex flex-row w-4/5">
                      <div class="flex flex-col mr-1">:</div>
                      <p class="flex col-span-2"> {{ dokumen.title }}</p>
                    </div>
                </div>
                <div class="flex flex-row items-start text-sm lg:text-base">
                  <div class="flex flex-col w-1/5 font-bold">Tanggal</div>
                    <div class="flex flex-row w-4/5">
                      <div class="flex flex-col mr-1">:</div>
                      <p class="flex col-span-2"> {{ formatDate(dokumen.date) }}</p>
                    </div>
                </div>
                
                <div class="flex flex-row items-start text-sm lg:text-base">
                  <div class="flex flex-col w-1/5 font-bold">Link</div>
                    <div class="flex flex-row w-4/5">
                      <div class="flex flex-col mr-1">:</div>
                      <a v-if="dokumen.link" :href="dokumen.link" target="_blank" class="line-clamp-1 text-blue-600 hover:underline ">
                        {{ dokumen.link }}
                      </a>
                      <p v-else class="flex col-span-9"> Tidak Tersedia</p>
                    </div>
                </div>
                <div class="mt-4">
                <button v-if="dokumen.file"
                  :href="`/storage/${dokumen.file}`"
                   @click="handleDownload"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-accent2 hover:bg-accent1"
                >
                  Unduh Laporan
                </button>
              </div>
                
              </div>
              <div v-if="dokumen.file && isPreviewable(dokumen.file)" class="pt-4">
                <iframe
                  :src="`/storage/${dokumen.file}`"
                  width="100%"
                  height="100%"
                  class="border rounded font-merriweather h-[350px] lg:h-[750px]"
                ></iframe>
              </div>
              
            </div>
            
            <!-- Metadata
            <div class="flex items-center text-sm text-gray-500 mb-8">
              <span>{{ formatDate(dokumen.date) }}</span>
            </div> -->

            <!-- Description -->
            <!-- <div class="prose max-w-none mb-8">
              {{ dokumen.description }}
            </div> -->

            <!-- Document Actions -->
            <!-- <div class="flex space-x-4">
              <a v-if="dokumen.file"
                 :href="`/storage/${dokumen.file}`"
                 target="_blank"
                 class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
              >
                Download Document
              </a>
              <a v-if="dokumen.link"
                 :href="dokumen.link"
                 target="_blank"
                 class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                View External Link
              </a>
            </div> -->
        </div>

      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useMeta } from '@/Utility/useMeta';
import axios from 'axios';


const props = defineProps({
  id: {
    type: String,
    required: true
  },
  slug: {
    type: String,

  }
});

const dokumen = ref({});
const loading = ref(true);
const error = ref(null);

const comments = ref([]);



// Meta tags setup
const {
  metaTitle,
  metaDescription,
  metaImage,
  canonicalUrl,
  metaType,
  metaSiteName,
  metaAuthor,
  metaPublishedTime
} = useMeta({
  title: computed(() => dokumen.value.title || 'Laporan Dana Desa'),
  description: computed(() => {
    if (dokumen.value.title) {
      return `Detail laporan dana desa: ${dokumen.value.title}. Akses dokumen transparan penggunaan anggaran dana desa.`;
    }
    return 'Detail laporan dana desa - Transparansi penggunaan anggaran dana desa untuk pembangunan dan pemberdayaan masyarakat.';
  }),
  type: 'article',
  url: computed(() => `${window.location.origin}/dana-desa/${props.slug}`),
  author: 'Admin',
  publishedTime: computed(() => dokumen.value.date)
});


// Home data
// const home = ref({});
// const homeLoading = ref(false);

// const fetchHome = async () => {
//     try {
//         homeLoading.value = true;
//         const response = await axios.get("/api/home");
//         home.value = response.data;
//     } catch (error) {
//         console.error("Error fetching home data:", error);
//     } finally {
//         homeLoading.value = false;
//     }
// };
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const isPreviewable = (filePath) => {
  const previewableExtensions = ['.pdf', '.jpg', '.jpeg', '.png'];
  const fileExtension = filePath.split('.').pop().toLowerCase();
  return previewableExtensions.includes(`.${fileExtension}`);
};

const getAvatar = (name) => {
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random`;
};

const truncateText = (text, length) => {
  if (!text) return '';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const fetchDokumen = async () => {
  try {
    loading.value = true;
    const response = await axios.get(`/api/dana-desa/${props.slug}`);
    dokumen.value = response.data.data;
  } catch (err) {
    error.value = 'Gagal memuat dokumen';
    console.error('Error fetching document:', err);
  } finally {
    loading.value = false;
  }
};

const handleDownload = async () => {
  try {
    const response = await fetch(`/api/dana-desa/${props.slug}/download`);
    const data = await response.json();
    
    if (data.success) {
      // Create a temporary link element and trigger download
      const link = document.createElement('a');
      link.href = data.file_url;
      link.download = ''; // This will use the filename from the server
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    } else {
      console.error('Download failed:', data.message);
    }
  } catch (error) {
    console.error('Download error:', error);
  }
};






onMounted(() => {
  fetchDokumen();

});
</script>