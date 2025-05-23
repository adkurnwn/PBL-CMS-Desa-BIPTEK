<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const props = defineProps({
    news: Array,
});

const form = useForm({});

const deleteNews = (slug) => {
    if (confirm("Are you sure you want to delete this news?")) {
        form.delete(route("dashboard.news.destroy", slug), {
            preserveScroll: true,
        });
    }
};
</script>

<template>

    <Head title="Arsip Berita" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Arsip Berita" :hasRightContent="true">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M160-120q-33 0-56.5-23.5T80-200v-640l67 67 66-67 67 67 67-67 66 67 67-67 67 67 66-67 67 67 67-67 66 67 67-67v640q0 33-23.5 56.5T800-120H160Zm0-80h280v-240H160v240Zm360 0h280v-80H520v80Zm0-160h280v-80H520v80ZM160-520h640v-120H160v120Z" />
                    </svg>

                </template>
                <template #right-content>
                    <Link :href="route('dashboard.news.index')">
                    <PrimaryButton>List News</PrimaryButton>
                    </Link>
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
                                <tr v-for="news in news" :key="news.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ news.title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <DangerButton @click="deleteNews(news.slug)">
                                            Delete
                                        </DangerButton>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link :href="route(
                                            'dashboard.news.restore',
                                            news.slug
                                        )
                                            ">
                                        <PrimaryButton>Restore</PrimaryButton>
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
