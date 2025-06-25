<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import axios from "axios";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";

const props = defineProps({
    currentConfig: Object,
});

const form = useForm({
    host: props.currentConfig.host,
    port: props.currentConfig.port,
    database: props.currentConfig.database,
    username: props.currentConfig.username,
    password: props.currentConfig.password,
    run_seeders: true, // Default to true
});

const loading = ref(false);
const errorMessage = ref("");
const testStatus = ref(null);
const connectionTested = ref(false); // Track if connection has been tested
const connectionSuccessful = ref(false); // Track if the last test was successful

const isFormValid = computed(() => {
    return form.host && form.port && form.database && form.username;
});

// Check if user can proceed (connection must be tested and successful)
const canProceed = computed(() => {
    return (
        isFormValid.value &&
        connectionTested.value &&
        connectionSuccessful.value
    );
});

const testConnection = async () => {
    if (!isFormValid.value) return;

    loading.value = true;
    testStatus.value = "testing";
    connectionTested.value = false;
    connectionSuccessful.value = false;

    const testData = {
        host: form.host,
        port: form.port,
        database: form.database,
        username: form.username,
        password: form.password,
    };

    try {
        const response = await axios.post(
            route("install.testConnection"),
            testData
        );
        loading.value = false;
        connectionTested.value = true;

        if (response.data.success) {
            testStatus.value = "success";
            connectionSuccessful.value = true;
        } else {
            testStatus.value = "failed";
            connectionSuccessful.value = false;
            errorMessage.value =
                response.data.message || "Database connection failed.";
        }
    } catch (error) {
        loading.value = false;
        testStatus.value = "failed";
        connectionTested.value = true;
        connectionSuccessful.value = false;
        errorMessage.value =
            error.response?.data?.message ||
            "An error occurred while testing the database connection.";
        console.error(error);
    }
};

// Reset connection status when form values change
const resetConnectionStatus = () => {
    if (connectionTested.value) {
        connectionTested.value = false;
        connectionSuccessful.value = false;
        testStatus.value = null;
        errorMessage.value = "";
    }
};

