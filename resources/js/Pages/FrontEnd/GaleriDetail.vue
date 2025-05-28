<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';

</script>

<template>
    <UserLayout>
        <main class="max-w-screen-2xl mx-auto w-full">
            <article class="flex flex-col bg-type1 font-merriweather">
                <h1 class="text-xl text-accent2 flex mt-40 ml-20">
                <span class="material-symbols-outlined mr-2 text-[1.3em] align-middle">gallery_thumbnail</span>
                Galeri Desa Pao-Pao
                </h1>
            </article>
            <article class="flex flex-col items-center justify-center bg-type1 font-merriweather ">
                <h1 class="xl:text-2xl sm:text-xl text-md font-semibold text-accent2 mt-5">Galeri Desa</h1> 
            </article>
            <!--Judul, Tanggal, Deskripsi-->
            <article class="flex flex-col w-full text-lg items-center justify-center mb-4 mt-5 px-20 bg-type1 font-merriweather">
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
            <!--Gambar-->
            <article class="w-full px-20 flex flex-col pb-8 lg:pb-24 bg-type1 font-merriweather space-y-5">
                <div class="flex flex-row w-full h-full justify-between space-x-5">
                    <img
                        src="../../../images/GALERI_DESA1.png"
                        alt="Gambar 1"
                        class="w-3/5 h-[150px] sm:h-[225px] lg:h-[300px] object-cover"
                    />
                    <img
                        src="../../../images/GALERI_DESA1.png"
                        alt="Gambar 2"
                        class="w-3/5 h-[150px] sm:h-[225px] lg:h-[300px] object-cover"
                    />
                </div>
                <div class="flex flex-col sm:flex-row w-full justify-between space-x-5">
                    <img
                        src="../../../images/GALERI_DESA1.png"
                        alt="Gambar 3"
                        class="w-full sm:w-1/3 h-[200px] lg:w-96 object-cover"
                    />
                    <img
                        src="../../../images/GALERI_DESA1.png"
                        alt="Gambar 4"
                        class="w-full sm:w-1/3 h-[200px] lg:w-96 object-cover"
                    />
                    <img
                        src="../../../images/GALERI_DESA1.png"
                        alt="Gambar 5"
                        class="w-full sm:w-1/3 h-[200px] lg:w-96 object-cover"
                    />
                </div>
            </article>
        </main>
    </UserLayout>
</template>