<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import { ref, onMounted } from "vue";

const props = defineProps({
    feedback: Object,
});

const form = useForm({
    tanggapan: props.feedback.tanggapan || "",
});

const buktiFiles = ref([]);

onMounted(() => {
    if (props.feedback.bukti) {
        try {
            buktiFiles.value = JSON.parse(props.feedback.bukti);
        } catch (e) {
            console.error("Error parsing bukti JSON:", e);
        }
    }
});

const submitResponse = () => {
    form.post(route('dashboard.feedbacks.respond', props.feedback.id), {
        preserveScroll: true,
    });
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

// Helper function to get file type
const getFileType = (path) => {
    const extension = path.split(".").pop().toLowerCase();
    return ["jpg", "jpeg", "png"].includes(extension) ? "image" : "pdf";
};
</script>

<template>
    <Head title="Feedback Detail" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Feedback Detail
                </h2>
                <Link
                    :href="route('dashboard.feedbacks.index')"
                    class="text-blue-600 hover:text-blue-800"
                >
                    ← Back to List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm  ">
                    <div class="p-6">
                        <!-- Feedback Details -->
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold mb-4">
                                {{ feedback.judul }}
                            </h3>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-gray-600">From:</p>
                                    <p class="font-semibold">
                                        {{ feedback.nama_user }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ feedback.email }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Submitted on:</p>
                                    <p>{{ formatDate(feedback.created_at) }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2 mb-4">
                                <span
                                    class="px-2 py-1 text-sm rounded-full"
                                    :class="
                                        feedback.status === 'Ditanggapi'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-yellow-100 text-yellow-800'
                                    "
                                >
                                    {{ feedback.status }}
                                </span>
                                <span
                                    class="px-2 py-1 text-sm rounded-full"
                                    :class="
                                        feedback.kategori === 'Saran'
                                            ? 'bg-blue-100 text-blue-800'
                                            : 'bg-red-100 text-red-800'
                                    "
                                >
                                    {{ feedback.kategori }}
                                </span>
                            </div>
                            <div class="prose max-w-none">
                                <p class="text-gray-700 whitespace-pre-line">
                                    {{ feedback.isi }}
                                </p>

                                <!-- Attachments Section -->
                                <div v-if="buktiFiles.length > 0" class="mt-6">
                                    <h4 class="text-lg font-semibold mb-4">
                                        Attachments
                                    </h4>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"
                                    >
                                        <div
                                            v-for="(file, index) in buktiFiles"
                                            :key="index"
                                            class="relative"
                                        >
                                            <!-- Image Preview -->
                                            <div
                                                v-if="
                                                    getFileType(file) ===
                                                    'image'
                                                "
                                                class="relative aspect-video"
                                            >
                                                <img
                                                    :src="`/storage/${file}`"
                                                    :alt="`Attachment ${
                                                        index + 1
                                                    }`"
                                                    class="rounded-lg object-cover w-full h-full shadow-sm"
                                                />
                                            </div>

                                            <!-- PDF Preview -->
                                            <div
                                                v-else
                                                class="relative aspect-video bg-gray-100 rounded-lg flex items-center justify-center"
                                            >
                                                <div class="text-center">
                                                    <svg
                                                        class="w-12 h-12 mx-auto text-gray-400"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                                        ></path>
                                                    </svg>
                                                    <span
                                                        class="mt-2 block text-sm text-gray-500"
                                                        >PDF Document</span
                                                    >
                                                </div>
                                            </div>

                                            <!-- Download Link -->
                                            <a
                                                :href="`/storage/${file}`"
                                                target="_blank"
                                                class="mt-2 text-sm text-blue-600 hover:text-blue-800 block"
                                            >
                                                View Full Size
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Response Form -->
                        <div class="mt-8 border-t pt-8">
                            <h4 class="text-lg font-semibold mb-4">Response</h4>
                            <form @submit.prevent="submitResponse">
                                <div class="mb-4">
                                    <textarea
                                        v-model="form.tanggapan"
                                        :disabled="
                                            feedback.status === 'Ditanggapi'
                                        "
                                        rows="4"
                                        placeholder="Enter your response..."
                                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm disabled:opacity-50"
                                    ></textarea>
                                </div>
                                <PrimaryButton
                                    :disabled="feedback.status === 'Ditanggapi'"
                                    type="submit"
                                >
                                    Submit Response
                                </PrimaryButton>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
