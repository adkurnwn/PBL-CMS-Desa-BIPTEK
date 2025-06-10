<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, onMounted } from "vue";
import useApiFetch from "@/Utility/useApiFetch";
import { formatDate } from "@/Utility/useComment";
import NotFound from "@/Components/NotFound.vue";
import { useImageErrorHandler } from "@/Utility/useImageErrorHandler";

const props = defineProps({
    slug: {
        type: String,
        required: true,
    },
});

const gallery = ref({});
const loading = ref(true);
const error = ref(null);

// Modal state
const showModal = ref(false);
const modalImage = ref({});
const currentImageIndex = ref(0);

// Touch/swipe state
const touchStartX = ref(0);
const touchEndX = ref(0);
const minSwipeDistance = 50;

const { handleImageError, getImageSrc } = useImageErrorHandler();

// Modal functions
const openModal = (image) => {
    const imageIndex = gallery.value.images.findIndex(img => img.id === image.id);
    currentImageIndex.value = imageIndex;
    modalImage.value = image;
    showModal.value = true;
    document.body.style.overflow = 'hidden';
};

const closeModal = () => {
    showModal.value = false;
    modalImage.value = {};
    currentImageIndex.value = 0;
    document.body.style.overflow = 'auto';
};

const nextImage = () => {
    if (currentImageIndex.value < gallery.value.images.length - 1) {
        currentImageIndex.value++;
        modalImage.value = gallery.value.images[currentImageIndex.value];
    }
};

const prevImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--;
        modalImage.value = gallery.value.images[currentImageIndex.value];
    }
};

// Touch/swipe functions
const handleTouchStart = (e) => {
    touchStartX.value = e.changedTouches[0].screenX;
};

const handleTouchEnd = (e) => {
    touchEndX.value = e.changedTouches[0].screenX;
    handleSwipe();
};

const handleSwipe = () => {
    const swipeDistance = touchStartX.value - touchEndX.value;
    
    if (Math.abs(swipeDistance) > minSwipeDistance) {
        if (swipeDistance > 0) {
            // Swipe left - next image
            nextImage();
        } else {
            // Swipe right - previous image
            prevImage();
        }
    }
};

// Gallery fetch logic
const galleryApi = useApiFetch();
const fetchGalleryDetail = async () => {
    await galleryApi.fetchData(`/api/gallery/${props.slug}`);
    gallery.value = galleryApi.data.value || {};
    error.value = galleryApi.error.value;
    loading.value = galleryApi.loading.value;
};

onMounted(() => {
    fetchGalleryDetail();
    
    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && showModal.value) {
            closeModal();
        }
        if (e.key === 'ArrowLeft' && showModal.value) {
            prevImage();
        }
        if (e.key === 'ArrowRight' && showModal.value) {
            nextImage();
        }
    });
});
</script>

