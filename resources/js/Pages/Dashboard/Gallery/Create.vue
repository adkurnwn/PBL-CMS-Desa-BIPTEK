<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";
import CategoryInput from "@/Components/DashboardComponent/CategoryInput.vue";
import ImageEditor from "@/Components/DashboardComponent/ImageEditor.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import TextAreaInput from "@/Components/DashboardComponent/TextAreaInput.vue";

const URL = window.URL;

const props = defineProps({
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
    title: "",
    category: {
        category_id: "",
        new_category: "",
        edit_category_id: "",
        new_category_name: "",
        deleting_category_id: [],
    },
    description: "",
    images: [],
    alts: [],
    bulk_alt_text: "",
});

const submit = () => {
    // Replace any empty alt texts with the title before submission
    if (form.alts && form.alts.length) {
        form.alts = form.alts.map((alt) =>
            alt.trim() === "" ? form.title : alt
        );
    }

    form.post(route("dashboard.galleries.store"), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Buat Galeri" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Buat Galeri" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="http://www.w3.org/2000/svg 0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M360-400h400L622-580l-92 120-62-80-108 140Zm-40 160q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320Zm0-80h480v-480H320v480ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Zm160-720v480-480Z" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Buat Galeri
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
                                        fieldName="Judul"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex flex-col gap-y-[15px]">
                                <!-- Second column -->
                                <div>
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

                        <!-- Image Editor -->
                        <div class="mt-[15px]">
                            <ImageEditor 
                                label="Gambar" 
                                v-model="form.images" 
                                v-model:alts="form.alts"
                                v-model:bulkAltText="form.bulk_alt_text" 
                                :defaultAltText="form.title"
                                :alt-error="form.errors.alts" 
                                :error="form.errors" 
                                :required="true"
                                max-images="25" 
                            />
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
