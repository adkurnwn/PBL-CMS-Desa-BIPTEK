<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    backups: Array,
});

const downloading = ref(false);
const restoring = ref(false);

const downloadBackup = (fileName) => {
    window.location.href = route("dashboard.recovery.download", fileName);
};

const createBackup = () => {
    router.post(
        route("dashboard.recovery.create"),
        {},
        {
            preserveScroll: true,
        }
    );
};

const restoreBackup = (path) => {
    if (
        confirm(
            "Are you sure you want to restore this backup? This will replace all current data."
        )
    ) {
        restoring.value = true;
        router.post(
            route("dashboard.recovery.restore", path),
            {},
            {
                preserveScroll: true,
                onFinish: () => {
                    restoring.value = false;
                },
            }
        );
    }
};

const deleteBackup = (path) => {
    if (confirm("Are you sure you want to delete this backup?")) {
        router.delete(route("dashboard.recovery.delete", path), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Data Recovery" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Recovery
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                Backup Management
                            </h3>
                            <PrimaryButton @click="createBackup"
                                >Create New Backup</PrimaryButton
                            >
                        </div>

                        <div class="bg-gray-100 p-4 mb-6 rounded-lg">
                            <h4 class="text-md font-medium mb-2">
                                About Data Backup
                            </h4>
                            <p class="text-sm text-gray-600">
                                This page allows you to create, download, and
                                restore data backups for your application.
                                Creating a backup will save the current state of
                                your database and files. Restoring will replace
                                the current data with the selected backup.
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Date
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Size
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr v-if="backups && backups.length === 0">
                                        <td
                                            class="px-6 py-4 text-center text-gray-500"
                                            colspan="3"
                                        >
                                            No backups available. Create your
                                            first backup to get started.
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="(backup, index) in backups"
                                        :key="index"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ backup.date }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ backup.size }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex space-x-2">
                                                <PrimaryButton
                                                    @click="
                                                        downloadBackup(
                                                            backup.path
                                                        )
                                                    "
                                                    :disabled="downloading"
                                                >
                                                    Download
                                                </PrimaryButton>
                                                <PrimaryButton
                                                    class="bg-yellow-500 hover:bg-yellow-600"
                                                    @click="
                                                        restoreBackup(
                                                            backup.path
                                                        )
                                                    "
                                                    :disabled="restoring"
                                                >
                                                    Restore
                                                </PrimaryButton>
                                                <DangerButton
                                                    @click="
                                                        deleteBackup(
                                                            backup.path
                                                        )
                                                    "
                                                >
                                                    Delete
                                                </DangerButton>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
