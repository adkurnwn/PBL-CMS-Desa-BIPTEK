<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import CategoryInput from "@/Components/DashboardComponent/CategoryInput.vue";
import ImageUploader from "@/Components/DashboardComponent/ImageUploader.vue";
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
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M160-120q-33 0-56.5-23.5T80-200v-640l67 67 66-67 67 67 67-67 66 67 67-67 67 67 66-67 67 67 67-67 66 67 67-67v640q0 33-23.5 56.5T800-120H160Zm0-80h280v-240H160v240Zm360 0h280v-80H520v80Zm0-160h280v-80H520v80ZM160-520h640v-120H160v120Z" />
                    </svg>

                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm  ">
                    <div class="p-6">
                        <form enctype="multipart/form-data">
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Name Field -->
                                <div>
                                    <InputLabel for="title" value="Title" />
                                    <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full"
                                        required />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>

                                <CategoryInput v-model="form.category" :categories="categories" :errors="form.errors" />

                                <div>
                                    <InputLabel for="content" value="Content" />
                                    <textarea id="content" v-model="form.content" type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required></textarea>
                                    <InputError :message="form.errors.content" class="mt-2" />
                                </div>

                                <!-- Use the new ImageInput component -->
                                <ImageUploader v-model="form.images" v-model:alts="form.alts" label="Images"
                                    :error="form.errors" :alt-error="form.errors.alts" :defaultAltText="form.title"
                                    :required="true" />

                                <div>
                                    <InputLabel for="video" value="Video" />
                                    <input id="video" type="file" @input="handleVideoChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="video/mp4, video/ogg, video/webm" />
                                    <InputError :message="form.errors.video" class="mt-2" />
                                </div>

                                <div>
                                    <div class="flex-row items-center">
                                        <InputLabel for="is_moderated" value="Is Moderated" />
                                        <input id="is_moderated" v-model="form.is_moderate" type="checkbox"
                                            class="rounded border-gray-300 mt-2" />
                                    </div>
                                    <InputError :message="form.errors.is_moderate" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-6 flex space-x-4">
                                <PrimaryButton type="button" :disabled="form.processing" @click="submit(false)"
                                    class="bg-green-600 hover:bg-green-700">
                                    Publish News
                                </PrimaryButton>

                                <PrimaryButton type="button" :disabled="form.processing" @click="submit(true)"
                                    class="bg-blue-600 hover:bg-blue-700">
                                    Save as Draft
                                </PrimaryButton>

                                <Link :href="route('dashboard.news.index')">
                                <PrimaryButton type="button" class="bg-gray-600 hover:bg-gray-700">
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
