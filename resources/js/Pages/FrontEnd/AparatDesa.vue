<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';

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
                    <h1 class="xl:text-2xl sm:text-xl text-md font-semibold text-accent2 mt-5">Struktur Organisasi dan Tata Kerja</h1>
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
        </main>
    </UserLayout>
</template>