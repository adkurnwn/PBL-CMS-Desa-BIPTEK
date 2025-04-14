<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    users: Array,
    positions: Array, // Add positions prop
});

const form = useForm({});

const deleteUser = (userId) => {
    if (confirm("Are you sure you want to delete this user?")) {
        form.delete(route("dashboard.users.destroy", userId), {
            preserveScroll: true,
        });
    }
};

// Add delete position function
const deletePosition = (positionId) => {
    if (confirm("Are you sure you want to delete this position?")) {
        form.delete(route("dashboard.positions.destroy", positionId), {
            preserveScroll: true,
        });
    }
};

// Function to parse JSON job descriptions
const parseJobDescription = (description) => {
    if (!description) return [];
    if (Array.isArray(description)) return description;

    try {
        return JSON.parse(description);
    } catch (e) {
        return [];
    }
};
</script>

<template>
    <Head title="User Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    User Management
                </h2>
                <Link :href="route('dashboard.users.create')">
                    <PrimaryButton>Create New User</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Users Table -->
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-8"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Users
                        </h3>
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Position</th>
                                    <th>Admin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id">
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>
                                        {{
                                            user.position?.name ||
                                            "Not assigned"
                                        }}
                                    </td>
                                    <td class="flex justify-center">
                                        {{ user.is_admin ? "Yes" : "No" }}
                                    </td>
                                    <td>
                                        <div
                                            class="flex gap-x-2 justify-center"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'dashboard.users.edit',
                                                        user.id
                                                    )
                                                "
                                            >
                                                <PrimaryButton
                                                    >Edit</PrimaryButton
                                                >
                                            </Link>
                                            <DangerButton
                                                @click="deleteUser(user.id)"
                                            >
                                                Delete
                                            </DangerButton>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Positions Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                Positions
                            </h3>
                            <Link :href="route('dashboard.positions.create')">
                                <PrimaryButton
                                    >Create New Position</PrimaryButton
                                >
                            </Link>
                        </div>
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th>Position Name</th>
                                    <th>Job Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="position in positions"
                                    :key="position.id"
                                >
                                    <td class="px-6 py-4">
                                        {{ position.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <ul class="list-disc ml-5">
                                            <li
                                                v-for="(
                                                    duty, index
                                                ) in parseJobDescription(
                                                    position.job_description
                                                )"
                                                :key="index"
                                                class="text-gray-700 truncate"
                                                v-show="index < 2"
                                            >
                                                {{ duty }}
                                            </li>
                                            <li
                                                v-if="
                                                    parseJobDescription(
                                                        position.job_description
                                                    ).length > 2
                                                "
                                                class="text-gray-500"
                                            >
                                                ... and
                                                {{
                                                    parseJobDescription(
                                                        position.job_description
                                                    ).length - 2
                                                }}
                                                more
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div
                                            class="flex gap-x-2 justify-center"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'dashboard.positions.edit',
                                                        position.id
                                                    )
                                                "
                                            >
                                                <PrimaryButton
                                                    >Edit</PrimaryButton
                                                >
                                            </Link>
                                            <DangerButton
                                                @click="
                                                    deletePosition(position.id)
                                                "
                                                v-if="
                                                    position.name !==
                                                        'Admin Sistem' &&
                                                    position.name !==
                                                        'Kepala Desa'
                                                "
                                            >
                                                Delete
                                            </DangerButton>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
