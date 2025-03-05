<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    position: {
        type: Object,
        required: true,
    },
    positions: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: props.position.name,
    parent_id: props.position.parent_id?.toString() || "",
});

const submit = () => {
    console.log("Submitting form with data:", form.data());
    form.put(route("admin.positions.update", props.position.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log("Form submitted successfully");
        },
        onError: (errors) => {
            console.error("Form submission errors:", errors);
        },
    });
};
</script>
<template>
    <Head title="Edit Position" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Position
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="name" value="Name" />
                                    <TextInput
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.name"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="parent_id"
                                        value="Parent Position"
                                    />
                                    <select
                                        id="parent_id"
                                        v-model="form.parent_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    >
                                        <option value="">No Parent</option>
                                        <option
                                            v-for="pos in positions"
                                            :key="pos.id"
                                            :value="pos.id.toString()"
                                            :disabled="pos.id === position.id"
                                        >
                                            {{ pos.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.parent_id"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-4 flex flex-row justify-between">
                                <PrimaryButton :disabled="form.processing">
                                    Update Position
                                </PrimaryButton>
                                <Link :href="route('admin.positions')">
                                    <DangerButton>Cancel</DangerButton>
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
