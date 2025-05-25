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
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#40483D"><path d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm0-80h480v-400H240v400Zm240-120q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80ZM240-160v-400 400Z"/></svg>
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
            <div class="overflow-hidden bg-white  ">
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