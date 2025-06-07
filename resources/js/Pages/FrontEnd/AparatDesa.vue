<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import useApiFetch from "@/Utility/useApiFetch";
import { onMounted, ref } from 'vue';
import { useImageErrorHandler } from "@/Utility/useImageErrorHandler";

const aparat = ref([]);
const profile = ref({});
const error = ref(null);
const loading = ref(true);

const aparatDesa = useApiFetch();
const fetchAparatDesa = async () => {
    await aparatDesa.fetchData('/api/users');
    if (aparatDesa.data.value) {
        aparat.value = aparatDesa.data.value.users || [];
        profile.value = {
            organization_image: aparatDesa.data.value.organization_image || ''
        };
    }
    error.value = aparatDesa.error.value;
    loading.value = aparatDesa.loading.value;
};

const showPopup = ref(false);
const selectedAparat = ref(null);

// Create a map to store individual image error handlers
const imageHandlers = ref(new Map());

// Function to get or create an image error handler for a specific image
function getImageHandler(imagePath) {
    if (!imageHandlers.value.has(imagePath)) {
        imageHandlers.value.set(imagePath, useImageErrorHandler());
    }
    return imageHandlers.value.get(imagePath);
}

function openPopup(aparat) {
    selectedAparat.value = aparat;
    showPopup.value = true;
}

function closePopup() {
    showPopup.value = false;
    selectedAparat.value = null;
}

function handleOutsideClick(e) {
    // Check if the click target is the overlay itself (outside the popup content)
    if (e.target === e.currentTarget) {
        closePopup();
    }
}

function parseJobDescription(jobDescStr) {
    if (!jobDescStr) return [];
    try {
        return JSON.parse(jobDescStr);
    } catch (e) {
        console.error("Error parsing job description:", e);
        return [];
    }
}

onMounted(() => {
    fetchAparatDesa();
});
</script>

<template>
    <UserLayout v-slot="{layout}">
        <main class="min-h-screen bg-type1 font-merriweather max-w-screen-3xl w-full px-4 sm:px-12 md:px-20 mx-auto py-0 pt-32 pb-6 lg:pt-36 4xl:pt-44">
            <article class="flex flex-col bg-type1">
                <h1 class="text-accent2 flex items-center text-lg">
                <span class="material-symbols-outlined mr-2 text-[1.3em] align-middle">assignment_ind</span>
                Aparat Pemerintah {{ layout.name }}
                </h1>
            </article>
            <!--Struktur Organisasi dan Tata Kerja-->
            <article class="flex flex-col items-center justify-center bg-type1">
                <!--Judul-->
                <div>
                    <h1 class="xl:text-2xl sm:text-xl text-md font-semibold text-accent2 mt-5">
                        Struktur Organisasi dan Tata Kerja
                    </h1>
                </div> 
                <!--Gambar-->
                <div class="flex w-full h-full mt-5 items-center justify-center">
                    <img
                        :src="getImageHandler(profile.organization_image).getImageSrc(`/storage/${profile.organization_image}`)"
                        alt="Struktur Organisasi"
                        class="w-full h-auto rounded-lg"
                        @error="getImageHandler(profile.organization_image).handleImageError()"
                    />
                </div>
            </article>
            <!-- Aparat Pemerintahan Desa -->
            <article class="flex flex-col items-center justify-center bg-type1">
                <!--Judul-->
                <div>
                    <h1 class="xl:text-2xl sm:text-xl text-md font-semibold text-accent2 mt-7">
                        Aparat Pemerintahan Desa
                    </h1>
                </div> 
                <!--Gambar Aparat-->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mt-5 w-full px-2 mb-10 font-thin">
                    <div
                        v-for="(aparatItem, index) in aparat"
                        :key="index"
                        class="flex flex-col items-center bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
                        @click="openPopup(aparatItem)"
                    >
                        <img
                            :src="getImageHandler(aparatItem.image).getImageSrc(`/storage/${aparatItem.image}`)"
                            :alt="aparatItem.name"
                            class="w-full h-60 object-cover object-top"
                            @error="getImageHandler(aparatItem.image).handleImageError()"
                        />
                        <div class="bg-accent2 w-full py-2 px-2 text-center">
                            <div class="text-white text-sm uppercase leading-tight truncate">{{ aparatItem.name }}</div>
                            <div class="text-white text-xs italic">{{ aparatItem.position }}</div>
                        </div>
                    </div>
                </div>
            </article>
            <!-- Popup Deskripsi Tugas -->
            <article
                v-if="showPopup && selectedAparat"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-40 w-full p-4 mx-auto"
                @click="handleOutsideClick"
            >
                <div class="bg-type1 rounded-lg shadow-lg flex flex-col sm:flex-row p-6 max-w-2xl w-full max-h-full relative">
                    <!-- Foto dan Nama -->
                    <div class="flex-shrink-0 flex flex-col items-center w-full sm:w-1/3">
                        <img
                            :src="getImageHandler(selectedAparat.image).getImageSrc(`/storage/${selectedAparat.image}`)"
                            :alt="selectedAparat.name"
                            class="w-full aspect-[308/357] object-cover object-top"
                            @error="getImageHandler(selectedAparat.image).handleImageError()"
                        />
                        <div class="bg-accent2 w-full py-2 px-2 text-center">
                            <div class="text-white font-thin text-sm uppercase leading-tight">{{ selectedAparat.name }}</div>
                            <div class="text-white text-xs font-thin italic">{{ selectedAparat.position }}</div>
                        </div>
                    </div>
                    <!-- Deskripsi Tugas -->
                    <div class="flex-1 ml-0 sm:ml-6 mt-4 sm:mt-0 text-accent1">
                        <h2 class="text-lg mb-2">Deskripsi Tugas</h2>
                        <ul class="list-disc ml-5 text-sm space-y-1">
                            <li v-for="(tugas, index) in parseJobDescription(selectedAparat.job_description)" :key="index">
                                    {{ tugas }}
                            </li>
                        </ul>
                    </div>
                    <!-- Tombol Close -->
                    <button @click="closePopup" class="absolute top-2 right-2 text-gray-400 hover:text-accent2 text-2xl font-bold">&times;</button>
                </div>
            </article>
        </main>
    </UserLayout>
</template>