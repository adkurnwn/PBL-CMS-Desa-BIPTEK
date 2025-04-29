<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    comments: Object, // Will contain paginated comments data
});

const form = useForm({});

const approveComment = (commentId) => {
    if (confirm("Are you sure you want to approve this comment?")) {
        form.patch(route("dashboard.comments.approve", commentId), {
            preserveScroll: true,
        });
    }
};

const deleteComment = (commentId) => {
    if (confirm("Are you sure you want to delete this comment?")) {
        form.delete(route("dashboard.comments.destroy", commentId), {
            preserveScroll: true,
        });
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Comment Moderation" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Comment Moderation
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Comments List -->
                        <div class="space-y-6">
                            <div v-for="comment in comments.data" :key="comment.id" 
                                 class="bg-gray-50 p-6 rounded-lg shadow">
                                <!-- Comment Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-semibold text-lg">
                                            {{ comment.nama_user }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            on post: {{ comment.news?.title || 'Deleted Post' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ formatDate(comment.created_at) }}
                                        </p>
                                    </div>
                                    <div class="space-x-2">
                                        <PrimaryButton 
                                            v-if="!comment.is_approved"
                                            @click="approveComment(comment.id)"
                                            class="mb-2"
                                        >
                                            Approve
                                        </PrimaryButton>
                                        <DangerButton 
                                            @click="deleteComment(comment.id)"
                                        >
                                            Delete
                                        </DangerButton>
                                    </div>
                                </div>

                                <!-- Comment Content -->
                                <div class="prose max-w-none">
                                    <p class="text-gray-700">{{ comment.komentar }}</p>
                                </div>

                                <!-- Parent Comment Reference -->
                                <div v-if="comment.parent" class="mt-4 pl-4 border-l-4 border-gray-200">
                                    <p class="text-sm text-gray-500">In reply to:</p>
                                    <p class="text-gray-600">{{ comment.parent.komentar }}</p>
                                </div>

                                <!-- Status Badge -->
                                <div class="mt-4">
                                    <span 
                                        class="px-2 py-1 text-xs rounded-full"
                                        :class="comment.is_approved ? 
                                            'bg-green-100 text-green-800' : 
                                            'bg-yellow-100 text-yellow-800'"
                                    >
                                        {{ comment.is_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div v-if="comments.data.length === 0" 
                                 class="text-center py-12 text-gray-500">
                                No comments to moderate
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="comments.last_page > 1" class="mt-6">
                            <nav class="flex items-center justify-between">
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <Link 
                                        v-if="comments.prev_page_url"
                                        :href="comments.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                    >
                                        Previous
                                    </Link>
                                    <Link 
                                        v-if="comments.next_page_url"
                                        :href="comments.next_page_url"
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