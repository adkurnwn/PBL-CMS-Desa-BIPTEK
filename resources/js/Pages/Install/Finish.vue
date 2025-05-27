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
                    Instalasi Selesai
                </h1>
                <h2 class="text-xl text-gray-600">
                    Langkah 5/5: Website CMS Desa siap digunakan
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
                            class="mx-auto text-green-600 fill-current"
                        >
                            <path
                                d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"
                            />
                        </svg>
                        <h3 class="mt-4 text-2xl font-medium text-gray-900">
                            Instalasi Berhasil!
                        </h3>
                    </div>

                    <div class="text-gray-600 mb-8 space-y-4">
                        <p class="text-center">
                            Selamat! CMS Desa telah berhasil diinstal dan siap
                            digunakan.
                        </p>

                        <div
                            class="bg-green-50 p-6 rounded-md border border-green-200"
                        >
                            <h4 class="font-medium text-green-800 mb-2">
                                Berikut adalah langkah selanjutnya:
                            </h4>
                            <ul class="list-disc pl-6 space-y-2 text-green-700">
                                <li>
                                    Login ke dashboard admin menggunakan email
                                    dan password yang telah Anda buat
                                </li>
                                <li>
                                    Lengkapi pengaturan dasar website desa
                                    seperti nama desa, logo, dan informasi
                                    kontak
                                </li>
                                <li>
                                    Tambahkan konten website seperti berita,
                                    kegiatan, dan informasi profil desa
                                </li>
                                <li>Publish website Anda kepada masyarakat</li>
                            </ul>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50 p-4 rounded-md border border-gray-200"
                    >
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-accent1"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    Jika Anda memerlukan bantuan lebih lanjut,
                                    silakan hubungi tim dukungan kami melalui
                                    email
                                    <a
                                        href="mailto:support@biptek.id"
                                        class="text-accent1 hover:underline"
                                        >support@biptek.id</a
                                    >
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer/Actions -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end">
                    <Link
                        href="/login"
                        class="inline-flex justify-center rounded-md border border-transparent bg-accent1 py-2 px-6 text-base font-medium text-white shadow-sm hover:bg-accent1/90 focus:outline-none focus:ring-2 focus:ring-accent1 focus:ring-offset-2"
                    >
                        Ke Halaman Login
                    </Link>
                </div>
            </div>

            <!-- Installation progress -->
            <div class="mt-8">
                <div class="flex justify-between">
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            1
                        </div>
                        <div class="text-xs mt-2 text-gray-600">
                            Selamat Datang
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            2
                        </div>
                        <div class="text-xs mt-2 text-gray-600">
                            Persyaratan
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            3
                        </div>
                        <div class="text-xs mt-2 text-gray-600">Database</div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            4
                        </div>
                        <div class="text-xs mt-2 text-gray-600">Admin</div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            5
                        </div>
                        <div class="text-xs mt-2 text-gray-600 font-bold">
                            Selesai
                        </div>
                    </div>
                </div>
                <div class="relative flex items-center justify-center mt-2">
                    <div class="h-1 w-full bg-gray-200">
                        <div class="h-1 bg-accent1" style="width: 100%"></div>
                    </div>
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
