<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ref, computed, watch } from "vue";
import TextInput from "@/Components/TextInput.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const props = defineProps({
    news: Array,
});

const form = useForm({});
const search = ref("");
const sortField = ref("created_at");
const sortDirection = ref("desc");

const deleteNews = (newsId) => {
    if (confirm("Are you sure you want to delete this news?")) {
        form.delete(route("dashboard.news.destroy", newsId), {
            preserveScroll: true,
        });
    }
};

const sortBy = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
};

const filteredNews = computed(() => {
    let result = [...props.news];

    // Apply search filter
    if (search.value) {
        const searchLower = search.value.toLowerCase();
        result = result.filter(
            (item) =>
                item.title.toLowerCase().includes(searchLower) ||
                (item.content &&
                    item.content.toLowerCase().includes(searchLower))
        );
    }

    // Apply sorting
    result.sort((a, b) => {
        let aValue = a[sortField.value];
        let bValue = b[sortField.value];

        // Handle date fields
        if (
            sortField.value === "created_at" ||
            sortField.value === "updated_at"
        ) {
            aValue = new Date(aValue);
            bValue = new Date(bValue);
        }

        if (aValue < bValue) return sortDirection.value === "asc" ? -1 : 1;
        if (aValue > bValue) return sortDirection.value === "asc" ? 1 : -1;
        return 0;
    });

    return result;
});
</script>

<template>

    <Head title="News" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Berita" :hasRightContent="true">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="31" height="28" viewBox="0 0 31 28" fill="none">
                        <path
                            d="M3.16634 27.875C2.31842 27.875 1.59256 27.5731 0.988737 26.9693C0.384918 26.3655 0.0830078 25.6396 0.0830078 24.7917V3.20833C0.0830078 2.36042 0.384918 1.63455 0.988737 1.03073C1.59256 0.42691 2.31842 0.125 3.16634 0.125H27.833C28.6809 0.125 29.4068 0.42691 30.0106 1.03073C30.6144 1.63455 30.9163 2.36042 30.9163 3.20833V24.7917C30.9163 25.6396 30.6144 26.3655 30.0106 26.9693C29.4068 27.5731 28.6809 27.875 27.833 27.875H3.16634ZM6.24967 21.7083H24.7497V18.625H6.24967V21.7083ZM6.24967 15.5417H12.4163V6.29167H6.24967V15.5417ZM15.4997 15.5417H24.7497V12.4583H15.4997V15.5417ZM15.4997 9.375H24.7497V6.29167H15.4997V9.375Z"
                            fill="#4C7660" />
                    </svg>

                </template>
                <template #right-content>
                    <div class="flex space-x-2">
                        <Link :href="route('dashboard.news.archive')">
                        <PrimaryButton>Archived News</PrimaryButton>
                        </Link>
                        <Link :href="route('dashboard.news.create')">
                        <PrimaryButton>Create New News</PrimaryButton>
                        </Link>
                    </div>
                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Search and Sort Controls -->
                        <div class="mb-4 flex justify-between items-center">
                            <div class="w-1/3">
                                <TextInput v-model="search" type="text" placeholder="Search news..." class="w-full" />
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-600">Sort by:</span>
                                <select v-model="sortField"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="title">Title</option>
                                    <option value="created_at">
                                        Date Created
                                    </option>
                                    <option value="updated_at">
                                        Date Updated
                                    </option>
                                </select>
                                <button @click="
                                    sortDirection =
                                    sortDirection === 'asc'
                                        ? 'desc'
                                        : 'asc'
                                    " class="p-2 border rounded-md">
                                    <span v-if="sortDirection === 'asc'">↑</span>
                                    <span v-else>↓</span>
                                </button>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th @click="sortBy('title')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50">
                                        Judul
                                        <span v-if="sortField === 'title'">
                                            {{
                                                sortDirection === "asc"
                                                    ? "↑"
                                                    : "↓"
                                            }}
                                        </span>
                                    </th>
                                    <th @click="sortBy('created_at')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50">
                                        Date
                                        <span v-if="sortField === 'created_at'">
                                            {{
                                                sortDirection === "asc"
                                                    ? "↑"
                                                    : "↓"
                                            }}
                                        </span>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="news in filteredNews" :key="news.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ news.title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{
                                            new Date(
                                                news.created_at
                                            ).toLocaleDateString()
                                        }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link :href="route(
                                            'dashboard.news.edit',
                                            news.id
                                        )
                                            ">
                                        <PrimaryButton>
                                            Edit News
                                        </PrimaryButton>
                                        </Link>
                                        <DangerButton @click="deleteNews(news.id)" class="ml-2">
                                            Delete
                                        </DangerButton>
                                    </td>
                                </tr>
                                <tr v-if="filteredNews.length === 0">
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                        No news found. Please try a different
                                        search term or create a new news item.
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
