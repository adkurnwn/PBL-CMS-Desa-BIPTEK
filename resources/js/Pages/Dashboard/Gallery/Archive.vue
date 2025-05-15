<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

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
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Archived Gallery
                </h2>
                <Link :href="route('dashboard.galleries.index')">
                    <PrimaryButton>List Gallery</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Judul
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
                                    v-for="gallery in galleries"
                                    :key="gallery.id"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ gallery.title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <DangerButton
                                            @click="deleteGallery(gallery.slug)"
                                        >
                                            Delete
                                        </DangerButton>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.galleries.restore',
                                                    gallery.slug
                                                )
                                            "
                                        >
                                            <PrimaryButton
                                                >Restore</PrimaryButton
                                            >
                                        </Link>
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