const submit = () => {
    if (!canProceed.value) return;

    loading.value = true;
    errorMessage.value = "";

    form.post(route("install.setDatabase"), {
        onSuccess: () => {
            loading.value = false;
        },
        onError: (errors) => {
            loading.value = false;
            if (errors.connection) {
                errorMessage.value = errors.connection;
            } else if (errors.error) {
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
                    Konfigurasi Database
                </h1>
                <h2 class="text-xl text-gray-600">
                    Langkah 4/7: Atur koneksi database untuk website Anda
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
                                d="M480-120q-66 0-129.5-17.5T234-191q-43-36-68.5-69.5T140-320v-320q0-35 25.5-68.5T234-769q53-36 116.5-53.5T480-840q66 0 129.5 17.5T726-769q43 36 68.5 69.5T820-640v320q0 35-25.5 68.5T726-191q-53 36-116.5 53.5T480-120Zm0-80q42 0 81-8.5t73-22.5q34-14 58-31t33-34l-62-36-414 238q69 31 152 31t152-31q69 31 152 31t152-31q33-14 57-31t34-34l-62-36-414 238Zm0-160q42 0 81-8.5t73-22.5q34-14 58-31t33-34l-62-36-414 238q69 31 152 31t152-31q69 31 152 31t152-31q33-14 57-31t34-34l-62-36-414 238Zm0-160q42 0 81-8.5t73-22.5q34-14 58-31t33-34l-62-36-414 238q69 31 152 31t152-31q69 31 152 31t152-31q33-14 57-31t34-34v-160q0-17-12.5-38T711-675q-45-27-106-46t-125-19q-64 0-125 19t-106 46q-26 16-38.5 37T198-600v160q0 17 12.5 38t37.5 37q45 27 106 46t126 19Zm0-240q64 0 125 19t106 46q26 16 38.5 37t12.5 38q0 17-12.5 38T711-355q-45 27-106 46t-125 19q-64 0-125-19t-106-46q-26-16-38.5-37T198-440q0-17 12.5-38t37.5-37q45-27 106-46t126-19Zm0 80q-42 0-81 8.5T326-680q-34 14-58 31t-33 34l62 36 414-238q-69-31-152-31t-152 31q-69-31-152-31t-152 31q-33 14-57 31t-34 34l62 36 414-238Z"
                            />
                        </svg>
                        <h3 class="mt-4 text-2xl font-medium text-gray-900">
                            Koneksi Database
                        </h3>

                        <!-- Warning message untuk uji koneksi -->
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
                                    Wajib melakukan uji koneksi database dan
                                    berhasil sebelum dapat melanjutkan
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

                        <!-- Success message when test connection works -->
                        <div
                            v-if="testStatus === 'success'"
                            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6"
                        >
                            <div class="flex items-center">
                                <svg
                                    class="w-5 h-5 text-green-600 mr-2"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                Koneksi database berhasil! Anda dapat
                                melanjutkan ke langkah berikutnya.
                            </div>
                        </div>

                        <!-- Database configuration form -->
                        <div class="space-y-6">
                            <!-- Host -->
                            <div>
                                <label
                                    for="host"
                                    class="block text-sm font-medium text-gray-700"
                                    >Host Database *</label
                                >
                                <div class="mt-1">
                                    <input
                                        id="host"
                                        v-model="form.host"
                                        @input="resetConnectionStatus"
                                        type="text"
                                        required
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                                        placeholder="localhost atau 127.0.0.1"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.host"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.host }}
                                </p>
                            </div>

                            <!-- Port -->
                            <div>
                                <label
                                    for="port"
                                    class="block text-sm font-medium text-gray-700"
                                    >Port *</label
                                >
                                <div class="mt-1">
                                    <input
                                        id="port"
                                        v-model="form.port"
                                        @input="resetConnectionStatus"
                                        type="text"
                                        required
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                                        placeholder="3306"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.port"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.port }}
                                </p>
                            </div>

                            <!-- Database Name -->
                            <div>
                                <label
                                    for="database"
                                    class="block text-sm font-medium text-gray-700"
                                    >Nama Database *</label
                                >
                                <div class="mt-1">
                                    <input
                                        id="database"
                                        v-model="form.database"
                                        @input="resetConnectionStatus"
                                        type="text"
                                        required
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                                        placeholder="cms_desa"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.database"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.database }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Database harus sudah dibuat sebelumnya.
                                </p>
                            </div>

                            <!-- Username -->
                            <div>
                                <label
                                    for="username"
                                    class="block text-sm font-medium text-gray-700"
                                    >Username *</label
                                >
                                <div class="mt-1">
                                    <input
                                        id="username"
                                        v-model="form.username"
                                        @input="resetConnectionStatus"
                                        type="text"
                                        required
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                                        placeholder="root"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.username"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.username }}
                                </p>
                            </div>

                            <!-- Password -->
                            <div>
                                <label
                                    for="password"
                                    class="block text-sm font-medium text-gray-700"
                                    >Password</label
                                >
                                <div class="mt-1">
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        @input="resetConnectionStatus"
                                        type="password"
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-accent1 focus:border-accent1 sm:text-sm"
                                        placeholder="Database password"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.password"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <!-- Run Seeders -->
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.run_seeders"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-600">
                                        Jalankan seeders data contoh (Disarankan
                                        untuk instalasi baru)
                                    </span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1">
                                    Ini akan mengisi database Anda dengan
                                    wilayah, kategori, dan data penting lainnya
                                    sebagai contoh.
                                </p>
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
                                        Pastikan database sudah dibuat dan
                                        pengguna database memiliki hak akses
                                        penuh.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer/Actions -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between">
                    <Link href="/install/permissions">
                        <SecondaryButton>Kembali</SecondaryButton>
                    </Link>
                    <div class="space-x-2 flex">
                        <PrimaryButton
                            @click="testConnection"
                            :disabled="loading || !isFormValid"
                            >Uji Koneksi</PrimaryButton
                        >

                        <PrimaryButton
                            v-if="!loading && !form.processing && canProceed"
                            @click="submit"
                        >
                            Simpan & Lanjutkan
                        </PrimaryButton>
                    </div>
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
                        <div class="text-xs mt-2 text-gray-600 font-bold">
                            Database
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold"
                        >
                            5
                        </div>
                        <div class="text-xs mt-2 text-gray-400">Admin</div>
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
                        <div class="h-1 bg-accent1" style="width: 50%"></div>
                    </div>
                </div>
            </div>

            <!-- Disclaimer -->
            <div class="text-center text-xs text-gray-500">
                <p>CMS Waradesa &copy; {{ new Date().getFullYear() }}</p>
                <p>Powered by PT BIPTEK Tokodata Indonesia</p>
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

.hover\:bg-accent1\/10:hover {
    background-color: var(--color-accent1-10, rgba(36, 64, 49, 0.1));
}

.focus\:ring-accent1:focus {
    --tw-ring-color: var(--color-accent1, #244031);
}

.focus\:border-accent1:focus {
    border-color: var(--color-accent1, #244031);
}

.border-accent1 {
    border-color: var(--color-accent1, #244031);
}
</style>
