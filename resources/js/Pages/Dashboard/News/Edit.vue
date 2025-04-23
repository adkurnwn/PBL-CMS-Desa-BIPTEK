<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, computed, onUnmounted, watch } from "vue";

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
    video: null,
    is_moderate: Boolean(props.news.is_moderate),
    is_draft: Boolean(props.news.is_draft),
    _method: "PUT",
});

const URL = window.URL;

const handleImagesChange = (e) => {
    const files = Array.from(e.target.files);
    form.images = files;
    form.alts = files.map(() => "");
};

const updateAltText = (index, value) => {
    form.alts[index] = value;
};

const handleVideoChange = (e) => {
    form.video = e.target.files[0];
};

const removeImage = (imageId) => {
    if (confirm("Are you sure you want to remove this image?")) {
        form.removed_images.push(imageId);
    }
};

const submit = () => {
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

                                <div>
                                    <InputLabel for="images" value="Images" />

                                    <!-- Existing Images -->
                                    <div
                                        v-if="props.news.images?.length"
                                        class="mb-4"
                                    >
                                        <p class="text-sm text-gray-600 mb-2">
                                            Current Images:
                                        </p>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div
                                                v-for="image in props.news
                                                    .images"
                                                :key="image.id"
                                                class="relative"
                                                v-show="
                                                    !form.removed_images.includes(
                                                        image.id
                                                    )
                                                "
                                            >
                                                <img
                                                    :src="`/storage/${image.path}`"
                                                    :alt="image.alt"
                                                    class="h-32 w-full object-cover rounded-lg"
                                                />
                                                <p
                                                    class="text-sm text-gray-600 mt-1"
                                                >
                                                    Alt: {{ image.alt }}
                                                </p>
                                                <DangerButton
                                                    type="button"
                                                    @click="
                                                        removeImage(image.id)
                                                    "
                                                    class="absolute top-2 right-2"
                                                >
                                                    ×
                                                </DangerButton>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- New Images Input -->
                                    <input
                                        id="images"
                                        type="file"
                                        @input="handleImagesChange"
                                        multiple
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="image/png, image/jpeg, image/jpg"
                                    />

                                    <!-- New Images Preview with Alt Inputs -->
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
                                                    @load="
                                                        URL.revokeObjectURL(
                                                            image
                                                        )
                                                    "
                                                />
                                            </div>
                                            <div class="flex-1">
                                                <InputLabel
                                                    :for="'alt-' + index"
                                                    :value="`Alt Text for New Image ${
                                                        index + 1
                                                    }`"
                                                />
                                                <TextInput
                                                    :id="'alt-' + index"
                                                    v-model="form.alts[index]"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    :placeholder="`Describe new image ${
                                                        index + 1
                                                    }`"
                                                    required
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <InputError
                                        :message="form.errors.images"
                                        class="mt-2"
                                    />
                                    <InputError
                                        :message="form.errors.alts"
                                        class="mt-2"
                                    />
                                </div>

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

                            <div>
                                <div class="flex-row items-center">
                                    <InputLabel for="is_draft" value="Draft" />
                                    <input
                                        id="is_draft"
                                        v-model="form.is_draft"
                                        type="checkbox"
                                        class="rounded border-gray-300 mt-2"
                                    />
                                </div>
                                <InputError
                                    :message="form.errors.is_draft"
                                    class="mt-2"
                                />
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Edit News
                                </PrimaryButton>
                                <Link :href="route('dashboard.news')">
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
