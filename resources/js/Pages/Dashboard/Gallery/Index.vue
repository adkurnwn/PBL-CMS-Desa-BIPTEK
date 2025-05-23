<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const props = defineProps({
    galleries: Array,
});

const form = useForm({});

const deleteGallery = (slug) => {
    if (confirm("Are you sure you want to delete this gallery?")) {
        form.delete(route("dashboard.galleries.destroy", slug), {
            preserveScroll: true,
        });
    }
};
</script>

<template>

    <Head title="Gallery" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Galeri" :hasRightContent="true">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M360-400h400L622-580l-92 120-62-80-108 140Zm-40 160q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320Zm0-80h480v-480H320v480ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Zm160-720v480-480Z" />
                    </svg>

                </template>
                <template #right-content>
                    <div class="flex space-x-2">
                        <Link :href="route('dashboard.galleries.archive')">
                        <PrimaryButton>Archived Gallery</PrimaryButton>
                        </Link>
                        <Link :href="route('dashboard.galleries.create')">
                        <PrimaryButton>Create New Gallery</PrimaryButton>
                        </Link>
                    </div>
                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm  ">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Judul
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="gallery in galleries" :key="gallery.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ gallery.title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link :href="route(
                                            'dashboard.galleries.edit',
                                            gallery.slug
                                        )
                                            ">
                                        <PrimaryButton>
                                            Edit Gallery
                                        </PrimaryButton>
                                        </Link>
                                        <DangerButton @click="deleteGallery(gallery.slug)">
                                            Delete
                                        </DangerButton>
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
