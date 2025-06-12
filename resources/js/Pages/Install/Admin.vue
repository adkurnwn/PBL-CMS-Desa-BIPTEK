<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import ImageInput from "@/Components/DashboardComponent/ImageInput.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    photo_profile: null,
});

const loading = ref(false);
const errorMessage = ref("");

const submit = () => {
    loading.value = true;
    errorMessage.value = "";

    form.post(route("install.setAdmin"), {
        onSuccess: () => {
            loading.value = false;
        },
        onError: (errors) => {
            loading.value = false;
            if (errors.error) {
                errorMessage.value = errors.error;
            }
        },
    });
};
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
                    Buat Akun Administrator
                </h1>
                <h2 class="text-xl text-gray-600">
                    Langkah 5/7: Buat pengguna admin untuk mengelola website
                </h2>
            </div>

            <!-- Content -->
            <div class="bg-white shadow overflow-hidden rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-center mb-8">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="80"
                            viewBox="http://www.w3.org/2000/svg"
                            width="80"
                            class="mx-auto text-accent1 fill-current"
                        >
                            <path
                                d="M400-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18h14q6 0 12 2-8 18-13.5 37.5T404-360h-4q-71 0-127.5 18T180-306q-9 5-14.5 14t-5.5 20v32h252q6 21 16 41.5t22 38.5H80Zm560 40-12-60q-12-5-22.5-10.5T584-204l-58 18-40-68 46-40q-2-14-2-26t2-26l-46-40 40-68 58 18q11-7 21.5-12.5T628-460l12-60h80l12 60q12 5 22.5 10.5T776-436l58-18 40 68-46 40q2 14 2 26t-2 26l46 40-40 68-58-18q-11 7-21.5 12.5T732-180l-12 60h-80Zm40-120q33 0 56.5-23.5T760-320q0-33-23.5-56.5T680-400q-33 0-56.5 23.5T600-320q0 33 23.5 56.5T680-240Z"
                            />
                        </svg>
                        <h3 class="mt-4 text-2xl font-medium text-gray-900">
                            Akun Administrator
                        </h3>
                        <div
                            class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg"
                        >
                            <div class="flex items-center justify-center">
                                <svg
                                    class="w-5 h-5 text-yellow-600 mr-2"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                <p class="text-yellow-800 text-sm font-medium">
                                    Mohon lengkapi semua kolom untuk melanjutkan
                                    ke tahap berikutnya
                                </p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Error message -->
                        <div
                            v-if="errorMessage"
                            class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6"
                        >
                            {{ errorMessage }}
                        </div>

                        <!-- Admin user form -->
                        <div class="md:grid md:grid-cols-2 md:gap-6">
                            <div class="space-y-6">
                                <div>
                                    <label
                                        for="name"
                                        class="block text-sm font-medium text-gray-700"
                                        >Nama *</label
                                    >
                                    <div class="mt-1">
                                        <input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            required
                                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                                            placeholder="Nama Admin"
                                        />
                                    </div>
                                    <p
                                        v-if="form.errors.name"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{ form.errors.name }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="email"
                                        class="block text-sm font-medium text-gray-700"
                                        >Email *</label
                                    >
                                    <div class="mt-1">
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            required
                                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                                            placeholder="admin@example.com"
                                        />
                                    </div>
                                    <p
                                        v-if="form.errors.email"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{ form.errors.email }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="password"
                                        class="block text-sm font-medium text-gray-700"
                                        >Password *</label
                                    >
                                    <div class="mt-1">
                                        <input
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            required
                                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                                            placeholder="Minimal 8 karakter"
                                        />
                                    </div>
                                    <p
                                        v-if="form.errors.password"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{ form.errors.password }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700"
                                        >Konfirmasi Password *</label
                                    >
                                    <div class="mt-1">
                                        <input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            required
                                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                                            placeholder="Ulangi password"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <!-- Photo Profile Upload - Using ImageInput component -->
                                <ImageInput
                                    v-model="form.photo_profile"
                                    label="Foto Profil *"
                                    :error="form.errors.photo_profile"
                                    :current-image="null"
                                    image-height="200px"
                                />
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
                                        Pengguna admin memiliki akses penuh
                                        untuk mengelola seluruh konten dan
                                        pengaturan website desa.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer/Actions -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between">
                    <Link href="/install/database">
                        <SecondaryButton>Kembali </SecondaryButton>
                    </Link>
                    <PrimaryButton
                        @click="submit"
                        :disabled="loading || form.processing"
                        >Buat Admin & Lanjutkan</PrimaryButton
                    >
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
                        <div class="text-xs mt-2 text-gray-600">
                            Izin Direktori
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            4
                        </div>
                        <div class="text-xs mt-2 text-gray-600">Database</div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            5
                        </div>
                        <div class="text-xs mt-2 text-gray-600 font-bold">
                            Admin
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold"
                        >
                            6
                        </div>
                        <div class="text-xs mt-2 text-gray-400">
                            Profile Desa
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold"
                        >
                            7
                        </div>
                        <div class="text-xs mt-2 text-gray-400">Selesai</div>
                    </div>
                </div>
                <div class="relative flex items-center justify-center mt-2">
                    <div class="h-1 w-full bg-gray-200">
                        <div class="h-1 bg-accent1" style="width: 64%"></div>
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

.focus\:border-accent1:focus {
    border-color: var(--color-accent1, #244031);
}
</style>
