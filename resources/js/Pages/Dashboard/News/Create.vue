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
import { watch, ref, computed } from 'vue';
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
    // Start from index 1 to skip the first image
    let imageIndex = 1

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

// Add title validation computed property
const titleError = computed(() => {
    if (!form.title) return null;
    
    // Check if title contains only symbols/special characters
    const hasAlphanumeric = /[a-zA-Z0-9\u00C0-\u017F\u0100-\u024F\u1E00-\u1EFF]/.test(form.title);
    
    if (!hasAlphanumeric) {
        return 'Judul berita tidak boleh hanya berisi simbol.';
    }
    
    return null;
});
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
            <SecondaryButton type="button" class="border border-dangerText !text-dangerText hover:!bg-dangerBG">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-dangerText">
                        <path
                            d="m336-280 144-144 144 144 56-56-144-144 144-144-56-56-144 144-144-144-56 56 144 144-144 144 56 56ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                    </svg>
                </template>
                Batal
            </SecondaryButton>
            </Link>
            <SecondaryButton type="button" :disabled="form.processing" @click="submit(true)">
                <template #icon>
                    <svg viewBox="0 0 33 34" fill="none" xmlns="http://www.w3.org/2000/svg" class="fill-selectedText">
                        <mask id="mask0_65_30" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="33"
                            height="34">
                            <rect x="0.267578" y="0.46756" width="32.7324" height="32.7324" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_65_30)">
                            <path
                                d="M19.1254 29.7904V26.1867L26.5294 18.8165C26.699 18.6558 26.8821 18.5387 27.0787 18.4653C27.2753 18.3919 27.472 18.3552 27.6686 18.3552C27.8832 18.3552 28.0906 18.3953 28.2908 18.4755C28.4913 18.556 28.6735 18.6767 28.8374 18.8376L30.099 20.1121C30.2458 20.2819 30.3595 20.4656 30.4399 20.6631C30.5204 20.8607 30.5606 21.0582 30.5606 21.2557C30.5606 21.4533 30.5239 21.6543 30.4505 21.8589C30.3771 22.0635 30.2599 22.2505 30.099 22.4201L22.7291 29.7904H19.1254ZM20.7515 28.1643H22.0471L26.4742 23.7158L25.8605 23.0546L25.2126 22.4283L20.7515 26.8687V28.1643ZM8.87029 29.7904C8.18131 29.7904 7.59815 29.5517 7.12081 29.0743C6.64346 28.597 6.40479 28.0138 6.40479 27.3249V6.3427C6.40479 5.65373 6.64346 5.07057 7.12081 4.59322C7.59815 4.11587 8.18131 3.8772 8.87029 3.8772H19.7023L26.8625 11.0374V14.8535H24.8168V12.0603H18.6794V5.92297H8.87029C8.76527 5.92297 8.66912 5.96673 8.58183 6.05424C8.49432 6.14153 8.45056 6.23768 8.45056 6.3427V27.3249C8.45056 27.4299 8.49432 27.526 8.58183 27.6133C8.66912 27.7008 8.76527 27.7446 8.87029 27.7446H16.1352V29.7904H8.87029ZM25.8605 23.0546L25.2126 22.4283L26.4742 23.7158L25.8605 23.0546Z" />
                        </g>
                    </svg>
                </template>
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
                <template #iconright>
                    <svg width="33" height="34" viewBox="0 0 33 34" class="fill-white" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_65_36" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="33"
                            height="34">
                            <rect x="0.267578" y="0.46756" width="32.7324" height="32.7324" />
                        </mask>
                        <g mask="url(#mask0_65_36)">
                            <path
                                d="M5.04102 26.7217V6.94586L28.5149 16.8338L5.04102 26.7217ZM7.08679 23.653L23.2484 16.8338L7.08679 10.0145V15.0502L14.483 16.8338L7.08679 18.6174V23.653Z"
                                fill="white" />
                        </g>
                    </svg>
                </template>
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
                                <InputError :message="titleError || form.errors.title" class="mt-2" />
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

                            <VideoUploader v-model="form.video" label="Video Berita" :error="form.errors.video"
                                :required="false" />

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
