<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: "",
    category: {
        category_id: "",
        new_category: "",
        edit_category_id: "",
        new_category_name: "",
    },
    content: "",
    images: [],
    alts: [],
    video: null,
    is_moderate: false,
    is_draft: false,
});

const URL = window.URL;

const imagePreview = ref(null);

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

const submit = () => {
    form.post(route("dashboard.news.store"), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create News" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create New News
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
                                    <InputLabel for="content" value="Content" />
                                    <textarea
                                        id="content"
                                        v-model="form.content"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.content"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="images" value="Images" />
                                    <input
                                        id="images"
                                        type="file"
                                        @input="handleImagesChange"
                                        multiple
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="image/png, image/jpeg, image/jpg"
                                        required
                                    />

                                    <!-- Image previews with alt inputs -->
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
                                            for="is_moderated"
                                            value="Is Moderated"
                                        />
                                        <input
                                            id="is_moderated"
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

                                <div>
                                    <div class="flex-row items-center">
                                        <InputLabel
                                            for="is_draft"
                                            value="Draft"
                                        />
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
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Create News
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
