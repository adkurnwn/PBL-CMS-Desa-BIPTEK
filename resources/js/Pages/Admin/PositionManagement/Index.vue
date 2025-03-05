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
    positions: Array,
});

const form = useForm({}); // Add this line

const deletePosition = (positionId) => {
    if (confirm("Are you sure you want to delete this position?")) {
        form.delete(route("admin.positions.destroy", positionId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Position Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Position Management
                </h2>
                <Link :href="route('admin.positions.create')">
                    <PrimaryButton>Buat Jabatan</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Users Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Atasan</th>

                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="position in positions"
                                    :key="position.id"
                                >
                                    <td>{{ position.name }}</td>
                                    <td>{{ position.parent_id }}</td>
                                    <td>
                                        <div
                                            class="flex gap-x-2 justify-center"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.positions.edit',
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
