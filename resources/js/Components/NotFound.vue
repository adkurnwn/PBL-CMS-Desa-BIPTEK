<script setup>
import { Link } from '@inertiajs/vue3'

</script>

<template>
    <div class="flex flex-col items-center justify-center min-h-screen px-4 bg-gray-100 mt-12">
        <div class="bg-white rounded-xl shadow-sm p-10 max-w-2xl w-full text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 mb-6 mx-auto text-accent1" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 2L2 22"></path>
                <path d="M22 22L2 2"></path>
            </svg>
            <div class="text-8xl font-bold text-accent1 leading-none">404</div>
            <h1 class="text-2xl font-semibold text-gray-800 mb-4">Halaman Tidak Ditemukan</h1>
            <p class="text-gray-500 mb-6">Maaf, halaman yang Anda cari tidak ditemukan. Periksa kembali URL atau kembali
                ke halaman utama.</p>
            <Link :href="route('home')"
                class="inline-block px-6 py-3 bg-accent1 text-white rounded-md font-semibold no-underline hover:bg-accent2 transition-colors duration-200">
            Kembali ke Beranda</Link>
        </div>
    </div>
</template>