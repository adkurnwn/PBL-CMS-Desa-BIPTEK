<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import DangerButton from "@/Components/DangerButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";

const props = defineProps({
    gallery: Object,
    categories: Array,
});

const URL = window.URL;

const form = useForm({
    title: props.gallery.title,
    category: {
        category_id: props.gallery.category_id,
        new_category: "",
    },
    description: props.gallery.description,
    images: [],
    alts: [],
    removed_images: [],
    _method: "PUT",
});

const handleImagesChange = (e) => {
    const files = Array.from(e.target.files);
    form.images = files;
    form.alts = files.map(() => "");
};

const updateAltText = (index, value) => {
    form.alts[index] = value;
};

const submit = () => {
    form.post(route("dashboard.galleries.update", props.gallery.id), {
        preserveScroll: true,
    });
};

// Add this method to handle image removal
const removeImage = (imageId) => {
    if (confirm("Are you sure you want to remove this image?")) {
        form.removed_images.push(imageId);
    }
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

                                <div>
                                    <InputLabel
                                        for="images"
                                        value="Gallery Images"
                                    />
                                    <input
                                        id="images"
                                        type="file"
                                        @input="handleImagesChange"
                                        multiple
                                        accept="image/png, image/jpeg, image/jpg"
                                        class="mt-1 block w-full"
                                    />

                                    <!-- Add alt text inputs for each selected image -->
                                    <div
                                        v-if="form.images.length > 0"
                                        class="mt-4 space-y-4"
                                    >
                                        <div
                                            v-for="(
                                                image, index
                                            ) in form.images"
                                            :key="index"
                                            class="flex gap-4 items-start"
                                        >
                                            <div class="w-32">
                                                <img
                                                    :src="
                                                        URL.createObjectURL(
                                                            image
                                                        )
                                                    "
                                                    class="w-full h-32 object-cover rounded"
                                                    @onload="
                                                        URL.revokeObjectURL(
                                                            image
                                                        )
                                                    "
                                                />
                                            </div>
                                            <div class="flex-1">
                                                <InputLabel
                                                    :for="'alt-' + index"
                                                    :value="`Alt Text for Image ${
                                                        index + 1
                                                    }`"
                                                />
                                                <TextInput
                                                    :id="'alt-' + index"
                                                    v-model="form.alts[index]"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    :placeholder="`Describe image ${
                                                        index + 1
                                                    }`"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <InputError
                                        :message="form.errors.images"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Create Gallery
                                </PrimaryButton>
                            </div>
                        </form>

                        <!-- Add section to display existing images -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium mb-4">
                                Existing Images
                            </h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div
                                    v-for="image in props.gallery.images"
                                    :key="image.id"
                                    class="space-y-2 relative"
                                    v-show="
                                        !form.removed_images.includes(image.id)
                                    "
                                >
                                    <img
                                        :src="`/storage/${image.path}`"
                                        :alt="image.alt"
                                        class="w-full h-32 object-cover rounded"
                                    />
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <p class="text-sm text-gray-600">
                                            Alt text: {{ image.alt }}
                                        </p>
                                        <DangerButton
                                            type="button"
                                            @click="removeImage(image.id)"
                                            class="absolute top-2 right-2"
                                        >
                                            ×
                                        </DangerButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
