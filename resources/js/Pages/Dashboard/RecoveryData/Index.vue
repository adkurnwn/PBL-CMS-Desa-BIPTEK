<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
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
            <HeadingMenu title="Data Recovery">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" class="text-selectedText">
                        <path d="M20 36.6667C17.2222 36.6667 14.6389 36.0556 12.25 34.8333C9.86111 33.6111 7.80556 31.9722 6.08333 29.9167C4.36111 27.8611 3.05556 25.5 2.16667 22.8333C1.27778 20.1667 0.833333 17.3333 0.833333 14.3333C0.833333 11.7778 1.25 9.30556 2.08333 6.91667C2.91667 4.52778 4.11111 2.41667 5.66667 0.583336H14.1667L17.5 7.25H31.6667C33.0556 7.25 34.2361 7.75 35.2083 8.75C36.1806 9.75 36.6667 10.9444 36.6667 12.3333V15.6667H33.3333V12.3333C33.3333 11.8889 33.1944 11.5 32.9167 11.1667C32.6389 10.8333 32.2222 10.6667 31.6667 10.6667H15.8333L12.5 4H7.41667C6.27778 5.55556 5.41667 7.27778 4.83333 9.16667C4.25 11.0556 3.95833 12.9722 3.95833 14.9167C3.95833 19.6389 5.375 23.7778 8.20833 27.3333C11.0417 30.8889 14.6667 33.1111 19.0833 34H23.3333V28.3333L33.3333 36.6667L23.3333 45V39.1667H18.75C15.5556 39.1667 12.5556 38.5 9.75 37.1667C6.94444 35.8333 4.55556 33.9722 2.58333 31.5833C0.611111 29.1944 -0.305555 26.5556 0.0833334 23.6667L3.08333 24.3333C2.86111 26.2778 3.45833 28.0278 4.875 29.5833C6.29167 31.1389 8.05556 32.3611 10.1667 33.25C12.2778 34.1389 14.4722 34.5833 16.75 34.5833H20V31.3333H14.9167C11.5556 30.6111 8.77778 28.8889 6.58333 26.1667C4.38889 23.4444 3.33333 20.2222 3.41667 16.5L6.5 16.1667C6.5 18.9167 7.33333 21.3333 9 23.4167C10.6667 25.5 12.8333 26.8333 15.5 27.4167H20.0833V37.0833H20Z" fill="currentColor"/>
                    </svg>
                </template>
            </HeadingMenu>
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
                            <table class="min-w-full divide-y divide-gray-200 dashboard-table">
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
