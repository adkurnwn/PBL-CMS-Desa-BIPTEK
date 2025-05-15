<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, computed, onUnmounted, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";
import ImageEditor from "@/Components/ImageEditor.vue";

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

const handleVideoChange = (e) => {
    form.video = e.target.files[0];
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

    form.post(route("dashboard.news.update", props.news.id), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit News" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit News
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
                                    <InputLabel for="content" value="Content" />
                                    <textarea
                                        id="content"
                                        v-model="form.content"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.content"
                                        class="mt-2"
                                    />
                                </div>

                                <ImageEditor
                                    v-model="form.images"
                                    v-model:alts="form.alts"
                                    :existingImages="news.images"
                                    v-model:removedImages="form.removed_images"
                                    v-model:existingImages="
                                        form.updated_existing_images
                                    "
                                    label="Images"
                                    :defaultAltText="form.title"
                                    :error="form.errors"
                                    :alt-error="form.errors.alts"
                                    :required="true"
                                />

                                <div>
                                    <InputLabel for="video" value="Video" />
                                    <input
                                        id="video"
                                        type="file"
                                        @input="handleVideoChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="video/mp4, video/ogg, video/webm"
                                    />
                                    <InputError
                                        :message="form.errors.video"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <div class="flex-row items-center">
                                        <InputLabel
                                            for="is_moderate"
                                            value="Is Moderate"
                                        />
                                        <input
                                            id="is_moderate"
                                            v-model="form.is_moderate"
                                            type="checkbox"
                                            class="rounded border-gray-300 mt-2"
                                        />
                                    </div>
                                    <InputError
                                        :message="form.errors.is_moderate"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-6 flex space-x-4">
                                <PrimaryButton
                                    type="button"
                                    :disabled="form.processing"
                                    @click="submit(false)"
                                    class="bg-green-600 hover:bg-green-700"
                                >
                                    Publish News
                                </PrimaryButton>

                                <PrimaryButton
                                    type="button"
                                    :disabled="form.processing"
                                    @click="submit(true)"
                                    class="bg-blue-600 hover:bg-blue-700"
                                >
                                    Save as Draft
                                </PrimaryButton>

                                <Link :href="route('dashboard.news.index')">
                                    <PrimaryButton
                                        type="button"
                                        class="bg-gray-600 hover:bg-gray-700"
                                    >
                                        Cancel
                                    </PrimaryButton>
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
