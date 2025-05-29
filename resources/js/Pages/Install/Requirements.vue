<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    requirements: Object,
    allMet: Boolean,
});

const requirementsArray = computed(() => {
    if (!props.requirements) return [];

    return Object.entries(props.requirements).map(([name, met]) => ({
        name,
        met,
    }));
});

const canContinue = computed(() => {
    return props.allMet;
});
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
                    Persyaratan Sistem
                </h1>
                <h2 class="text-xl text-gray-600">
                    Langkah 2/6: Periksa apakah server Anda memenuhi persyaratan
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
                                d="M364.62-135.38q-14.77 0-25.08-10.31t-10.31-25.08v-404.46H232.31q-14.77 0-25.08-10.31t-10.31-25.08q0-14.77 10.31-25.08t25.08-10.31h118.46v-118.46q0-14.77 10.31-25.08t25.08-10.31q14.77 0 25.08 10.31t10.31 25.08v583.7q0 14.77-10.31 25.08t-25.08 10.31Zm230.76 0q-14.77 0-25.08-10.31t-10.31-25.08v-256.92q0-14.77 10.31-25.08t25.08-10.31q14.77 0 25.08 10.31t10.31 25.08v256.92q0 14.77-10.31 25.08t-25.08 10.31Zm-115.38 0q-14.77 0-25.08-10.31t-10.31-25.08v-139.23q0-14.77 10.31-25.08t25.08-10.31q14.77 0 25.08 10.31t10.31 25.08v139.23q0 14.77-10.31 25.08t-25.08 10.31Zm230.77 0q-14.77 0-25.08-10.31t-10.31-25.08v-404.46q0-14.77 10.31-25.08t25.08-10.31q14.77 0 25.07 10.31 10.31 10.31 10.31 25.08v404.46q0 14.77-10.31 25.08t-25.07 10.31Z"
                            />
                        </svg>
                        <h3 class="mt-4 text-2xl font-medium text-gray-900">
                            Persyaratan Teknis
                        </h3>
                    </div>

                    <div class="text-gray-600 mb-6 space-y-4">
                        <p>
                            CMS Desa BIPTEK memerlukan beberapa ekstensi PHP dan
                            versi minimum tertentu untuk berfungsi dengan baik.
                            Periksa apakah server Anda memenuhi persyaratan
                            berikut:
                        </p>
                    </div>

                    <!-- Requirements List -->
                    <div
                        class="bg-gray-50 p-4 rounded-md border border-gray-200 mb-6"
                    >
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Persyaratan
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="(
                                            req, index
                                        ) in requirementsArray"
                                        :key="index"
                                        :class="{
                                            'bg-gray-50': index % 2 === 0,
                                        }"
                                    >
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        >
                                            {{ req.name }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            <span
                                                v-if="req.met"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                                Terpenuhi
                                            </span>
                                            <span
                                                v-else
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                                Tidak Terpenuhi
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div
                        v-if="!canContinue"
                        class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4"
                    >
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-yellow-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Beberapa persyaratan tidak terpenuhi. Mohon
                                    perbaiki masalah tersebut sebelum
                                    melanjutkan instalasi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer/Actions -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between">
                    <Link
                        href="/install"
                        class="inline-flex justify-center rounded-md border border-gray-300 py-2 px-4 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent1 focus:ring-offset-2"
                    >
                        Kembali
                    </Link>
                    <Link
                        :href="canContinue ? '/install/permissions' : '#'"
                        :class="[
                            'inline-flex justify-center rounded-md border border-transparent py-2 px-6 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2',
                            canContinue
                                ? 'bg-accent1 hover:bg-accent1/90 focus:ring-accent1'
                                : 'bg-gray-400 cursor-not-allowed',
                        ]"
                    >
                        Lanjutkan
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
                        <div class="text-xs mt-2 text-gray-600 font-bold">
                            Persyaratan
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold"
                        >
                            3
                        </div>
                        <div class="text-xs mt-2 text-gray-400">Database</div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold"
                        >
                            4
                        </div>
                        <div class="text-xs mt-2 text-gray-400">Admin</div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold"
                        >
                            5
                        </div>
                        <div class="text-xs mt-2 text-gray-400">Selesai</div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold"
                        >
                            6
                        </div>
                        <div class="text-xs mt-2 text-gray-400">Selesai</div>
                    </div>
                </div>
                <div class="relative flex items-center justify-center mt-2">
                    <div class="h-1 w-full bg-gray-200">
                        <div class="h-1 bg-accent1" style="width: 25%"></div>
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
