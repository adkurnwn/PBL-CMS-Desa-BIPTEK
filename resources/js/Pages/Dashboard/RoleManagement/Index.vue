<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref, computed } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

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

    <Head title="Hak Akses" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Hak Akses" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="32" viewBox="0 0 25 32" fill="none">
                        <path
                            d="M10.1873 20.6247H14.8123L13.9259 15.6528C14.4398 15.3959 14.8445 15.0233 15.1399 14.5351C15.4354 14.0469 15.5832 13.5073 15.5832 12.9163C15.5832 12.0684 15.2813 11.3426 14.6774 10.7387C14.0736 10.1349 13.3478 9.83301 12.4998 9.83301C11.6519 9.83301 10.9261 10.1349 10.3222 10.7387C9.71841 11.3426 9.4165 12.0684 9.4165 12.9163C9.4165 13.5073 9.56425 14.0469 9.85973 14.5351C10.1552 15.0233 10.5599 15.3959 11.0738 15.6528L10.1873 20.6247ZM12.4998 31.4163C8.92831 30.517 5.97987 28.4679 3.65452 25.2689C1.32918 22.07 0.166504 18.5177 0.166504 14.6122V5.20801L12.4998 0.583008L24.8332 5.20801V14.6122C24.8332 18.5177 23.6705 22.07 21.3451 25.2689C19.0198 28.4679 16.0714 30.517 12.4998 31.4163Z"
                            fill="#4C7660" />
                    </svg>
                </template>
                <template #right-content>
                </template>
            </HeadingMenu>
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
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Features
                                            </th>
                                            <th v-for="role in roles" :key="role.id"
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ role.name }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <!-- Feature group headers -->
                                        <template v-for="(
permissions, feature
                                            ) in groupedPermissions" :key="feature">
                                            <!-- Feature heading row -->
                                            <tr class="bg-gray-50">
                                                <td colspan="100"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                    {{ feature }} Management
                                                </td>
                                            </tr>

                                            <!-- Permission rows for this feature -->
                                            <tr v-for="permission in permissions" :key="permission.id">
                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    <span class="capitalize">{{
                                                        permission.name.split(
                                                            "_"
                                                        )[0]
                                                    }}</span>
                                                </td>

                                                <!-- Checkbox for each role -->
                                                <td v-for="role in roles" :key="`${role.id}-${permission.id}`"
                                                    class="px-6 py-3 text-center">
                                                    <input type="checkbox" :id="`${role.id}-${permission.name}`"
                                                        :checked="hasPermission(
                                                            role.id,
                                                            permission.name
                                                        )
                                                            " @change="
                                                            togglePermission(
                                                                role.id,
                                                                permission.name
                                                            )
                                                            " class="rounded border-gray-300" />
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
