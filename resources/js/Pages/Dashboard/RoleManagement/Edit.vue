<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import { ref, computed } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";

const props = defineProps({
    role: Object,
    permissions: Array,
    rolePermissions: Array, // Array of permission names assigned to this role
});

// Ensure we're using a proper reactive form
const form = useForm({
    permissions: [...props.rolePermissions], // Clone to avoid mutating props
});

// Permission name-to-display mapping
const permissionDisplayMap = {
    'Users Manage': 'Manajemen pengguna',
    'Positions Manage': 'Manajemen Posisi',
    'Dashboard Manage': 'Manajemen Dasbor',
    'Profiles Manage': 'Manajemen Profil',
    'Demografi Manage': 'Manajemen Demografi',
    'News Manage': 'Manajemen Berita',
    'Galleries Manage': 'Manajemen Galeri',
    'Events Manage': 'Manajemen Agenda',
    'Produk-desa Manage': 'Manajemen UMKM/Produk Unggulan',
    'Produk-hukum Manage': 'Manajemen Produk Hukum',
    'Dana-desa Manage': 'Manajemen Laporan Dana Desa',
    'Comments Manage': 'Manajemen Komentar',
    'Feedbacks Manage': 'Manajemen Saran & Keluhan',
    'Activity-logs Manage': 'Manajemen Riwayat Aktivitas'
};

// Section definitions
const sections = {
    'Manajemen Pengguna': ['Users Manage', 'Positions Manage', 'Dashboard Manage'],
    'Informasi Desa': ['Profiles Manage', 'Demografi Manage'],
    'Konten & Publikasi': ['News Manage', 'Galleries Manage', 'Events Manage', 'Produk-desa Manage'],
    'Regulasi Desa': ['Produk-hukum Manage', 'Dana-desa Manage'],
    'Interaksi Pengguna': ['Comments Manage', 'Feedbacks Manage', 'Activity-logs Manage']
};

// Reorganized permissions by section
const organizedPermissions = computed(() => {
    // Filter permissions and organize them by section
    const result = {};

    Object.keys(sections).forEach(sectionName => {
        const sectionPermissions = [];

        props.permissions.forEach(permission => {
            // Extract feature and action from permission name
            const [action, feature] = permission.name.split("_");

            // Recreate the permission display format from the backend
            const permissionKey = `${feature ? feature.charAt(0).toUpperCase() + feature.slice(1) : ''} ${action ? action.charAt(0).toUpperCase() + action.slice(1) : ''}`.trim();

            // Check if this permission belongs to this section
            if (sections[sectionName].includes(permissionKey)) {
                sectionPermissions.push({
                    id: permission.id,
                    name: permission.name,
                    displayName: permissionDisplayMap[permissionKey] || permissionKey
                });
            }
        });

        // Only add sections that have permissions
        if (sectionPermissions.length > 0) {
            result[sectionName] = sectionPermissions;
        }
    });

    return result;
});

// Debugging to ensure permissions are properly tracked
const togglePermission = (permissionName) => {
    const index = form.permissions.indexOf(permissionName);
    if (index > -1) {
        // Create a new array to ensure reactivity
        form.permissions.splice(index, 1); // Remove permission
        console.log(`Removed permission: ${permissionName}`, form.permissions);
    } else {
        form.permissions.push(permissionName); // Add permission
        console.log(`Added permission: ${permissionName}`, form.permissions);
    }
};

const hasPermission = (permissionName) => {
    return form.permissions.includes(permissionName);
};

// Add feedback messages
const successMessage = ref('');
const errorMessage = ref('');

