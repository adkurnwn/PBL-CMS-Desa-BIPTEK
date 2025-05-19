<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const props = defineProps({
    news: Array,
});

const form = useForm({});

const deleteNews = (newsId) => {
    if (confirm("Are you sure you want to delete this news?")) {
        form.delete(route("dashboard.news.destroy", newsId), {
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="31" height="28" viewBox="0 0 31 28" fill="none">
                        <path
                            d="M3.16634 27.875C2.31842 27.875 1.59256 27.5731 0.988737 26.9693C0.384918 26.3655 0.0830078 25.6396 0.0830078 24.7917V3.20833C0.0830078 2.36042 0.384918 1.63455 0.988737 1.03073C1.59256 0.42691 2.31842 0.125 3.16634 0.125H27.833C28.6809 0.125 29.4068 0.42691 30.0106 1.03073C30.6144 1.63455 30.9163 2.36042 30.9163 3.20833V24.7917C30.9163 25.6396 30.6144 26.3655 30.0106 26.9693C29.4068 27.5731 28.6809 27.875 27.833 27.875H3.16634ZM6.24967 21.7083H24.7497V18.625H6.24967V21.7083ZM6.24967 15.5417H12.4163V6.29167H6.24967V15.5417ZM15.4997 15.5417H24.7497V12.4583H15.4997V15.5417ZM15.4997 9.375H24.7497V6.29167H15.4997V9.375Z"
                            fill="#4C7660" />
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
                                <tr v-for="news in news" :key="news.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ news.title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <DangerButton
                                            @click="deleteNews(news.id)"
                                        >
                                            Delete
                                        </DangerButton>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.news.restore',
                                                    news.id
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
