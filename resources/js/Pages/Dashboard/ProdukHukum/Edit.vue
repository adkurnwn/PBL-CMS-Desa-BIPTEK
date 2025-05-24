<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";
import CategoryInput from "@/Components/DashboardComponent/CategoryInput.vue";
import CheckboxField from "@/Components/DashboardComponent/CheckboxField.vue";
import FileField from "@/Components/DashboardComponent/FileField.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import TextAreaInput from "@/Components/DashboardComponent/TextAreaInput.vue";

const props = defineProps({
    produkHukum: {
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
    title: props.produkHukum.title,
    description: props.produkHukum.description,
    category: {
        category_id: props.produkHukum.category_id,
        new_category: "",
        edit_category_id: "",
        new_category_name: "",
        deleting_category_id: [],
    },
    file: "",
    link: props.produkHukum.link,
    date: props.produkHukum.date,
    is_moderate: Boolean(props.produkHukum.is_moderate),
    _method: "PUT",
});

const handleFileChange = (file) => {
    form.file = file;
};

const submit = () => {
    form.post(route("dashboard.produk-hukum.update", props.produkHukum.slug), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit Produk Hukum" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Edit Produk Hukum" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M160-120v-80h480v80H160Zm226-194L160-540l84-86 228 226-86 86Zm254-254L414-796l86-84 226 226-86 86Zm184 408L302-682l56-56 522 522-56 56Z" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Edit Produk Hukum
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

                                <!-- Date Field -->
                                <div>
                                    <SingleLineField 
                                        id="date" 
                                        v-model="form.date" 
                                        type="date" 
                                        fieldName="Tanggal" 
                                        required 
                                        custom-width="100%" 
                                    />
                                    <InputError :message="form.errors.date" class="mt-2" />
                                </div>

                                <!-- Updated Category Input with new styling -->
                                <CategoryInput 
                                    v-model="form.category" 
                                    :categories="categories"
                                    :unused_categories="unused_categories" 
                                    :errors="form.errors" 
                                    fieldName="Kategori"
                                    customWidth="100%"
                                />
                            </div>

                            <div class="flex flex-col gap-y-[15px]">
                                <!-- Second column -->
                                <div>
                                    <SingleLineField 
                                        id="link" 
                                        v-model="form.link" 
                                        type="text" 
                                        fieldName="Link" 
                                        custom-width="100%" 
                                    />
                                    <InputError :message="form.errors.link" class="mt-2" />
                                </div>

                                <!-- Updated Checkbox Field -->
                                <div>
                                    <CheckboxField
                                        id="is_moderate"
                                        v-model="form.is_moderate"
                                        fieldName="Moderasi Komentar"
                                        labelText="Hidupkan moderasi komentar untuk Produk Hukum ini"
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.is_moderate" class="mt-2" />
                                </div>

                                <!-- Updated File Field -->
                                <div>
                                    <FileField
                                        id="file"
                                        v-model="form.file"
                                        fieldName="File"
                                        :current-file="props.produkHukum.file"
                                        :current-file-url="`/storage/${props.produkHukum.file}`"
                                        accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                        custom-width="100%"
                                        help-text="Kosongkan untuk mempertahankan file saat ini"
                                        @update:modelValue="handleFileChange"
                                    />
                                    <InputError :message="form.errors.file" class="mt-2" />
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

                        <div class="mt-[15px] flex justify-end">
                            <Link :href="route('dashboard.produk-hukum.index')" class="mr-[15px]">
                                <SecondaryButton type="button">
                                    BATAL
                                </SecondaryButton>
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                UPDATE
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
