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
    
    <!-- Product specific -->
    <meta property="product:price:amount" :content="produk.price" v-if="produk.price" />
    <meta property="product:availability" content="in_stock" />
    
    <!-- Canonical URL -->
    <link rel="canonical" :href="canonicalUrl" />
  </Head>

  <UserLayout v-slot="{ layout }">
    <div class="min-h-screen bg-type1">
      <div class="max-w-screen-4xl mx-auto w-full px-[6.56%] pt-20">
        <div v-if="loading" class="text-center py-8">Memuat detail produk...</div>
        <div v-else-if="error" class="text-center text-red-600 py-8">{{ error }}</div>
        <div v-else>
          <main class="mt-16 font-merriweather ">
            <div class="flex items-center space-x-2 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor" class="text-accent1 ">
                <path d="M120-200q-33 0-56.5-23.5T40-280v-400q0-33 23.5-56.5T120-760h400q33 0 56.5 23.5T600-680v400q0 33-23.5 56.5T520-200H120Zm600-320q-17 0-28.5-11.5T680-560v-160q0-17 11.5-28.5T720-760h160q17 0 28.5 11.5T920-720v160q0 17-11.5 28.5T880-520H720Zm40-80h80v-80h-80v80ZM120-280h400v-400H120v400Zm40-80h320L375-500l-75 100-55-73-85 113Zm560 160q-17 0-28.5-11.5T680-240v-160q0-17 11.5-28.5T720-440h160q17 0 28.5 11.5T920-400v160q0 17-11.5 28.5T880-200H720Zm40-80h80v-80h-80v80Zm-640 0v-400 400Zm640-320v-80 80Zm0 320v-80 80Z"/>
              </svg>
                <h1 class="flex text-accent2">Produk Unggulan <span class="hidden md:inline">{{ layout.name }}</span></h1>
            </div>
            
            <div class="flex flex-col md:flex-row w-full gap-8 pb-8">
              <!-- Images section - mobile: full width, desktop: left side -->
              <div class="w-full md:w-2/5">
                <!-- Multiple images layout -->
                <div v-if="produk.images?.length > 1" class="flex flex-col gap-2">
                  <!-- Selected image on top -->
                  <div class="h-64">
                    <img 
                      :src="selectedImage" 
                      alt="" 
                      class="w-full h-full object-cover rounded-lg" 
                      @error="handleImageError(selectedImage)"
                    />
                  </div>
                  
                  <!-- Thumbnail collage below -->
                  <div class="flex gap-2 h-24">
                    <div class="flex-1">
                      <img 
                        :src="getImageSrc(`/storage/${produk.images[0].path}`)" 
                        alt="" 
                        class="w-full h-full object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity"
                        :class="{ 'ring-2 ring-accent2': selectedImageIndex === 0 }"
                        @click="selectedImageIndex = 0"
                        @error="handleImageError(`/storage/${produk.images[0].path}`)"
                      />
                    </div>
                    <div v-if="produk.images?.length > 1" class="flex-1">
                      <img 
                        :src="getImageSrc(`/storage/${produk.images[1].path}`)" 
                        alt="" 
                        class="w-full h-full object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity"
                        :class="{ 'ring-2 ring-accent2': selectedImageIndex === 1 }"
                        @click="selectedImageIndex = 1"
                        @error="handleImageError(`/storage/${produk.images[1].path}`)"
                      />
                    </div>
                    <div v-if="produk.images?.length > 2" class="flex-1">
                      <img 
                        :src="getImageSrc(`/storage/${produk.images[2].path}`)" 
                        alt="" 
                        class="w-full h-full object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity"
                        :class="{ 'ring-2 ring-accent2': selectedImageIndex === 2 }"
                        @click="selectedImageIndex = 2"
                        @error="handleImageError(`/storage/${produk.images[2].path}`)"
                      />
                    </div>
                  </div>
                </div>
                
                <!-- Single image display -->
                <div v-else-if="produk.images?.length === 1" class="h-64">
                  <img 
                    :src="getImageSrc(`/storage/${produk.images[0].path}`)" 
                    alt="" 
                    class="w-full h-full object-cover rounded-lg" 
                    @error="handleImageError(`/storage/${produk.images[0].path}`)"
                  />
                </div>
              </div>
              
              <!-- Content section - mobile: full width below images, desktop: right side -->
              <div class="flex flex-col justify-start flex-1">
                <div class="flex items-center justify-start">
                  <p class="text-accent-1 text-4xl">{{ produk.name || '-' }}</p>
                </div>
                <div class="flex items-center justify-start">
                  <p class="mt-2 mb-2 text-justify whitespace-pre-line">{{ produk.description }}</p>
                </div>
                <div class="flex items-center justify-start my-3">
                  <p class="mb-2 text-2xl">{{ produk.price ? `${produk.price}` : '-' }}</p>
                </div>
                <div class="flex items-center justify-start mb-1 space-x-2">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor" class="text-accent1">
                    <path d="M841-518v318q0 33-23.5 56.5T761-120H201q-33 0-56.5-23.5T121-200v-318q-23-21-35.5-54t-.5-72l42-136q8-26 28.5-43t47.5-17h556q27 0 47 16.5t29 43.5l42 136q12 39-.5 71T841-518Zm-272-42q27 0 41-18.5t11-41.5l-22-140h-78v148q0 21 14 36.5t34 15.5Zm-180 0q23 0 37.5-15.5T441-612v-148h-78l-22 140q-4 24 10.5 42t37.5 18Zm-178 0q18 0 31.5-13t16.5-33l22-154h-78l-40 134q-6 20 6.5 43t41.5 23Zm540 0q29 0 42-23t6-43l-42-134h-76l22 154q3 20 16.5 33t31.5 13ZM201-200h560v-282q-5 2-6.5 2H751q-27 0-47.5-9T663-518q-18 18-41 28t-49 10q-27 0-50.5-10T481-518q-17 18-39.5 28T393-480q-29 0-52.5-10T299-518q-21 21-41.5 29.5T211-480h-4.5q-2.5 0-5.5-2v282Zm560 0H201h560Z"/>
                  </svg>
                  <p class=" font-light">{{ produk.nama_penjual ? `${produk.nama_penjual}` : '-' }}</p>
                </div>
                <div class="flex items-center justify-start mb-2 space-x-2">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor" class="text-accent1">
                    <path d="M798-120q-125 0-247-54.5T329-329Q229-429 174.5-551T120-798q0-18 12-30t30-12h162q14 0 25 9.5t13 22.5l26 140q2 16-1 27t-11 19l-97 98q20 37 47.5 71.5T387-386q31 31 65 57.5t72 48.5l94-94q9-9 23.5-13.5T670-390l138 28q14 4 23 14.5t9 23.5v162q0 18-12 30t-30 12ZM241-600l66-66-17-94h-89q5 41 14 81t26 79Zm358 358q39 17 79.5 27t81.5 13v-88l-94-19-67 67ZM241-600Zm358 358Z"/>
                  </svg>
                  <p class="font-light">{{ produk.phone ? `${produk.phone}` : '-' }}</p>
                </div>
                
                <div class="flex items-center justify-start mb-1 space-x-4 mt-1">
                  <a
                    v-if="produk.instagram"
                    :href="produk.instagram"
                    target="_blank"
                    rel="noopener"
                    class="text-accent1 hover:text-accent2 transition-colors duration-300"
                  >
                    <i class="bi bi-instagram md:text-xl relative"></i>
                  </a>
                  <a
                    v-if="produk.x"
                    :href="produk.x"
                    target="_blank"
                    rel="noopener"
                    class="text-accent1 hover:text-accent2 transition-colors duration-300"
                  >
                    <i class="bi bi-twitter-x md:text-xl relative"></i>
                  </a>
                  <a
                    v-if="produk.facebook"
                    :href="produk.facebook"
                    target="_blank"
                    rel="noopener"
                    class="text-accent1 hover:text-accent2 transition-colors duration-300"
                  >
                    <i class="bi bi-facebook md:text-xl relative"></i>
                  </a>
                  <a
                    v-if="produk.tiktok"
                    :href="produk.tiktok"
                    target="_blank"
                    rel="noopener"
                    class="text-accent1 hover:text-accent2 transition-colors duration-300"
                  >
                    <i class="bi bi-tiktok md:text-xl relative"></i>
                  </a>
                  <a
                    v-if="produk.youtube"
                    :href="produk.youtube"
                    target="_blank"
                    rel="noopener"
                    class="text-accent1 hover:text-accent2 transition-colors duration-300"
                  >
                    <i class="bi bi-youtube md:text-xl relative"></i>
                  </a>
                  <a
                    v-if="produk.alamat"
                    :href="produk.alamat"
                    target="_blank"
                    rel="noopener"
                    class="text-accent1 hover:text-accent2 transition-colors duration-300"
                  >
                    <i class="bi bi-geo-alt-fill md:text-xl relative"></i>
                  </a>
                </div>
                
                <div class="flex items-center justify-start mb-1 space-x-4 mt-1">
                  <a
                      v-if="produk.phone"
                      :href="`https://wa.me/${whatsappNumber}?text=${encodeURIComponent('Haloo saya .... dari ..., saya ingin bertanya apakah produk ' + (produk.name || '-') + ' masih tersedia?')}`"
                      target="_blank"
                      rel="noopener"
                      class="flex justify-center bg-accent2 text-white space-x-2 text-xs md:text-sm p-2 rounded-lg mt-4"
                    >
                      <i class="bi bi-whatsapp text-lg"></i>
                      <p class="mt-1">Hubungi Penjual</p>
                    </a>
                </div>
                    
                
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, onMounted,computed } from 'vue';
import axios from 'axios';
import { Head } from "@inertiajs/vue3";
import UserLayout from '@/Layouts/UserLayout.vue';
import { useImageErrorHandler } from '@/Utility/useImageErrorHandler';
import { useMeta } from "@/Utility/useMeta";

