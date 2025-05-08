<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref, computed } from "vue";

const props = defineProps({
    roles: Array,
    permissions: Array,
    rolePermissions: Object, // Map of role_id to array of permission names
});

const form = useForm({
    permissions: { ...props.rolePermissions }, // Clone to avoid mutating props
});

// Group permissions by feature
const groupedPermissions = computed(() => {
    return props.permissions.reduce((groups, permission) => {
        const [action, feature] = permission.name.split("_");
        if (!groups[feature]) {
            groups[feature] = [];
        }
        groups[feature].push(permission);
        return groups;
    }, {});
});

const togglePermission = (roleId, permissionName) => {
    if (!form.permissions[roleId]) {
        form.permissions[roleId] = [];
    }

    const index = form.permissions[roleId].indexOf(permissionName);
    if (index > -1) {
        form.permissions[roleId].splice(index, 1); // Remove permission
    } else {
        form.permissions[roleId].push(permissionName); // Add permission
    }
};

const hasPermission = (roleId, permissionName) => {
    return (
        form.permissions[roleId] &&
        form.permissions[roleId].includes(permissionName)
    );
};

const submit = () => {
    form.post(route("roles.batch-update"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Role Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Role Management
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="mb-6 text-gray-600 text-sm">
                                Select permissions for each role by checking the
                                boxes below
                            </div>

                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                >
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Features
                                            </th>
                                            <th
                                                v-for="role in roles"
                                                :key="role.id"
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                {{ role.name }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-gray-200"
                                    >
                                        <!-- Feature group headers -->
                                        <template
                                            v-for="(
                                                permissions, feature
                                            ) in groupedPermissions"
                                            :key="feature"
                                        >
                                            <!-- Feature heading row -->
                                            <tr class="bg-gray-50">
                                                <td
                                                    colspan="100"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                                                >
                                                    {{ feature }} Management
                                                </td>
                                            </tr>

                                            <!-- Permission rows for this feature -->
                                            <tr
                                                v-for="permission in permissions"
                                                :key="permission.id"
                                            >
                                                <td
                                                    class="px-6 py-3 whitespace-nowrap"
                                                >
                                                    <span class="capitalize">{{
                                                        permission.name.split(
                                                            "_"
                                                        )[0]
                                                    }}</span>
                                                </td>

                                                <!-- Checkbox for each role -->
                                                <td
                                                    v-for="role in roles"
                                                    :key="`${role.id}-${permission.id}`"
                                                    class="px-6 py-3 text-center"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        :id="`${role.id}-${permission.name}`"
                                                        :checked="
                                                            hasPermission(
                                                                role.id,
                                                                permission.name
                                                            )
                                                        "
                                                        @change="
                                                            togglePermission(
                                                                role.id,
                                                                permission.name
                                                            )
                                                        "
                                                        class="rounded border-gray-300"
                                                    />
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Update All Permissions
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
