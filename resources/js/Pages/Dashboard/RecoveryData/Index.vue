<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
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
            <HeadingMenu title="Cadangkan dan Pulihkan Data">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M480-400q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0 280q-139 0-241-91.5T122-440h82q14 104 92.5 172T480-200q117 0 198.5-81.5T760-480q0-117-81.5-198.5T480-760q-69 0-129 32t-101 88h110v80H120v-240h80v94q51-64 124.5-99T480-840q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-120Z" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm  ">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                Backup Management
                            </h3>
                            <PrimaryButton @click="createBackup">Create New Backup</PrimaryButton>
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
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Size
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="backups && backups.length === 0">
                                        <td class="px-6 py-4 text-center text-gray-500" colspan="3">
                                            No backups available. Create your
                                            first backup to get started.
                                        </td>
                                    </tr>
                                    <tr v-for="(backup, index) in backups" :key="index">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <span v-if="backup.date">
                                                    {{ backup.date }}
                                                </span>
                                                <span v-else>Unknown date</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ backup.size }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex space-x-2">
                                                <PrimaryButton @click="
                                                    downloadBackup(
                                                        backup.path
                                                    )
                                                    " :disabled="downloading">
                                                    Download
                                                </PrimaryButton>
                                                <PrimaryButton class="bg-yellow-500 hover:bg-yellow-600" @click="
                                                    restoreBackup(
                                                        backup.path
                                                    )
                                                    " :disabled="restoring">
                                                    Restore
                                                </PrimaryButton>
                                                <DangerButton @click="
                                                    deleteBackup(
                                                        backup.path
                                                    )
                                                    ">
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
