<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const props = defineProps({
    comments: Object, // Will contain paginated comments data
});

const form = useForm({});

const approveComment = (commentId) => {
    if (confirm("Are you sure you want to approve this comment?")) {
        form.patch(
            route("dashboard.produk-hukum.index-comments.approve", commentId),
            {
                preserveScroll: true,
            }
        );
    }
};

const deleteComment = (commentId) => {
    if (confirm("Are you sure you want to delete this comment?")) {
        form.delete(
            route("dashboard.produk-hukum.index-comments.destroy", commentId),
            {
                preserveScroll: true,
            }
        );
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
    <Head title="Moderasi Komentar Produk Hukum" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Moderasi Komentar Produk Hukum" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="37" height="38" viewBox="0 0 37 38" fill="none">
                        <mask id="mask0_1448_7519" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="37" height="38">
                            <rect y="0.5" width="37" height="37" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1448_7519)">
                            <path
                                d="M27.75 32.8753L25.5917 30.717L28.0198 28.2503H21.5833V25.167H28.0198L25.5917 22.7003L27.75 20.542L33.9167 26.7087L27.75 32.8753ZM4.625 32.8753V9.75033C4.625 8.90241 4.92691 8.17654 5.53073 7.57272C6.13455 6.9689 6.86042 6.66699 7.70833 6.66699H26.2083C27.0562 6.66699 27.7821 6.9689 28.3859 7.57272C28.9898 8.17654 29.2917 8.90241 29.2917 9.75033V17.5743C29.0347 17.5229 28.7778 17.4908 28.5208 17.4779C28.2639 17.4651 28.0069 17.4587 27.75 17.4587C25.1549 17.4587 22.9644 18.358 21.1786 20.1566C19.3929 21.9552 18.5 24.1392 18.5 26.7087C18.5 26.9656 18.5064 27.2225 18.5193 27.4795C18.5321 27.7364 18.5642 27.9934 18.6156 28.2503H9.25L4.625 32.8753Z"
                                fill="#4C7660" />
                        </g>
                    </svg>

                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Comments List -->
                        <div class="space-y-6">
                            <div
                                v-for="comment in comments.data"
                                :key="comment.id"
                                class="bg-gray-50 p-6 rounded-lg shadow"
                            >
                                <!-- Comment Header -->
                                <div
                                    class="flex justify-between items-start mb-4"
                                >
                                    <div>
                                        <h3 class="font-semibold text-lg">
                                            {{ comment.nama_user }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            on document:
                                            {{
                                                comment.produk_hukum?.title ||
                                                "Deleted Document"
                                            }}
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
                                    <p class="text-gray-700">
                                        {{ comment.komentar }}
                                    </p>
                                </div>

                                <!-- Parent Comment Reference -->
                                <div
                                    v-if="comment.parent"
                                    class="mt-4 pl-4 border-l-4 border-gray-200"
                                >
                                    <p class="text-sm text-gray-500">
                                        In reply to:
                                    </p>
                                    <p class="text-gray-600">
                                        {{ comment.parent.komentar }}
                                    </p>
                                </div>

                                <!-- Status Badge -->
                                <div class="mt-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full"
                                        :class="
                                            comment.is_approved
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-yellow-100 text-yellow-800'
                                        "
                                    >
                                        {{
                                            comment.is_approved
                                                ? "Approved"
                                                : "Pending"
                                        }}
                                    </span>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div
                                v-if="comments.data.length === 0"
                                class="text-center py-12 text-gray-500"
                            >
                                No comments to moderate
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="comments.last_page > 1" class="mt-6">
                            <nav class="flex items-center justify-between">
                                <div
                                    class="flex justify-between flex-1 sm:hidden"
                                >
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
