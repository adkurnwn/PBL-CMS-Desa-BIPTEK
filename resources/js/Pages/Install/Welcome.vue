<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="min-h-screen bg-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-3xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h1
                    class="text-4xl font-extrabold text-accent1 tracking-tight mb-2"
                >
                    Selamat Datang di CMS Desa BIPTEK
                </h1>
                <h2 class="text-xl text-gray-600">
                    Langkah Pertama Membangun Website Desa Anda
                </h2>
            </div>

            <!-- Content -->
            <div class="bg-white shadow overflow-hidden rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-center mb-8">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="80"
                            viewBox="0 -960 960 960"
                            width="80"
                            class="mx-auto text-accent1 fill-current"
                        >
                            <path
                                d="M480-95.384q-78.268 0-147.883-30.08-69.616-30.079-121.444-81.907-51.829-51.828-81.908-121.445-30.079-69.615-30.079-147.883 0-78.269 30.079-147.885 30.079-69.615 81.908-121.444 51.828-51.828 121.444-81.907Q401.732-857.615 480-857.615q78.269 0 147.884 30.08 69.616 30.079 121.444 81.907 51.829 51.829 81.908 121.444 30.08 69.616 30.08 147.885 0 78.268-30.08 147.883-30.079 69.617-81.908 121.445-51.828 51.828-121.444 81.907Q558.269-95.384 480-95.384ZM480-384q40.385 0 68.192-27.808Q576-439.615 576-480t-27.808-68.192Q520.385-576 480-576t-68.192 27.808Q384-520.385 384-480t27.808 68.192Q439.615-384 480-384Z"
                            />
                        </svg>
                        <h3 class="mt-4 text-2xl font-medium text-gray-900">
                            Instalasi CMS Desa
                        </h3>
                    </div>

                    <div class="text-gray-600 mb-8 space-y-4">
                        <p>
                            Selamat datang di proses instalasi CMS Desa BIPTEK.
                            Website ini adalah solusi lengkap untuk manajemen
                            konten desa yang akan membantu Anda:
                        </p>

                        <ul class="list-disc pl-6 space-y-2">
                            <li>
                                Mengelola informasi dan berita desa dengan mudah
                            </li>
                            <li>Mempublikasikan profil dan potensi desa</li>
                            <li>
                                Memfasilitasi layanan publik untuk masyarakat
                                desa
                            </li>
                            <li>
                                Menyajikan transparansi mengenai dana desa dan
                                keuangan
                            </li>
                            <li>
                                Memperkenalkan struktur organisasi dan perangkat
                                desa
                            </li>
                        </ul>

                        <p>
                            Proses instalasi ini akan membantu Anda mengatur
                            konfigurasi dasar yang diperlukan untuk menjalankan
                            website Desa. Ikuti langkah-langkah berikut untuk
                            menyelesaikan proses instalasi.
                        </p>
                    </div>

                    <div
                        class="bg-gray-50 p-4 rounded-md border border-gray-200"
                    >
                        <h4 class="font-medium text-gray-900 mb-2">
                            Proses Instalasi
                        </h4>
                        <ol
                            class="list-decimal pl-6 space-y-1 text-sm text-gray-600"
                        >
                            <li>Pemeriksaan kebutuhan sistem</li>
                            <li>Verifikasi izin direktori</li>
                            <li>Konfigurasi basis data</li>
                            <li>Pembuatan akun administrator</li>
                            <li>Finalisasi instalasi</li>
                        </ol>
                    </div>
                </div>

                <!-- Footer/Actions -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end">
                    <Link
                        href="/install/requirements"
                        class="inline-flex justify-center rounded-md border border-transparent bg-accent1 py-2 px-6 text-base font-medium text-white shadow-sm hover:bg-accent1/90 focus:outline-none focus:ring-2 focus:ring-accent1 focus:ring-offset-2"
                    >
                        Mulai Instalasi
                    </Link>
                </div>
            </div>

            <!-- Disclaimer -->
            <div class="text-center text-xs text-gray-500">
                <p>CMS Desa BIPTEK &copy; {{ new Date().getFullYear() }}</p>
                <p>
                    Dibuat untuk membantu pembangunan digital desa di Indonesia
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-accent1 {
    background-color: var(--color-accent1, #244031);
}

.text-accent1 {
    color: var(--color-accent1, #244031);
}

.hover\:bg-accent1\/90:hover {
    background-color: var(--color-accent1-90, rgba(36, 64, 49, 0.9));
}

.focus\:ring-accent1:focus {
    --tw-ring-color: var(--color-accent1, #244031);
}
</style>
