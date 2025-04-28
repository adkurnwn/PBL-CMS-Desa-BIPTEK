<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";

const props = defineProps({
    danaDesa: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.danaDesa.title,
    file: "",
    link: props.danaDesa.link,
    date: props.danaDesa.date,
    _method: "PUT",
});

const handleFileChange = (e) => {
    form.file = e.target.files[0];
};

const submit = () => {
    form.post(route("dashboard.dana-desa.update", props.danaDesa.id), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit Dana Desa" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Dana Desa
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
                                    <InputLabel for="file" value="File" />
                                    <!-- Show current file if exists -->
                                    <div
                                        v-if="props.danaDesa.file"
                                        class="mb-2"
                                    >
                                        <p class="text-sm text-gray-600">
                                            Current file:
                                        </p>
                                        <a
                                            :href="`/storage/${props.danaDesa.file}`"
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-800"
                                        >
                                            View current file
                                        </a>
                                    </div>
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

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Update Dana Desa
                                </PrimaryButton>
                                <Link :href="route('dashboard.dana-desa')">
                                    <PrimaryButton type="button">
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
