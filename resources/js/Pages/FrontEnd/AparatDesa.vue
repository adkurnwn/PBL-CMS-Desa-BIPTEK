<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';

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
</script>

<template>
    <UserLayout>
        <main class="mx-auto mb-10 h-full">
            <article class="flex flex-col bg-type1 font-merriweather">
                <h1 class="text-xl text-accent2 flex mt-40 ml-20">
                <span class="material-symbols-outlined mr-2 text-[1.3em] align-middle">assignment_ind</span>
                Aparat Pemerintah Desa Pao-Pao
                </h1>
            </article>
            <!--Struktur Organisasi dan Tata Kerja-->
            <article class="flex flex-col items-center justify-center bg-type1 font-merriweather">
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
            <article class="flex flex-col items-center justify-center bg-type1 font-merriweather">
                <!--Judul-->
                <div>
                    <h1 class="xl:text-2xl sm:text-xl text-md font-semibold text-accent2 mt-7">
                        Aparat Pemerintahan Desa
                    </h1>
                </div> 
                <!--Gambar Aparat-->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mt-5 w-full max-w-6xl px-2">
                    <div
                        v-for="aparat in aparatDesa"
                        :key="aparat.nama"
                        class="flex flex-col items-center bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
                    >
                        <img
                            :src="aparat.foto"
                            :alt="aparat.nama"
                            class="w-full h-60 object-cover object-top"
                        />
                        <div class="bg-accent2 w-full py-2 px-2 text-center">
                            <div class="text-white font-semibold text-sm uppercase leading-tight">{{ aparat.nama }}</div>
                            <div class="text-white text-xs">{{ aparat.jabatan }}</div>
                        </div>
                    </div>
                </div>
            </article>
        </main>
    </UserLayout>
</template>