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

const deleteNews = (slug) => {
    if (confirm("Are you sure you want to delete this news?")) {
        form.delete(route("dashboard.news.destroy", slug), {
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
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M160-120q-33 0-56.5-23.5T80-200v-640l67 67 66-67 67 67 67-67 66 67 67-67 67 67 66-67 67 67 67-67 66 67 67-67v640q0 33-23.5 56.5T800-120H160Zm0-80h280v-240H160v240Zm360 0h280v-80H520v80Zm0-160h280v-80H520v80ZM160-520h640v-120H160v120Z" />
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
                <div class="overflow-hidden bg-white shadow-sm  ">
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
                                            news.slug
                                        )
                                            ">
                                        <PrimaryButton>
                                            Edit News
                                        </PrimaryButton>
                                        </Link>
                                        <DangerButton @click="deleteNews(news.slug)" class="ml-2">
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
