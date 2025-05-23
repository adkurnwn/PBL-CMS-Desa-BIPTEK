<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { onMounted } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const getTodayDate = () => {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, "0");
    const day = String(today.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
};

const form = useForm({
    title: "",
    description: "",
    category: {
        category_id: "",
        new_category: "",
        edit_category_id: "",
        new_category_name: "",
    },
    file: null,
    link: "",
    date: getTodayDate(),
    is_moderate: true,
});

const handleFileChange = (e) => {
    form.file = e.target.files[0];
};

const submit = () => {
    form.post(route("dashboard.produk-hukum.store"), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>

    <Head title="Tambah Produk Hukum" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Tambah Produk Hukum" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M160-120v-80h480v80H160Zm226-194L160-540l84-86 228 226-86 86Zm254-254L414-796l86-84 226 226-86 86Zm184 408L302-682l56-56 522 522-56 56Z" />
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

                                <div>
                                    <InputLabel for="link" value="Link" />
                                    <TextInput id="link" v-model="form.link" type="text" class="mt-1 block w-full" />
                                    <InputError :message="form.errors.link" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="description" value="Description" />
                                    <textarea id="description" v-model="form.description"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required></textarea>
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="file" value="File" />
                                    <input id="file" type="file" @input="handleFileChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                                    <InputError :message="form.errors.file" class="mt-2" />
                                </div>

                                <CategoryInput v-model="form.category" :categories="categories" :errors="form.errors" />

                                <!-- Add Date Field -->
                                <div>
                                    <InputLabel for="date" value="Date" />
                                    <TextInput id="date" v-model="form.date" type="date" class="mt-1 block w-full"
                                        required />
                                    <InputError :message="form.errors.date" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center mt-4">
                                    <InputLabel for="is_moderate" value="Is Moderate" class="block mr-4" />
                                    <input id="is_moderate" v-model="form.is_moderate" type="checkbox"
                                        class="rounded border-gray-300" />
                                </div>
                                <InputError :message="form.errors.is_moderate" class="mt-2" />
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Create Produk Hukum
                                </PrimaryButton>
                                <Link :href="route('dashboard.produk-hukum.index')
                                    ">
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
