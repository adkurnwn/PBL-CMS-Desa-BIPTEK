<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import RichText from "@/Components/DashboardComponent/RichText.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: "",
    content: "",
    images: [],
    alts: [],
    is_moderate: true,
    is_draft: false,
});

// Handle rich text editor changes
const handleRichTextChange = (richTextData) => {
    form.title = richTextData.title;
    form.content = richTextData.content;
    form.images = richTextData.images;
    form.alts = richTextData.alts;
};

// Modified submit function to handle both publish and draft
const submit = (isDraft = false) => {
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

        <template #subtitle-area>
            Buat Berita Baru
        </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-6">
                    <form enctype="multipart/form-data">
                        <!-- Rich Text Editor -->
                        <RichText 
                            :modelValue="{
                                title: form.title,
                                content: form.content,
                                images: form.images,
                                alts: form.alts
                            }"
                            @update:modelValue="handleRichTextChange"
                            :errors="form.errors"
                        />

                        <!-- Action Buttons -->
                        <div class="mt-8 flex space-x-4 pt-6 border-t border-gray-200">
                            <PrimaryButton 
                                type="button" 
                                :disabled="form.processing" 
                                @click="submit(false)"
                                class="bg-green-600 hover:bg-green-700">
                                Publish News
                            </PrimaryButton>

                            <PrimaryButton 
                                type="button" 
                                :disabled="form.processing" 
                                @click="submit(true)"
                                class="bg-blue-600 hover:bg-blue-700">
                                Save as Draft
                            </PrimaryButton>

                            <Link :href="route('dashboard.news.index')">
                                <PrimaryButton 
                                    type="button" 
                                    class="bg-gray-600 hover:bg-gray-700">
                                    Cancel
                                </PrimaryButton>
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
