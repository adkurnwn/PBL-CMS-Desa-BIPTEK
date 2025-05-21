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
                    <svg xmlns="http://www.w3.org/2000/svg" width="29" height="28" viewBox="0 0 29 28" fill="none">
                        <path
                            d="M3.70833 27.875C2.86042 27.875 2.13455 27.5731 1.53073 26.9693C0.92691 26.3655 0.625 25.6396 0.625 24.7917V3.20833C0.625 2.36042 0.92691 1.63455 1.53073 1.03073C2.13455 0.42691 2.86042 0.125 3.70833 0.125H25.2917C26.1396 0.125 26.8655 0.42691 27.4693 1.03073C28.0731 1.63455 28.375 2.36042 28.375 3.20833V24.7917C28.375 25.6396 28.0731 26.3655 27.4693 26.9693C26.8655 27.5731 26.1396 27.875 25.2917 27.875H3.70833ZM5.25 21.7083H23.75L17.9688 14L13.3438 20.1667L9.875 15.5417L5.25 21.7083ZM9.10417 10.9167C9.74653 10.9167 10.2925 10.6918 10.7422 10.2422C11.1918 9.79253 11.4167 9.24653 11.4167 8.60417C11.4167 7.96181 11.1918 7.4158 10.7422 6.96615C10.2925 6.51649 9.74653 6.29167 9.10417 6.29167C8.46181 6.29167 7.9158 6.51649 7.46615 6.96615C7.01649 7.4158 6.79167 7.96181 6.79167 8.60417C6.79167 9.24653 7.01649 9.79253 7.46615 10.2422C7.9158 10.6918 8.46181 10.9167 9.10417 10.9167Z"
                            fill="#4C7660" />
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
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
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
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.galleries.edit',
                                                    gallery.slug
                                                )
                                            "
                                        >
                                            <PrimaryButton>
                                                Edit Gallery
                                            </PrimaryButton>
                                        </Link>
                                        <DangerButton
                                            @click="deleteGallery(gallery.slug)"
                                        >
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
