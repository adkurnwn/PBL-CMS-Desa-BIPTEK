<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";
import ImageUploader from "@/Components/ImageUploader.vue";

const URL = window.URL;

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
    description: "",
    images: [],
    alts: [],
});

const submit = () => {
    form.post(route("dashboard.galleries.store"), {
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

                                <ImageUploader
                                    v-model="form.images"
                                    v-model:alts="form.alts"
                                    label="Images"
                                    :error="form.errors"
                                    :defaultAltText="form.title"
                                    :alt-error="form.errors.alts"
                                    :required="true"
                                />
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Create Gallery
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
