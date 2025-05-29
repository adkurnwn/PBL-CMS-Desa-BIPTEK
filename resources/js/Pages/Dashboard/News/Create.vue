<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import RichText from "@/Components/DashboardComponent/RichText.vue";
import CategoryInput from "@/Components/DashboardComponent/CategoryInput.vue";
import ImageUploader from "@/Components/DashboardComponent/ImageUploader.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";
import { watch, ref } from 'vue';
import PreviewBerita from "@/Components/DashboardComponent/PreviewBerita.vue";
import CheckboxField from "@/Components/DashboardComponent/CheckboxField.vue";
import VideoUploader from "@/Components/DashboardComponent/VideoUploader.vue";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: "",
    content: "",
    category: {
        category_id: "",
        new_category: "",
        edit_category_id: "",
        new_category_name: "",
    },
    video: null,
    images: [],
    alts: [],
    is_moderate: true,
    is_draft: false,
});

const richTextRef = ref(null)
const imageToPlaceholderMap = ref(new Map()) // Track image file to placeholder mapping

// Initialize TipTap editor
const editor = useEditor({
    content: form.content,
    extensions: [
        StarterKit,
    ],
    onUpdate: ({ editor }) => {
        form.content = editor.getHTML();
    },
});

// Watch for changes in form.content to update editor
watch(() => form.content, (newContent) => {
    if (editor.value && editor.value.getHTML() !== newContent) {
        editor.value.commands.setContent(newContent);
    }
});

// Handle rich text editor changes
const handleRichTextChange = (richTextData) => {
    form.title = richTextData.title;
    form.content = richTextData.content;
    form.images = richTextData.images;
    form.alts = richTextData.alts;
};

// Handle images added from RichText
const handleRichTextImageAdded = (imageFile) => {
    // Generate placeholder ID
    const placeholderId = `image_placeholder_${Date.now()}`

    // Add to images array for ImageUploader
    const newImages = [...form.images, imageFile]
    const newAlts = [...form.alts, ''] // Empty string instead of filename

    form.images = newImages
    form.alts = newAlts

    // Store mapping for later replacement
    imageToPlaceholderMap.value.set(newImages.length - 1, placeholderId)
}

// Process content before submission to replace image URLs with placeholders
const processContentForSubmission = (content) => {
    let processedContent = content

    // Replace blob URLs and images with data-is-placeholder="true" with indexed placeholders
    let imageIndex = 0

    // First, replace images with data-is-placeholder attribute
    const imgWithPlaceholderRegex = /<img[^>]*data-is-placeholder="true"[^>]*>/g
    processedContent = processedContent.replace(imgWithPlaceholderRegex, (match) => {
        const placeholder = `{{IMAGE_${imageIndex}}}`
        imageIndex++
        return placeholder
    })

    // Also replace any remaining blob URLs that might not have the data attribute
    const blobUrlRegex = /<img[^>]*src="blob:[^"]*"[^>]*>/g
    processedContent = processedContent.replace(blobUrlRegex, (match) => {
        // Only replace if it doesn't already have a placeholder
        if (!match.includes('{{IMAGE_')) {
            const placeholder = `{{IMAGE_${imageIndex}}}`
            imageIndex++
            return placeholder
        }
        return match
    })

    return processedContent
}

// Add preview modal state
const showPreview = ref(false)

// Add preview function
const openPreview = () => {
    showPreview.value = true
}

const closePreview = () => {
    showPreview.value = false
}

// Modified submit function
const submit = (isDraft = false) => {
    form.is_draft = isDraft

    // Process content to replace image placeholders
    const originalContent = form.content
    form.content = processContentForSubmission(originalContent)

    form.post(route("dashboard.news.store"), {
        onSuccess: () => {
            form.reset()
            imageToPlaceholderMap.value.clear()
        },
        onError: () => {
            // Restore original content on error
            form.content = originalContent
        },
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>

    <Head title="Buat Berita" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Buat Berita" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M160-120q-33 0-56.5-23.5T80-200v-640l67 67 66-67 67 67 67-67 66 67 67-67 67 67 66-67 67 67 67-67 66 67 67-67v640q0 33-23.5 56.5T800-120H160Zm0-80h280v-240H160v240Zm360 0h280v-80H520v80Zm0-160h280v-80H520v80ZM160-520h640v-120H160v120Z" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Buat Berita Baru
        </template>

        <template #search-area>
            <Link :href="route('dashboard.news.index')">
            <SecondaryButton type="button">
                Batal
            </SecondaryButton>
            </Link>
            <SecondaryButton type="button" :disabled="form.processing" @click="submit(true)">
                simpan draft
            </SecondaryButton>
            <SecondaryButton type="button" @click="openPreview">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-selectedText" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </template>
                preview
            </SecondaryButton>
            <PrimaryButton type="button" :disabled="form.processing" @click="submit(false)">
                Post Berita
            </PrimaryButton>
        </template>

        <template #border-shadow>
            
        </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <div class="overflow-hidden bg-white">
                <div class="">
                    <form enctype="multipart/form-data">
                        <div class="grid grid-cols-2 gap-4">

                            <!-- Name Field -->
                            <div>
                                <SingleLineField id="title" v-model="form.title" type="text" class="mt-1 block w-full"
                                    fieldName="Judul Berita" required />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>

                            <CategoryInput fieldName="Kategori" v-model="form.category" :categories="categories"
                                :errors="form.errors" />

                            <!-- Updated Checkbox Field -->
                            <div>
                                <CheckboxField id="is_moderate" v-model="form.is_moderate" fieldName="Moderasi Komentar"
                                    labelText="Hidupkan moderasi komentar untuk Berita ini" custom-width="100%" />
                                <InputError :message="form.errors.is_moderate" class="mt-2" />
                            </div>


                            <div
                                class="col-span-2 p-[3.5px] sm:p-[4px] md:p-[4.5px] lg:p-[4.8px] xl:p-[5.3px] 2xl:p-[6px] 3xl:p-[7px] 4xl:p-[8px]">
                                <RichText type="for_create" ref="richTextRef" v-model="form.content"
                                    @image-added="handleRichTextImageAdded" />
                                <InputError :message="form.errors.content" class="mt-2" />
                            </div>

                            <!-- ImageUploader handles all images but hides upload box for news -->
                            <ImageUploader v-model="form.images" v-model:alts="form.alts" label="Images" type="news"
                                :error="form.errors" :alt-error="form.errors.alts" :defaultAltText="form.title"
                                :required="true" :maxImages="10" />

                            <VideoUploader 
                                v-model="form.video" 
                                label="Video Berita" 
                                :error="form.errors.video"
                                :required="false"
                            />

                        </div>

                        <div class="mt-6 flex space-x-4">

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <PreviewBerita :isOpen="showPreview" :newsData="form" :categories="categories" @close="closePreview" />
    </AuthenticatedLayout>
</template>
