<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";
import ImageUploader from "@/Components/ImageUploader.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

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

    <Head title="Buat" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Buat Galeri" :hasRightContent="false">
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

                                <CategoryInput v-model="form.category" :categories="categories" :errors="form.errors" />

                                <div>
                                    <InputLabel for="description" value="Description" />
                                    <textarea id="description" v-model="form.description" type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required></textarea>
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>

                                <ImageUploader v-model="form.images" v-model:alts="form.alts" label="Images"
                                    :error="form.errors" :defaultAltText="form.title" :alt-error="form.errors.alts"
                                    :required="true" />
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Create Gallery
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