const props = defineProps({ slug: String });

const { handleImageError, getImageSrc } = useImageErrorHandler();

const produk = ref({});
const loading = ref(true);
const error = ref(null);
const selectedImageIndex = ref(0);

const selectedImage = computed(() => {
  if (!produk.value.images || produk.value.images.length === 0) {
    return '';
  }
  const selectedImg = produk.value.images[selectedImageIndex.value];
  return selectedImg ? getImageSrc(`/storage/${selectedImg.path}`) : '';
});

// Meta tags setup using the composable
const {
    metaTitle,
    metaDescription,
    metaImage,
    canonicalUrl,
    metaType,
    metaSiteName
} = useMeta({
    title: computed(() => produk.value.name ? `${produk.value.name}` : 'Produk Unggulan'),
    description: computed(() => {
        if (!produk.value.description) return 'Produk unggulan dari desa kami dengan kualitas terbaik';
        
        // Limit description to 160 characters
        const description = produk.value.description;
        return description.length > 160 
            ? description.substring(0, 157) + '...' 
            : description;
    }),
    type: 'product',
    image: computed(() => produk.value.images?.length > 0 ? produk.value.images[0].path : null),
    url: computed(() => `${window.location.origin}/produk-unggulan/${props.slug}`)
});

onMounted(async () => {
  try {
    const response = await axios.get(`/api/produk-desa/${props.slug}`);
    produk.value = response.data.data;
  } catch (e) {
    error.value = 'Gagal memuat detail produk';
  } finally {
    loading.value = false;
  }
});

const whatsappNumber = computed(() => {
  if (!produk.value.phone) return '';
  // Remove all non-digit characters
  let phone = produk.value.phone.replace(/\D/g, '');
  // Remove leading zeros and country code if present
  if (phone.startsWith('62')) {
    return phone;
  }
  if (phone.startsWith('0')) {
    return '62' + phone.slice(1);
  }
  if (phone.startsWith('8')) {
    return '62' + phone;
  }
  // If already starts with +62, remove the plus
  if (produk.value.phone.startsWith('+62')) {
    return phone;
  }
  return phone;
});
</script>
