<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";
import ImageUploader from "@/Components/ImageUploader.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

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
    is_moderate: true,
    is_draft: false, // Default is not draft
});

const URL = window.URL;

const imagePreview = ref(null);

const handleVideoChange = (e) => {
    form.video = e.target.files[0];
};

// Modified submit function to handle both publish and draft
const submit = (isDraft = false) => {
    // Set the draft status based on the button clicked
    form.is_draft = isDraft;

    form.post(route("dashboard.news.store"), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Buat Berita" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Buat Berita" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="31" height="28" viewBox="0 0 31 28" fill="none">
                        <path
                            d="M3.16634 27.875C2.31842 27.875 1.59256 27.5731 0.988737 26.9693C0.384918 26.3655 0.0830078 25.6396 0.0830078 24.7917V3.20833C0.0830078 2.36042 0.384918 1.63455 0.988737 1.03073C1.59256 0.42691 2.31842 0.125 3.16634 0.125H27.833C28.6809 0.125 29.4068 0.42691 30.0106 1.03073C30.6144 1.63455 30.9163 2.36042 30.9163 3.20833V24.7917C30.9163 25.6396 30.6144 26.3655 30.0106 26.9693C29.4068 27.5731 28.6809 27.875 27.833 27.875H3.16634ZM6.24967 21.7083H24.7497V18.625H6.24967V21.7083ZM6.24967 15.5417H12.4163V6.29167H6.24967V15.5417ZM15.4997 15.5417H24.7497V12.4583H15.4997V15.5417ZM15.4997 9.375H24.7497V6.29167H15.4997V9.375Z"
                            fill="#4C7660" />
                    </svg>

                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form enctype="multipart/form-data">
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

                                <!-- Use the new ImageInput component -->
                                <ImageUploader
                                    v-model="form.images"
                                    v-model:alts="form.alts"
                                    label="Images"
                                    :error="form.errors"
                                    :alt-error="form.errors.alts"
                                    :defaultAltText="form.title"
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
