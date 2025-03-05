<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    role: Object,
    permissions: Array,
    rolePermissions: Array,
});

const form = useForm({
    permissions: props.rolePermissions,
});

// Group permissions by feature
const groupedPermissions = props.permissions.reduce((groups, permission) => {
    const [action, feature] = permission.name.split("_");
    if (!groups[feature]) {
        groups[feature] = [];
    }
    groups[feature].push(permission);
    return groups;
}, {});

const submit = () => {
    form.put(route("admin.roles.update", props.role.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Edit ${role.name} Permissions`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit {{ role.name }} Permissions
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Group permissions by feature -->
                            <div class="space-y-6">
                                <div
                                    v-for="(
                                        permissions, feature
                                    ) in groupedPermissions"
                                    :key="feature"
                                >
                                    <h3
                                        class="text-lg font-medium text-gray-900 capitalize mb-4"
                                    >
                                        {{ feature }} Management
                                    </h3>
                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div
                                            v-for="permission in permissions"
                                            :key="permission.id"
                                            class="flex items-center space-x-2"
                                        >
                                            <input
                                                type="checkbox"
                                                :id="permission.name"
                                                v-model="form.permissions"
                                                :value="permission.name"
                                                class="rounded border-gray-300"
                                            />
                                            <label
                                                :for="permission.name"
                                                class="capitalize"
                                            >
                                                {{
                                                    permission.name.split(
                                                        "_"
                                                    )[0]
                                                }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="border-b border-gray-200"></div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Update Permissions
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
