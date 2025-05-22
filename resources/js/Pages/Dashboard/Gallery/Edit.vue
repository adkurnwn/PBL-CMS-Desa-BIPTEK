<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import ImageEditor from "@/Components/ImageEditor.vue";
import { ref } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const props = defineProps({
    gallery: {
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

const URL = window.URL;
const existingImages = ref(props.gallery.images);

const form = useForm({
    title: props.gallery.title,
    category: {
        category_id: props.gallery.category_id,
        new_category: "",
        edit_category_id: "",
        new_category_name: "",
        deleting_category_id: [],
    },
    description: props.gallery.description,
    images: [],
    alts: [],
    removed_images: [],
    updated_existing_images: [],
    bulk_alt_text: "",
    _method: "PUT",
});

// Handle updates to existing images (alt text changes)
const updateExistingImages = (images) => {
    form.updated_existing_images = images;
};

// Handle bulk alt text updates
const updateBulkAltText = (text) => {
    form.bulk_alt_text = text;
};

const submit = () => {
    // Replace any empty alt texts with the title before submission
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

    form.post(route("dashboard.galleries.update", props.gallery.slug), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit Galeri" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Edit Galeri" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="29" height="28" viewBox="0 0 29 28" fill="none">
                        <path
                            d="M3.70833 27.875C2.86042 27.875 2.13455 27.5731 1.53073 26.9693C0.92691 26.3655 0.625 25.6396 0.625 24.7917V3.20833C0.625 2.36042 0.92691 1.63455 1.53073 1.03073C2.13455 0.42691 2.86042 0.125 3.70833 0.125H25.2917C26.1396 0.125 26.8655 0.42691 27.4693 1.03073C28.0731 1.63455 28.375 2.36042 28.375 3.20833V24.7917C28.375 25.6396 28.0731 26.3655 27.4693 26.9693C26.8655 27.5731 26.1396 27.875 25.2917 27.875H3.70833ZM5.25 21.7083H23.75L17.9688 14L13.3438 20.1667L9.875 15.5417L5.25 21.7083ZM9.10417 10.9167C9.74653 10.9167 10.2925 10.6918 10.7422 10.2422C11.1918 9.79253 11.4167 9.24653 11.4167 8.60417C11.4167 7.96181 11.1918 7.4158 10.7422 6.96615C10.2925 6.51649 9.74653 6.29167 9.10417 6.29167C8.46181 6.29167 7.9158 6.51649 7.46615 6.96615C7.01649 7.4158 6.79167 7.96181 6.79167 8.60417C6.79167 9.24653 7.01649 9.79253 7.46615 10.2422C7.9158 10.6918 8.46181 10.9167 9.10417 10.9167Z"
                            fill="#4C7660" />
                    </svg>

                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form
                            @submit.prevent="submit"
                            enctype="multipart/form-data"
                        >
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Name Field -->
                                <div>
                                    <InputLabel for="title" value="Title" />
                                    <TextInput
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.title"
                                        class="mt-2"
                                    />
                                </div>

                                <CategoryInput
                                    v-model="form.category"
                                    :categories="categories"
                                    :unused_categories="unused_categories"
                                    :errors="form.errors"
                                />

                                <div>
                                    <InputLabel
                                        for="description"
                                        value="Description"
                                    />
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.description"
                                        class="mt-2"
                                    />
                                </div>

                                <ImageEditor
                                    label="Images"
                                    v-model="form.images"
                                    v-model:alts="form.alts"
                                    :existingImages="gallery.images"
                                    v-model:removedImages="form.removed_images"
                                    v-model:existingImages="
                                        form.updated_existing_images
                                    "
                                    v-model:bulkAltText="form.bulk_alt_text"
                                    :defaultAltText="form.title"
                                    :alt-error="form.errors.alts"
                                    :error="form.errors"
                                    :required="true"
                                />
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Edit Gallery
                                </PrimaryButton>
                                <Link
                                    :href="route('dashboard.galleries.index')"
                                >
                                    <PrimaryButton type="button"
                                        >Cancel</PrimaryButton
                                    >
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