<template>
    <UserLayout v-slot="{layout}">
        <div v-if="error">
            <NotFound />
        </div>
        
        <main v-else class="max-w-screen-3xl mx-auto w-full px-[6.56%] pt-32 4xl:pt-44 pb-8 xl:pb-16">
            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-accent2"></div>
            </div>

            <div v-else>
                <article class="flex flex-col bg-type1 font-merriweather">
                    <h1 class="sm:text-base xl:text-xl text-accent2 flex">
                    <span class="material-symbols-outlined mr-2 sm:text-base xl:text-xl align-middle">gallery_thumbnail</span>
                    Galeri {{ layout.name }}
                    </h1>
                </article>
                <article class="flex flex-col items-center justify-center bg-type1 font-merriweather ">
                    <h1 class="text-md sm:text-xl xl:text-2xl font-semibold text-accent2 mt-5">Galeri Desa</h1> 
                </article>
                <!--Judul, Tanggal, Deskripsi-->
                <article class="flex flex-col w-full text-sm xl:text-lg items-center justify-center mb-4 mt-5 bg-type1 font-merriweather">
                    <div class="flex w-full items-start mb-1">
                        <p class="text-accent1 w-36">Judul</p>
                        <span class="text-accent2 w-4">:</span>
                        <p class="text-accent2 flex-1">{{ gallery.title || 'Tidak ada judul' }}</p>
                    </div>
                    <div class="flex w-full items-start mb-2">
                        <p class="text-accent1 w-36">Tanggal</p>
                        <span class="text-accent2 w-4">:</span>
                        <p class="text-accent2 flex-1">{{ formatDate(gallery.created_at) }}</p>
                    </div>
                    <div class="flex w-full items-start">
                        <p class="text-accent1 w-36">Deskripsi</p>
                        <span class="text-accent2 w-4">:</span>
                        <p class="text-accent2 flex-1">
                        {{ gallery.description || 'Tidak ada deskripsi' }}
                        </p>
                    </div>
                </article>
                <!--Gambar-->
                <article class="w-full flex flex-col bg-type1 font-merriweather">
                    <div v-if="gallery.images && gallery.images.length > 0" class="space-y-4">
                        <template v-for="(image, index) in gallery.images" :key="image.id">
                            <!-- Pattern: 1st-2nd images side by side, 3rd full width, 4th-5th side by side, 6th full width, etc. -->
                            
                            <!-- Handle pairs (1st-2nd, 4th-5th, 7th-8th, etc.) -->
                            <div v-if="index % 3 === 0" class="flex gap-4">
                                <!-- First image of the pair -->
                                <div class="overflow-hidden cursor-pointer" style="width: calc(50% - 8px);">
                                    <img
                                        :src="getImageSrc(`/storage/${image.path}`)"
                                        :alt="image.alt"
                                        class="h-[150px] sm:h-[170px] lg:h-[300px] object-cover w-full transition-transform duration-300 hover:scale-110"
                                        @error="handleImageError(`/storage/${image.path}`)"
                                        @click="openModal(image)"
                                    />
                                </div>
                                
                                <!-- Second image of the pair (if it exists) -->
                                <div v-if="index + 1 < gallery.images.length" class="overflow-hidden cursor-pointer" style="width: calc(50% - 8px);">
                                    <img
                                        :src="getImageSrc(`/storage/${gallery.images[index + 1].path}`)"
                                        :alt="gallery.images[index + 1].alt"
                                        class="h-[150px] sm:h-[170px] lg:h-[300px] object-cover w-full transition-transform duration-300 hover:scale-110"
                                        @error="handleImageError(`/storage/${gallery.images[index + 1].path}`)"
                                        @click="openModal(gallery.images[index + 1])"
                                    />
                                </div>
                            </div>
                            
                            <!-- Handle full width images (3rd, 6th, 9th, etc.) -->
                            <div v-else-if="index % 3 === 2" class="w-full overflow-hidden cursor-pointer">
                                <img
                                    :src="getImageSrc(`/storage/${image.path}`)"
                                    :alt="image.alt"
                                    class="w-full h-[200px] sm:h-[250px] lg:h-[400px] object-cover transition-transform duration-300 hover:scale-110"
                                    @error="handleImageError(`/storage/${image.path}`)"
                                    @click="openModal(image)"
                                />
                            </div>
                        </template>
                    </div>
                    
                    <!-- No images message -->
                    <div v-else class="flex justify-center items-center py-12">
                        <p class="text-accent2">Tidak ada gambar untuk ditampilkan</p>
                    </div>
                </article>
            </div>
        </main>

        <!-- Image Modal -->
        <div v-if="showModal" class="fixed font-merriweather inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-85 p-[6.56%] lg:px-[6.56%] lg:py-0" @click="closeModal">
            <div 
                class="relative max-w-screen max-h-screen-lg w-full h-full flex items-center justify-center" 
                @click.stop
                @touchstart="handleTouchStart"
                @touchend="handleTouchEnd"
            >
                <!-- Close button -->
                <button 
                    @click="closeModal" 
                    class="absolute top-4 right-4 z-10 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full p-2 transition-all duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Image counter -->
                <div class="absolute top-4 left-4 z-10 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full">
                    <span class="text-sm font-medium">{{ currentImageIndex + 1 }}/{{ gallery.images?.length || 0 }}</span>
                </div>

                <!-- Previous button - only on lg screens and above -->
                <button 
                    v-if="currentImageIndex > 0"
                    @click="prevImage" 
                    class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full p-3 transition-all duration-200 hidden lg:block"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Next button - only on lg screens and above -->
                <button 
                    v-if="currentImageIndex < (gallery.images?.length || 0) - 1"
                    @click="nextImage" 
                    class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full p-3 transition-all duration-200 hidden lg:block"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Swipe instruction for mobile -->
                <div class="absolute bottom-16 left-1/2 transform -translate-x-1/2 z-10 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-xs lg:hidden">
                    <span>Geser untuk navigasi</span>
                </div>
                
                <!-- Modal image -->
                 <div class="p-[6.56%]">
                <img
                    :src="getImageSrc(`/storage/${modalImage.path}`)"
                    :alt="modalImage.alt"
                    class="max-w-full max-h-full object-contain rounded-lg shadow-2xl"
                    @error="handleImageError(`/storage/${modalImage.path}`)"
                />
                </div>
                
                <!-- Image caption -->
                <div v-if="modalImage.alt" class="absolute bottom-4 left-4 right-4 bg-black bg-opacity-50 text-white p-3 rounded-lg">
                    <p class="text-center">{{ modalImage.alt }}</p>
                </div>
            </div>
        </div>
    </UserLayout>
</template>