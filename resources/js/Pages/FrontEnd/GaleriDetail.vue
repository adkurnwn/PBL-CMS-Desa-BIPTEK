<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';

</script>

<template>
    <UserLayout>
        <article class="flex flex-col items-left justify-left bg-type1 font-merriweather">
            <h1 class="text-xl text-accent2 flex mt-40 ml-20">
            <span class="material-symbols-outlined mr-2 text-[1.3em] align-middle">gallery_thumbnail</span>
            Galeri Desa Pao-Pao
            </h1>
        </article>
        <article class="flex flex-col items-center justify-center bg-type1 font-merriweather ">
            <h1 class="xl:text-2xl sm:text-xl text-md font-semibold text-accent2 mt-5">Galeri Desa</h1> 
        </article>

        <article class="flex flex-col w-full text-lg items-center justify-center mt-5 px-20 bg-type1 font-merriweather">
            <div class="flex w-full items-start mb-1">
                <p class="text-accent1 w-36">Judul</p>
                <span class="text-accent2 w-4">:</span>
                <p class="text-accent2 flex-1">MOU Pao-Pao x ICMI</p>
            </div>
            <div class="flex w-full items-start mb-2">
                <p class="text-accent1 w-36">Tanggal</p>
                <span class="text-accent2 w-4">:</span>
                <p class="text-accent2 flex-1">12/04/2025</p>
            </div>
            <div class="flex w-full items-start">
                <p class="text-accent1 w-36">Deskripsi</p>
                <span class="text-accent2 w-4">:</span>
                <p class="text-accent2 flex-1">
                Kegiatan Desa Pao-Pao dalam mendukung Program Desa Cerdas melalui kerja sama dengan ICMI Orwil Sulawesi Selatan. Fokus utama program ini adalah pengembangan potensi pertanian desa menuju lumbung jagung.
                </p>
            </div>
        </article>
    </UserLayout>

</template>