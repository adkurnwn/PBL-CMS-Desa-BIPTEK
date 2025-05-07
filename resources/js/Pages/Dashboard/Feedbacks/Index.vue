<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    feedbacks: Object,
});

const form = useForm({});

const deleteFeedback = (id) => {
    if (confirm("Are you sure you want to delete this feedback?")) {
        form.delete(route("dashboard.feedbacks.destroy", id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>

<template>
    <Head title="Feedback Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Feedback Management
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Feedbacks List -->
                        <div class="space-y-6">
                            <div
                                v-for="feedback in feedbacks.data"
                                :key="feedback.id"
                                class="bg-gray-50 p-6 rounded-lg shadow"
                            >
                                <!-- Feedback Header -->
                                <div
                                    class="flex justify-between items-start mb-4"
                                >
                                    <div>
                                        <h3 class="font-semibold text-lg">
                                            {{ feedback.judul }}
                                        </h3>
                                        <div
                                            class="flex space-x-4 text-sm text-gray-500"
                                        >
                                            <p>By: {{ feedback.nama_user }}</p>
                                            <p>
                                                {{
                                                    formatDate(
                                                        feedback.created_at
                                                    )
                                                }}
                                            </p>
                                        </div>
                                        <div class="mt-1">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full"
                                                :class="
                                                    feedback.status ===
                                                    'Ditanggapi'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-yellow-100 text-yellow-800'
                                                "
                                            >
                                                {{ feedback.status }}
                                            </span>
                                            <span
                                                class="ml-2 px-2 py-1 text-xs rounded-full"
                                                :class="
                                                    feedback.kategori ===
                                                    'Saran'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : 'bg-red-100 text-red-800'
                                                "
                                            >
                                                {{ feedback.kategori }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-x-2">
                                        <Link
                                            :href="route('dashboard.feedbacks.show', feedback.id)"
                                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700"
                                        >
                                            Tanggapi
                                        </Link>
                                    </div>
                                </div>

                                <!-- Feedback Content -->
                                <div class="prose max-w-none">
                                    <p class="text-gray-700">
                                        {{ feedback.isi }}
                                    </p>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div
                                v-if="feedbacks.data.length === 0"
                                class="text-center py-12 text-gray-500"
                            >
                                No feedbacks available
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="feedbacks.last_page > 1" class="mt-6">
                            <nav class="flex items-center justify-between">
                                <div
                                    class="flex justify-between flex-1 sm:hidden"
                                >
                                    <Link
                                        v-if="feedbacks.prev_page_url"
                                        :href="feedbacks.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="feedbacks.next_page_url"
                                        :href="feedbacks.next_page_url"
                                        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                    >
                                        Next
                                    </Link>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
