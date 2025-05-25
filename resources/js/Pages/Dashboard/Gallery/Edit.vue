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
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M360-400h400L622-580l-92 120-62-80-108 140Zm-40 160q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320Zm0-80h480v-480H320v480ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Zm160-720v480-480Z" />
                    </svg>

                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm  ">
                    <div class="p-6">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Name Field -->
                                <div>
                                    <InputLabel for="title" value="Title" />
                                    <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full"
                                        required />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>

                                <CategoryInput v-model="form.category" :categories="categories"
                                    :unused_categories="unused_categories" :errors="form.errors" />

                                <div>
                                    <InputLabel for="description" value="Description" />
                                    <textarea id="description" v-model="form.description" type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required></textarea>
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>

                                <ImageEditor label="Images" v-model="form.images" v-model:alts="form.alts"
                                    :existingImages="gallery.images" v-model:removedImages="form.removed_images"
                                    v-model:existingImages="form.updated_existing_images
                                        " v-model:bulkAltText="form.bulk_alt_text" :defaultAltText="form.title"
                                    :alt-error="form.errors.alts" :error="form.errors" :required="true" />
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Edit Gallery
                                </PrimaryButton>
                                <Link :href="route('dashboard.galleries.index')">
                                <PrimaryButton type="button">Cancel</PrimaryButton>
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
