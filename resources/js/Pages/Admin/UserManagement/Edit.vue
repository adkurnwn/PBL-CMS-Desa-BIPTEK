<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    user: Object,
    positions: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "",
    role: props.user.role,
    id_position: props.user.id_position,
});

const submit = () => {
    form.put(route("admin.users.update", props.user.id), {
        preserveScroll: true,
        onSuccess: () => form.reset("password"),
    });
};
</script>
<template>
    <Head title="Edit User" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit User
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
                                        value="Password (leave empty to keep current)"
                                    />
                                    <TextInput
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.password"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="role" value="Role" />
                                    <select
                                        id="role"
                                        v-model="form.role"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    >
                                        <option value="admin">Admin</option>
                                        <option value="kepala_desa">
                                            Kepala Desa
                                        </option>
                                        <option value="aparat_desa">
                                            Aparat Desa
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.role"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="id_position"
                                        value="Position"
                                    />
                                    <select
                                        id="id_position"
                                        v-model="form.id_position"
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
                                        :message="form.errors.id_position"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-4 flex flex-row justify-between">
                                <PrimaryButton :disabled="form.processing">
                                    Update User
                                </PrimaryButton>
                                <Link :href="route('admin.users')">
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
