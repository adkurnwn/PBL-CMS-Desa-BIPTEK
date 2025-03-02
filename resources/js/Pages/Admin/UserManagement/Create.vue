<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
const form = useForm({
    name: "",
    email: "",
    password: "",
    is_admin: false,
});

const submit = () => {
    form.post(route("admin.users.store"), {
        onSuccess: () => form.reset(),
    });
};
</script>
<template>
    <Head title="User Management" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Create User Form -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">
                            Create New User
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
                                    <InputLabel for="email" value="Email" />
                                    <TextInput
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.email"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="password"
                                        value="Password"
                                    />
                                    <TextInput
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.password"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="flex items-center mt-4">
                                    <input
                                        id="is_admin"
                                        v-model="form.is_admin"
                                        type="checkbox"
                                        class="rounded border-gray-300"
                                    />
                                    <label for="is_admin" class="ml-2"
                                        >Is Admin</label
                                    >
                                </div>
                            </div>

                            <div class="mt-4">
                                <PrimaryButton :disabled="form.processing">
                                    Create User
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
