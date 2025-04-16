<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    demografi: Array,
});

const form = useForm({});

const deleteDemografi = (demografiId) => {
    if (confirm("Are you sure you want to delete this demographic data?")) {
        form.delete(route("dashboard.demografi.destroy", demografiId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Demografi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Demografi
                </h2>

                <Link :href="route('dashboard.demografi.create')">
                    <PrimaryButton>Create New Demografi</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash Message will be handled by AuthenticatedLayout -->

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Tahun
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Jumlah Penduduk
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Jumlah KK
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="demografi in demografi"
                                    :key="demografi.id"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ demografi.tahun }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ demografi.jumlah_penduduk }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ demografi.jumlah_kk }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex gap-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'dashboard.demografi.edit',
                                                        demografi.id
                                                    )
                                                "
                                            >
                                                <PrimaryButton>
                                                    Edit
                                                </PrimaryButton>
                                            </Link>
                                            <DangerButton
                                                @click="
                                                    deleteDemografi(
                                                        demografi.id
                                                    )
                                                "
                                            >
                                                Delete
                                            </DangerButton>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="demografi.length === 0">
                                    <td
                                        class="px-6 py-4 text-center text-gray-500"
                                        colspan="4"
                                    >
                                        No demographic data found. Create new
                                        demographic data to get started.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
