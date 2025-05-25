<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, computed, onUnmounted, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import CategoryInput from "@/Components/DashboardComponent/CategoryInput.vue";
import ImageEditor from "@/Components/DashboardComponent/ImageEditor.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";
import RichText from "@/Components/DashboardComponent/RichText.vue";
import PreviewBerita from "@/Components/DashboardComponent/PreviewBerita.vue";
import CheckboxField from "@/Components/DashboardComponent/CheckboxField.vue";


const props = defineProps({
    news: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    unused_categories: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    title: props.news.title,
    category: {
        category_id: props.news.category_id,
        new_category: "",
        edit_category_id: "",
        new_category_name: "",
        deleting_category_id: [],
    },
    content: props.news.content,
    images: [],
    alts: [],
    removed_images: [],
    updated_existing_images: [],
    bulk_alt_text: "",
    video: null,
    is_moderate: Boolean(props.news.is_moderate),
    is_draft: Boolean(props.news.is_draft),
    _method: "PUT",
});

const richTextRef = ref(null);
const showPreview = ref(false);

const handleVideoChange = (e) => {
    form.video = e.target.files[0];
};

// Process content before submission to replace image URLs with placeholders
const processContentForSubmission = (content) => {
    let processedContent = content;
    let imageIndex = 0;

    // Replace images with data-is-placeholder attribute
    const imgWithPlaceholderRegex = /<img[^>]*data-is-placeholder="true"[^>]*>/g;
    processedContent = processedContent.replace(imgWithPlaceholderRegex, (match) => {
        const placeholder = `{{IMAGE_${imageIndex}}}`;
        imageIndex++;
        return placeholder;
    });

    // Replace remaining blob URLs
    const blobUrlRegex = /<img[^>]*src="blob:[^"]*"[^>]*>/g;
    processedContent = processedContent.replace(blobUrlRegex, (match) => {
        if (!match.includes('{{IMAGE_')) {
            const placeholder = `{{IMAGE_${imageIndex}}}`;
            imageIndex++;
            return placeholder;
        }
        return match;
    });

    return processedContent;
};

const openPreview = () => {
    showPreview.value = true;
};

const closePreview = () => {
    showPreview.value = false;
};

const submit = (isDraft = null) => {
    // Only update the draft status if explicitly passed
    if (isDraft !== null) {
        form.is_draft = isDraft;
    }

    // Handle image alt texts
    if (form.updated_existing_images && form.updated_existing_images.length) {
        form.updated_existing_images = form.updated_existing_images.map(
            (img) => {
                if (!img.alt || img.alt.trim() === "") {
                    return { ...img, alt: form.title };
                }
                return img;
            }
        );
    }

    // Also handle empty alts for new images
    if (form.alts && form.alts.length) {
        form.alts = form.alts.map((alt) =>
            alt.trim() === "" ? form.title : alt
        );
    }

    // Process content to restore EXISTING_IMAGE_PLACEHOLDER for edit mode
    const originalContent = form.content;
    if (richTextRef.value && richTextRef.value.restoreImageUploaderPlaceholders) {
        form.content = richTextRef.value.restoreImageUploaderPlaceholders();
    } else {
        form.content = processContentForSubmission(originalContent);
    }

    form.post(route("dashboard.news.update", props.news.slug), {
        preserveScroll: true,
        forceFormData: true,
        onError: () => {
            // Restore original content on error
            form.content = originalContent;
        },
    });
};

// Handle images added from RichText
const handleRichTextImageAdded = (imageFile) => {
    // Add to images array for ImageEditor
    const newImages = [...form.images, imageFile];
    const newAlts = [...form.alts, ''];

    form.images = newImages;
    form.alts = newAlts;
};
</script>

<template>

    <Head title="Edit Berita" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Edit Berita" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="http://www.w3.org/2000/svg" width="24px"
                        fill="#40483D">
                        <path
                            d="M160-120q-33 0-56.5-23.5T80-200v-640l67 67 66-67 67 67 67-67 66 67 67-67 67 67 66-67 67 67 67-67 66 67 67-67v640q0 33-23.5 56.5T800-120H160Zm0-80h280v-240H160v240Zm360 0h280v-80H520v80Zm0-160h280v-80H520v80ZM160-520h640v-120H160v120Z" />
                    </svg>

                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Edit Berita
        </template>

        <template #search-area>
            <Link :href="route('dashboard.news.index')">
                <SecondaryButton type="button">
                    Batal
                </SecondaryButton>
            </Link>
            <SecondaryButton type="button" :disabled="form.processing" @click="submit(true)">
                Simpan Draft
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
                Preview
            </SecondaryButton>
            <PrimaryButton type="button" :disabled="form.processing" @click="submit(false)">
                Update Berita
            </PrimaryButton>
        </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <div class="overflow-hidden bg-white">
                <div class="">
                    <form enctype="multipart/form-data">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Title Field -->
                            <div>
                                <SingleLineField 
                                    id="title" 
                                    v-model="form.title" 
                                    type="text" 
                                    fieldName="Judul Berita" 
                                    required 
                                    custom-width="100%"
                                />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>

                            <!-- Category Field -->
                            <CategoryInput 
                                fieldName="Kategori" 
                                v-model="form.category" 
                                :categories="categories"
                                :unused_categories="unused_categories" 
                                :errors="form.errors" 
                            />

                            <!-- Moderation Checkbox -->
                            <div>
                                <CheckboxField 
                                    id="is_moderate" 
                                    v-model="form.is_moderate" 
                                    fieldName="Moderasi Komentar"
                                    labelText="Hidupkan moderasi komentar untuk Berita ini" 
                                    custom-width="100%" 
                                />
                                <InputError :message="form.errors.is_moderate" class="mt-2" />
                            </div>

                            <!-- Rich Text Content -->
                            <div class="col-span-2 p-[3.5px] sm:p-[4px] md:p-[4.5px] lg:p-[4.8px] xl:p-[5.3px] 2xl:p-[6px] 3xl:p-[7px] 4xl:p-[8px]">
                                <RichText 
                                    ref="richTextRef" 
                                    type="for_edit"
                                    v-model="form.content"
                                    @image-added="handleRichTextImageAdded" 
                                />
                                <InputError :message="form.errors.content" class="mt-2" />
                            </div>

                            <!-- Image Editor -->
                            <ImageEditor 
                                v-model="form.images" 
                                v-model:alts="form.alts"
                                :existingImages="news.images" 
                                v-model:removedImages="form.removed_images"
                                v-model:existingImages="form.updated_existing_images" 
                                label="Images" 
                                :defaultAltText="form.title" 
                                :error="form.errors"
                                :alt-error="form.errors.alts" 
                                :required="true" 
                                :maxImages="10"
                            />

                            <!-- Video Upload -->
                            <div>
                                <InputLabel for="video" value="Video" />
                                <input 
                                    id="video" 
                                    type="file" 
                                    @input="handleVideoChange"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    accept="video/mp4, video/ogg, video/webm" 
                                />
                                <InputError :message="form.errors.video" class="mt-2" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <PreviewBerita 
            :isOpen="showPreview" 
            :newsData="form" 
            :categories="categories" 
            @close="closePreview" 
        />
    </AuthenticatedLayout>
</template>
