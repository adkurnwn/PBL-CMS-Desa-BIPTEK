<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import ImageEditor from "@/Components/ImageEditor.vue";
import { ref } from "vue";

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
    _method: "PUT",
});

// Handle updates to existing images (alt text changes)
const updateExistingImages = (images) => {
    form.updated_existing_images = images;
};

const submit = () => {
    form.post(route("dashboard.galleries.update", props.gallery.id), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create Gallery" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create New Gallery
            </h2>
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
                                    v-model="form.images"
                                    v-model:alts="form.alts"
                                    :existingImages="gallery.images"
                                    v-model:removedImages="form.removed_images"
                                    v-model:existingImages="
                                        form.updated_existing_images
                                    "
                                    label="Images"
                                    :error="form.errors"
                                    :alt-error="form.errors.alts"
                                    :required="true"
                                />
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Edit Gallery
                                </PrimaryButton>
                                <Link :href="route('dashboard.galleries')">
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
