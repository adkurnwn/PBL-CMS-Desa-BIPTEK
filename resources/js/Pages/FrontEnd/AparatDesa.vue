<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref } from 'vue';

const aparatDesa = [
    {
        nama: 'Syamsul Bahri, S.E.',
        jabatan: 'Kepala Desa',
        foto: 'images/FOTO_KADES.png',
    },
    {
        nama: 'Syamsul Bahri, S.E.',
        jabatan: 'Kepala Desa',
        foto: 'images/FOTO_KADES.png',
    },
    {
        nama: 'Syamsul Bahri, S.E.',
        jabatan: 'Kepala Desa',
        foto: 'images/FOTO_KADES.png',
    },
        {
        nama: 'Syamsul Bahri, S.E.',
        jabatan: 'Kepala Desa',
        foto: 'images/FOTO_KADES.png',
    },
    {
        nama: 'Syamsul Bahri, S.E.',
        jabatan: 'Kepala Desa',
        foto: 'images/FOTO_KADES.png',
    },
        {
        nama: 'Syamsul Bahri, S.E.',
        jabatan: 'Kepala Desa',
        foto: 'images/FOTO_KADES.png',
    },
        {
        nama: 'Syamsul Bahri, S.E.',
        jabatan: 'Kepala Desa',
        foto: 'images/FOTO_KADES.png',
    },
        {
        nama: 'Syamsul Bahri, S.E.',
        jabatan: 'Kepala Desa',
        foto: 'images/FOTO_KADES.png',
    },
    {
        nama: 'Syamsul Bahri, S.E.',
        jabatan: 'Kepala Desa',
        foto: 'images/FOTO_KADES.png',
    },
    {
        nama: 'Syamsul Bahri, S.E.',
        jabatan: 'Kepala Desa',
        foto: 'images/FOTO_KADES.png',
    },   
];
const showPopup = ref(false);
const selectedAparat = ref<any>(null);
const tugasAparat: Record<string, string[]> = {
    'Kepala Desa': [
        'Memimpin penyelenggaraan pemerintahan desa',
        'Mengembangkan perekonomian desa',
        'Mengkoordinasikan pembangunan desa secara partisipatif',
        'Mewakili desanya di dalam dan di luar pengadilan',
        'Menetapkan peraturan desa yang telah disetujui bersama BPD',
    ],
};
function openPopup(aparat: any) {
    selectedAparat.value = aparat;
    showPopup.value = true;
}
function closePopup() {
    showPopup.value = false;
    selectedAparat.value = null;
}
</script>

<template>
    <UserLayout>
        <main class="mx-auto mb-10 h-full font-merriweather">
            <article class="flex flex-col bg-type1">
                <h1 class="text-xl text-accent2 flex mt-40 ml-20">
                <span class="material-symbols-outlined mr-2 text-[1.3em] align-middle">assignment_ind</span>
                Aparat Pemerintah Desa Pao-Pao
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
                        src="../../../images/SO_PEMERINTAHAN_DESA.png"
                        alt="Struktur Organisasi"
                        class="w-full max-w-6xl h-auto"
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
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mt-5 w-full max-w-6xl px-2 mb-10 font-thin">
                    <div
                        v-for="aparat in aparatDesa"
                        :key="aparat.nama"
                        class="flex flex-col items-center bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
                        @click="openPopup(aparat)"
                    >
                        <img
                            :src="aparat.foto"
                            :alt="aparat.nama"
                            class="w-full h-60 object-cover object-top"
                        />
                        <div class="bg-accent2 w-full py-2 px-2 text-center">
                            <div class="text-white text-sm uppercase leading-tight">{{ aparat.nama }}</div>
                            <div class="text-white text-xs italic">{{ aparat.jabatan }}</div>
                        </div>
                    </div>
                </div>
            </article>
            <!-- Popup Deskripsi Tugas -->
            <article
                v-if="showPopup && selectedAparat"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
            >
                <div class="bg-type1 rounded-lg shadow-lg flex flex-col md:flex-row p-6 max-w-2xl w-full relative">
                    <!-- Foto dan Nama -->
                    <div class="flex-shrink-0 flex flex-col items-center w-full md:w-1/3">
                        <img
                            :src="selectedAparat.foto"
                            :alt="selectedAparat.nama"
                            class="w-40 h-52 object-cover object-top"
                        />
                        <div class="bg-accent2 w-full py-2 px-2 text-center mt-2">
                            <div class="text-white font-thin text-sm uppercase leading-tight">{{ selectedAparat.nama }}</div>
                            <div class="text-white text-xs font-thin italic">{{ selectedAparat.jabatan }}</div>
                        </div>
                    </div>
                    <!-- Deskripsi Tugas -->
                    <div class="flex-1 ml-0 md:ml-6 mt-4 md:mt-0 text-accent1">
                        <h2 class="text-lg mb-2">Deskripsi Tugas</h2>
                        <ul class="list-disc ml-5 text-sm space-y-1">
                            <li v-for="tugas in tugasAparat[selectedAparat.jabatan] || []" :key="tugas">{{ tugas }}</li>
                        </ul>
                    </div>
                    <!-- Tombol Close -->
                    <button @click="closePopup" class="absolute top-2 right-2 text-gray-400 hover:text-accent2 text-2xl font-bold">&times;</button>
                </div>
            </article>
        </main>
    </UserLayout>
</template>