const submit = () => {
    // Clear previous messages
    successMessage.value = '';
    errorMessage.value = '';

    // Debug log
    console.log('Submitting permissions:', form.permissions);

    form.put(route("dashboard.roles.update", props.role.id), {
        onSuccess: () => {
            successMessage.value = 'Hak akses berhasil disimpan!';
            setTimeout(() => {
                successMessage.value = '';
            }, 3000);
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors);
            errorMessage.value = 'Terjadi kesalahan saat menyimpan hak akses.';
        },
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Edit Hak Akses" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Edit Hak Akses" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="32" viewBox="0 0 25 32" fill="none">
                        <path
                            d="M10.1873 20.6247H14.8123L13.9259 15.6528C14.4398 15.3959 14.8445 15.0233 15.1399 14.5351C15.4354 14.0469 15.5832 13.5073 15.5832 12.9163C15.5832 12.0684 15.2813 11.3426 14.6774 10.7387C14.0736 10.1349 13.3478 9.83301 12.4998 9.83301C11.6519 9.83301 10.9261 10.1349 10.3222 10.7387C9.71841 11.3426 9.4165 12.0684 9.4165 12.9163C9.4165 13.5073 9.56425 14.0469 9.85973 14.5351C10.1552 15.0233 10.5599 15.3959 11.0738 15.6528L10.1873 20.6247ZM12.4998 31.4163C8.92831 30.517 5.97987 28.4679 3.65452 25.2689C1.32918 22.07 0.166504 18.5177 0.166504 14.6122V5.20801L12.4998 0.583008L24.8332 5.20801V14.6122C24.8332 18.5177 23.6705 22.07 21.3451 25.2689C19.0198 28.4679 16.0714 30.517 12.4998 31.4163Z"
                            fill="#4C7660" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Edit Hak Akses {{ role.name }}
        </template>

        <template #search-area>
            <Link :href="route('dashboard.roles.index')">
                <SecondaryButton>
                    BATAL
                </SecondaryButton>
            </Link>
            <PrimaryButton @click.prevent="submit" :disabled="form.processing">
                {{ form.processing ? 'UPDATING...' : 'UPDATE' }}
            </PrimaryButton>
        </template>

        <div class="h-full pb-[48px]">
            <div class="overflow-hidden bg-white sm:rounded-lg">
                <div class="pl-[22px]">
                    <!-- Show validation errors if any -->
                    <div v-if="Object.keys(form.errors).length > 0"
                        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                        </ul>
                    </div>

                    <form @submit.prevent="submit">
                        <!-- Sections of permissions -->
                        <div class="grid grid-cols-1 md:grid-cols-2">
                            <!-- Sections of permissions -->
                            <div v-for="(permissions, sectionName) in organizedPermissions" :key="sectionName"
                                class="mb-8">
                                <!-- Section Title -->
                                <h4 class="text-selectedText font-medium text-[15px] mb-[19px]">{{ sectionName }}</h4>

                                <!-- Permissions as vertical list -->
                                <div class="">
                                    <div v-for="permission in permissions" :key="permission.id"
                                        class=" pb-[13px] pt-[13px] flex items-center">
                                        <div class="relative flex items-center">
                                            <!-- Custom checkbox design -->
                                            <div class="w-[24px] h-[24px] rounded-[5px] border border-blueBorder flex items-center justify-center cursor-pointer"
                                                :class="{ 'bg-blueBorder': hasPermission(permission.name) }"
                                                @click="togglePermission(permission.name)">
                                                <!-- Checkmark SVG - visible when checked -->
                                                <svg v-if="hasPermission(permission.name)"
                                                    xmlns="http://www.w3.org/2000/svg" width="12" height="10"
                                                    viewBox="0 0 12 10" fill="none">
                                                    <path d="M4 9.4L0 5.4L1.4 4L4 6.6L10.6 0L12 1.4L4 9.4Z"
                                                        fill="white" />
                                                </svg>
                                            </div>

                                            <!-- Hidden actual checkbox for form submission -->
                                            <input type="checkbox" :id="permission.name" :value="permission.name"
                                                :checked="hasPermission(permission.name)"
                                                @change="togglePermission(permission.name)" class="hidden" />
                                        </div>

                                        <!-- Permission display name -->
                                        <label :for="permission.name"
                                            class="ml-[10px] block text-[18px] text-TableText cursor-pointer">
                                            {{ permission.displayName }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>