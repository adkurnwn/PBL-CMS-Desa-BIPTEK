<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { ref, onMounted } from "vue";
import { computed } from "vue";
import TextAreaInput from "@/Components/DashboardComponent/TextAreaInput.vue";
import Labels from "@/Components/DashboardComponent/Labels.vue";
import ImageDetail from "@/Components/DashboardComponent/ImageDetail.vue";
import FilePreview from "@/Components/DashboardComponent/FilePreview.vue";

const props = defineProps({
    feedback: Object,
});

const form = useForm({
    tanggapan: props.feedback.tanggapan || "",
});

const buktiFiles = ref([]);
const modalOpen = ref(false);
const selectedImage = ref('');
const selectedImageAlt = ref('');
const filePreviewOpen = ref(false);
const selectedFileUrl = ref('');
const selectedFileName = ref('');

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

const getAvatar = (nama_user) => {
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(
        nama_user
    )}&background=random`;
};

const avatarUrl = computed(() => {
    return getAvatar(props.feedback.nama_user)
})

const openImageModal = (imageSrc, imageAlt) => {
    selectedImage.value = imageSrc;
    selectedImageAlt.value = imageAlt;
    modalOpen.value = true;
};

const closeImageModal = () => {
    modalOpen.value = false;
    selectedImage.value = '';
    selectedImageAlt.value = '';
};

const openFilePreview = (fileUrl, fileName) => {
    selectedFileUrl.value = fileUrl;
    selectedFileName.value = fileName;
    filePreviewOpen.value = true;
};

const closeFilePreview = () => {
    filePreviewOpen.value = false;
    selectedFileUrl.value = '';
    selectedFileName.value = '';
};
</script>

<template>

    <Head title="Feedback Detail" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Balas Saran & Keluhan" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#314D1C">
                        <path
                            d="m363-390 117-71 117 71-31-133 104-90-137-11-53-126-53 126-137 11 104 90-31 133ZM80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Balas Saran & Keluhan
        </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <div class="overflow-hidden bg-white">
                <div class="ml-[5px] sm:ml-[6px] md:ml-[8px] xl:ml-[6px] 2xl:ml-[8px] 3xl:ml-[9px] 4xl:ml-[10px] ">
                    <!-- Feedback Details -->
                    <div class="mb-8">
                        <h2
                            class="mb-4 py-2 px-2 rounded-sm w-full sm:rounded-sm md:rounded lg:rounded-r-md xl:rounded-lg 2xl:rounded-xl 3xl:rounded-2xl 4xl:rounded-md bg-selectedBG bg-opacity-50 text-[18px] sm:text-[18px] md:text-[18px] xl:text-[20px] 2xl:text-[23px] 3xl:text-[27px] 4xl:text-[31px] font-semibold leading-tight text-selectedText">
                            {{ feedback.judul }}
                        </h2>
                        <div class="flex gap-2 mb-4">
                            <Labels :type="feedback.status === 'Ditanggapi' ? 'ditanggapi' : 'menunggu'"
                                :text="feedback.status" />
                            <Labels :type="feedback.kategori === 'Saran' ? 'saran' : 'keluhan'"
                                :text="feedback.kategori" />
                        </div>
                        <div class="grid grid-cols-2 mb-4">
                            <div>
                                <div
                                    class="flex flex-row items-center justify-start gap-[7px] sm:gap-[7px] md:gap-[7px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px]">
                                    <div
                                        class="w-[30px] h-[30px] sm:w-[32px] sm:h-[32px] md:w-[35px] md:h-[35px] lg:w-[39px] lg:h-[39px] xl:w-[44px] xl:h-[44px] 2xl:w-[50px] 2xl:h-[50px] 3xl:w-[58px] 3xl:h-[58px] 4xl:w-[66px] 4xl:h-[66px] rounded-full overflow-hidden flex-shrink-0">
                                        <img :src="avatarUrl" :alt="feedback.nama_user" class="w-full h-full object-cover"
                                            @error="handleImageError" />
                                    </div>
                                    <div class="flex-1 relative ">
                                        <div
                                            class="font-semibold text-[9.3px] sm:text-[10.6px] md:text-[12px] lg:text-[13.3px] xl:text-[14.6px] 2xl:text-[16.7px] 3xl:text-[19.4px] 4xl:text-[22px]">
                                            {{ feedback.nama_user }}
                                        </div>
                                        <div
                                            class="font-medium text-menuText text-[9.3px] sm:text-[10.6px] md:text-[12px] lg:text-[13.3px] xl:text-[14.6px] 2xl:text-[16.7px] 3xl:text-[19.4px] 4xl:text-[22px] italic">
                                            {{ feedback.email }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-selectedText text-[9.3px] sm:text-[10.6px] md:text-[12px] lg:text-[13.3px] xl:text-[14.6px] 2xl:text-[16.7px] 3xl:text-[19.4px] 4xl:text-[22px]">
                                    Dikirim pada:</p>
                                <p
                                    class="text-[#404DBD] text-[9.3px] sm:text-[10.6px] md:text-[12px] lg:text-[13.3px] xl:text-[14.6px] 2xl:text-[16.7px] 3xl:text-[19.4px] 4xl:text-[22px]">
                                    {{ formatDate(feedback.created_at) }}</p>
                            </div>
                        </div>
                        <div class="prose max-w-none">
                            <!-- Attachments Section -->
                            <div v-if="buktiFiles.length > 0" class="mb-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                    <div v-for="(file, index) in buktiFiles" :key="index" class="relative">
                                        <!-- Image Preview -->
                                        <div v-if="
                                            getFileType(file) ===
                                            'image'
                                        " class="relative aspect-video cursor-pointer" @click="openImageModal(`/storage/${file}`, `Attachment ${index + 1}`)">
                                            <img :src="`/storage/${file}`" :alt="`Attachment ${index + 1
                                                }`" class="rounded-lg object-cover w-full h-full shadow-sm hover:opacity-90 transition-opacity" />
                                        </div>

                                        <!-- PDF Preview -->
                                        <div v-else
                                            class="relative aspect-video bg-gray-100 rounded-lg overflow-hidden cursor-pointer group" 
                                            @click="openFilePreview(`/storage/${file}`, file.split('/').pop())">
                                            <iframe
                                                :src="`/storage/${file}#toolbar=0&navpanes=0&scrollbar=0`"
                                                class="w-full h-full border-0 pointer-events-none"
                                                type="application/pdf"
                                            >
                                            </iframe>
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-200 flex items-center justify-center">
                                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-white bg-opacity-90 rounded-full p-2">
                                                    <svg class="w-6 h-6 text-selectedText" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="absolute bottom-2 left-2 bg-selectedBG bg-opacity-70 text-selectedText text-xs px-2 py-1 rounded">
                                                File PDF
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="rounded-r-sm w-full sm:rounded-r-sm md:rounded-r lg:rounded-r-md xl:rounded-r-lg 2xl:rounded-r-xl 3xl:rounded-r-2xl 4xl:rounded-r-md bg-gray-200 bg-opacity-50 border-l border-[#6f7684] overflow-hidden flex flex-col items-start justify-center p-[2.6px] sm:p-[3px] md:p-[3.4px] lg:p-[3.6px] xl:p-[4px] 2xl:p-[4.6px] 3xl:p-[5.4px] 4xl:p-[6px] text-[9.3px] sm:text-[10.6px] md:text-[12px] lg:text-[13.3px] xl:text-[14.6px] 2xl:text-[16.7px] 3xl:text-[19.4px] 4xl:text-[22px]">
                                <span class="self-stretch relative font-light">{{ feedback.isi }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Response Form -->
                    <div class="mt-4 border-t pt-4">
                        <h4 class="text-[9.3px] sm:text-[10.6px] md:text-[12px] lg:text-[13.3px] xl:text-[14.6px] 2xl:text-[16.7px] 3xl:text-[19.4px] 4xl:text-[22px] text-selectedText font-semibold mb-4">Tanggapan</h4>
                        <form @submit.prevent="submitResponse">
                            <div class="mb-4">
                                <TextAreaInput class="!p-0" v-model="form.tanggapan" :disabled="feedback.status === 'Ditanggapi'
                                    " placeholder="Masukkan tanggapan..."></TextAreaInput>
                            </div>
                            <div class="flex justify-end">
                                <PrimaryButton :disabled="feedback.status === 'Ditanggapi'" type="submit">
                                    Kirim Tanggapan
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>

                    <FilePreview 
                        :isOpen="filePreviewOpen"
                        :fileUrl="selectedFileUrl"
                        :fileName="selectedFileName"
                        @close="closeFilePreview"
                    />

                    <ImageDetail 
                        :isOpen="modalOpen" 
                        :imageSrc="selectedImage" 
                        :imageAlt="selectedImageAlt" 
                        @close="closeImageModal" 
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
