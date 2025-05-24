<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import CategoryInput from "@/Components/DashboardComponent/CategoryInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import ImageEditor from "@/Components/DashboardComponent/ImageEditor.vue";
import { ref } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import TextAreaInput from "@/Components/DashboardComponent/TextAreaInput.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";

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

        <template #subtitle-area>
            Edit Galeri
        </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <div class="overflow-hidden bg-white">
                <div class="">
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-[22px] gap-y-[15px]">
                            <div class="flex flex-col gap-y-[15px]">
                                <!-- First column -->
                                <div>
                                    <SingleLineField
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        fieldName="Edit"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex flex-col gap-y-[15px]">
                                <!-- Second column - placeholder for future fields -->
                                <div>
                                    <!-- Updated Category Input with new styling -->
                                <CategoryInput
                                    v-model="form.category"
                                    :categories="categories"
                                    :unused_categories="unused_categories"
                                    :errors="form.errors"
                                    fieldName="Kategori"
                                    customWidth="100%"
                                />
                                </div>
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="mt-[15px]">
                            <TextAreaInput
                                id="description"
                                v-model="form.description"
                                type="text"
                                fieldName="Deskripsi"
                                required
                                custom-width="100%"
                            />
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <!-- Image Editor - keeping existing functionality -->
                        <div class="mt-[15px]">
                            <ImageEditor label="Gambar" v-model="form.images" v-model:alts="form.alts"
                                :existingImages="gallery.images" v-model:removedImages="form.removed_images"
                                v-model:existingImages="form.updated_existing_images"
                                v-model:bulkAltText="form.bulk_alt_text" :defaultAltText="form.title"
                                :alt-error="form.errors.alts" :error="form.errors" :required="true" />
                        </div>

                        <div class="mt-[15px] flex justify-end">
                            <Link :href="route('dashboard.galleries.index')" class="mr-[15px]">
                                <SecondaryButton type="button">
                                    BATAL
                                </SecondaryButton>
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                SIMPAN
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
