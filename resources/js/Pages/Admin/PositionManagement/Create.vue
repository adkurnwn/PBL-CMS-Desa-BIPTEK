<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    positions: Array,
});

const form = useForm({
    name: "",
    parent_id: "",
});

const submit = () => {
    form.post(route("admin.positions.store"), {
        // Fixed route name
        onSuccess: () => form.reset(),
    });
};
</script>
<template>
    <Head title="Position Management" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">
                            Create New Position
                        </h3>
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
                                        value="Parent"
                                    />
                                    <select
                                        id="parent_id"
                                        v-model="form.parent_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">
                                            Select Parent Department
                                        </option>
                                        <option
                                            v-for="pos in positions"
                                            :key="pos.id"
                                            :value="pos.id"
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

                            <div class="mt-4">
                                <PrimaryButton :disabled="form.processing">
                                    Create Deparatement
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
