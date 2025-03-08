<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";

const props = defineProps({
    positions: Array,
});

const form = useForm({
    name: "",
    email: "",
    password: "",
    phone: "",
    photo_profile: null,
    position: "Staff", // Default value from enum
    job_description: [], // Changed to array for multiple descriptions
    status: true, // Default value
    role: "Admin", // Default role
});

// New job description input
const newJobDescription = ref("");

const handleFileChange = (e) => {
    form.photo_profile = e.target.files[0];
};

// Add new job description to the array
const addJobDescription = (e) => {
    e.preventDefault();
    if (newJobDescription.value.trim()) {
        form.job_description.push(newJobDescription.value.trim());
        newJobDescription.value = "";
    }
};

// Remove job description at specific index
const removeJobDescription = (index) => {
    form.job_description.splice(index, 1);
};

const submit = () => {
    form.post(route("admin.users.store"), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create User" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create New User
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

                                <!-- Email Field -->
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

                                <!-- Password Field -->
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

                                <!-- Phone Field -->
                                <div>
                                    <InputLabel for="phone" value="Phone" />
                                    <TextInput
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.phone"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Photo Profile Field -->
                                <div>
                                    <InputLabel
                                        for="photo_profile"
                                        value="Profile Photo"
                                    />
                                    <input
                                        id="photo_profile"
                                        type="file"
                                        @input="handleFileChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="image/png, image/jpeg, image/jpg"
                                    />
                                    <InputError
                                        :message="form.errors.photo_profile"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Position Field -->
                                <div>
                                    <InputLabel
                                        for="position"
                                        value="Position"
                                    />
                                    <select
                                        id="position"
                                        v-model="form.position"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="Kepala Desa">
                                            Kepala Desa
                                        </option>
                                        <option value="Sekretaris Desa">
                                            Sekretaris Desa
                                        </option>
                                        <option value="Kepala Urusan">
                                            Kepala Urusan
                                        </option>
                                        <option value="Kepala Dusun">
                                            Kepala Dusun
                                        </option>
                                        <option value="Kepala Seksi">
                                            Kepala Seksi
                                        </option>
                                        <option value="Staff">Staff</option>
                                    </select>
                                    <InputError
                                        :message="form.errors.position"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Modified Job Description Field -->
                                <div class="col-span-2 mb-4">
                                    <InputLabel
                                        for="job_description"
                                        value="Job Description"
                                    />
                                    <div class="flex mt-1">
                                        <TextInput
                                            id="job_description"
                                            v-model="newJobDescription"
                                            type="text"
                                            class="block w-full"
                                            placeholder="Enter job description and press Enter"
                                            @keydown.enter="addJobDescription"
                                        />
                                        <PrimaryButton
                                            type="button"
                                            class="ml-2"
                                            @click="addJobDescription"
                                        >
                                            Add
                                        </PrimaryButton>
                                    </div>

                                    <!-- Display job descriptions as tags -->
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <div
                                            v-for="(
                                                description, index
                                            ) in form.job_description"
                                            :key="index"
                                            class="bg-gray-100 px-2 py-1 rounded-md flex items-center gap-1"
                                        >
                                            <span>{{ description }}</span>
                                            <button
                                                type="button"
                                                class="text-red-500 hover:text-red-700 focus:outline-none"
                                                @click="
                                                    removeJobDescription(index)
                                                "
                                            >
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                    <InputError
                                        :message="form.errors.job_description"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Status Field -->
                                <div>
                                    <div class="flex items-center">
                                        <input
                                            id="status"
                                            v-model="form.status"
                                            type="checkbox"
                                            class="rounded border-gray-300"
                                        />
                                        <InputLabel
                                            for="status"
                                            value="Active"
                                            class="ml-2"
                                        />
                                    </div>
                                    <InputError
                                        :message="form.errors.status"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Role Field -->
                                <div>
                                    <InputLabel for="role" value="Role" />
                                    <select
                                        id="role"
                                        v-model="form.role"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="Admin">Admin</option>
                                        <option value="Kepala Desa">
                                            Kepala Desa
                                        </option>
                                        <option value="Aparat Desa">
                                            Aparat Desa
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.role"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-6">
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
