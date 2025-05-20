<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

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
            <HeadingMenu title="Saran & Keluhan" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="33" viewBox="0 0 32 33" fill="none">
                        <path
                            d="M3.70833 31.4163C2.86042 31.4163 2.13455 31.1144 1.53073 30.5106C0.92691 29.9068 0.625 29.1809 0.625 28.333V6.74967C0.625 5.90176 0.92691 5.17589 1.53073 4.57207C2.13455 3.96825 2.86042 3.66634 3.70833 3.66634H5.25V0.583008H8.33333V3.66634H20.6667V0.583008H23.75V3.66634H25.2917C26.1396 3.66634 26.8655 3.96825 27.4693 4.57207C28.0731 5.17589 28.375 5.90176 28.375 6.74967V13.957C28.375 14.3938 28.2273 14.7599 27.9318 15.0554C27.6363 15.3509 27.2701 15.4986 26.8333 15.4986C26.3965 15.4986 26.0304 15.3509 25.7349 15.0554C25.4394 14.7599 25.2917 14.3938 25.2917 13.957V12.9163H3.70833V28.333H12.65C13.0868 28.333 13.453 28.4808 13.7484 28.7762C14.0439 29.0717 14.1917 29.4379 14.1917 29.8747C14.1917 30.3115 14.0439 30.6776 13.7484 30.9731C13.453 31.2686 13.0868 31.4163 12.65 31.4163H3.70833ZM23.75 32.958C21.6174 32.958 19.7995 32.2064 18.2964 30.7033C16.7932 29.2002 16.0417 27.3823 16.0417 25.2497C16.0417 23.117 16.7932 21.2992 18.2964 19.796C19.7995 18.2929 21.6174 17.5413 23.75 17.5413C25.8826 17.5413 27.7005 18.2929 29.2036 19.796C30.7068 21.2992 31.4583 23.117 31.4583 25.2497C31.4583 27.3823 30.7068 29.2002 29.2036 30.7033C27.7005 32.2064 25.8826 32.958 23.75 32.958ZM26.3323 28.9111L27.4115 27.832L24.5208 24.9413V20.6247H22.9792V25.558L26.3323 28.9111Z"
                            fill="#4C7660" />
                    </svg>

                </template>
            </HeadingMenu>
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
