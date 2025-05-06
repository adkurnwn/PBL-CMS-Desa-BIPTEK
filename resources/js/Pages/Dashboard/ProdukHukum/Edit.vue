<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CategoryInput from "@/Components/CategoryInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";

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

const handleFileChange = (e) => {
    form.file = e.target.files[0];
};

const submit = () => {
    form.post(route("dashboard.produk-hukum.update", props.produkHukum.id), {
            onSuccess: () => form.reset(),
            preserveScroll: true,
            forceFormData: true,
        }
    );
};
</script>

<template>
    <Head title="Create Produk Hukum" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit New Produk Hukum
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

                                <div>
                                    <InputLabel for="link" value="Link" />
                                    <TextInput
                                        id="link"
                                        v-model="form.link"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.link"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="description"
                                        value="Description"
                                    />
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.description"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="file" value="File" />
                                    <!-- Show current file if exists -->
                                    <div
                                        v-if="props.produkHukum.file"
                                        class="mb-2"
                                    >
                                        <p class="text-sm text-gray-600">
                                            Current file:
                                        </p>
                                        <a
                                            :href="`/storage/${props.produkHukum.file}`"
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-800"
                                        >
                                            View current file
                                        </a>
                                    </div>
                                    <!-- File input -->
                                    <input
                                        id="file"
                                        type="file"
                                        @input="handleFileChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        Leave empty to keep the current file
                                    </p>
                                    <InputError
                                        :message="form.errors.file"
                                        class="mt-2"
                                    />
                                </div>

                                <CategoryInput
                                    v-model="form.category"
                                    :categories="categories"
                                    :unused_categories="unused_categories"
                                    :errors="form.errors"
                                />

                                <!-- Add Date Field -->
                                <div>
                                    <InputLabel for="date" value="Date" />
                                    <TextInput
                                        id="date"
                                        v-model="form.date"
                                        type="date"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.date"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center mt-4">
                                    <InputLabel
                                        for="is_moderate"
                                        value="Is Moderate"
                                        class="block mr-4"
                                    />
                                    <input
                                        id="is_moderate"
                                        v-model="form.is_moderate"
                                        type="checkbox"
                                        class="rounded border-gray-300"
                                    />
                                </div>
                                <InputError
                                    :message="form.errors.is_moderate"
                                    class="mt-2"
                                />
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Update Produk Hukum
                                </PrimaryButton>
                                <Link
                                    :href="
                                        route('dashboard.produk-hukum.index')
                                    "
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
