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
    'Home Manage': 'Beranda',
    'Users Manage': 'Manajemen pengguna',
    'Positions Manage': 'Manajemen Posisi',
    'Profiles Manage': 'Manajemen Profil',
    'Demografi Manage': 'Manajemen Demografi',
    'News Manage': 'Manajemen Berita',
    'Galleries Manage': 'Manajemen Galeri',
    'Events Manage': 'Manajemen Agenda',
    'Produk-desa Manage': 'Manajemen UMKM/Produk Unggulan',
    'Produk-hukum Manage': 'Manajemen Produk Hukum',
    'Dana-desa Manage': 'Manajemen Laporan Dana Desa',
    'Comments Manage': 'Moderasi Komentar',
    'Feedbacks Manage': 'Tanggapi Saran & Keluhan',
    'Statistics Manage': 'Lihat Statistik',
};

// Section definitions
const sections = {
    'Pengguna': ['Users Manage', 'Positions Manage'],
    'Informasi Desa': ['Profiles Manage', 'Demografi Manage'],
    'Konten & Publikasi': ['News Manage', 'Galleries Manage', 'Events Manage', 'Produk-desa Manage'],
    'Regulasi Desa': ['Produk-hukum Manage', 'Dana-desa Manage'],
    'Interaksi Pengguna': ['Comments Manage', 'Feedbacks Manage'],
    'Lainnya': ['Home Manage', 'Statistics Manage'],
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

// Check if all permissions in a section are selected
const isAllSectionSelected = (sectionPermissions) => {
    return sectionPermissions.every(permission => hasPermission(permission.name));
};

// Get count of selected permissions in a section
const getSelectedCount = (sectionPermissions) => {
    return sectionPermissions.filter(permission => hasPermission(permission.name)).length;
};

// Toggle all permissions in a section
const toggleSectionPermissions = (sectionPermissions) => {
    const allSelected = isAllSectionSelected(sectionPermissions);
    
    sectionPermissions.forEach(permission => {
        const index = form.permissions.indexOf(permission.name);
        
        if (allSelected) {
            // Remove all permissions from this section
            if (index > -1) {
                form.permissions.splice(index, 1);
            }
        } else {
            // Add all permissions from this section
            if (index === -1) {
                form.permissions.push(permission.name);
            }
        }
    });
    
    console.log('Section permissions toggled:', form.permissions);
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                            <!-- Sections of permissions -->
                            <div v-for="(permissions, sectionName) in organizedPermissions" :key="sectionName"
                                class="mb-8 bg-menuBG p-4 rounded-lg flex flex-col">
                                <!-- Section Title with Select All -->
                                <div class="flex items-center justify-between mb-[19px]">
                                    <!-- Section Title -->
                                    <h4 class="text-selectedText font-medium text-[12px] md:text-[13px] xl:text-[11px] 2xl:text-[13px] 3xl:text-[14px] 4xl:text-[15px]">
                                        {{ sectionName }}
                                    </h4>
                                    
                                    <!-- Select All Checkbox -->
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            :id="`select-all-${sectionName}`"
                                            :checked="isAllSectionSelected(permissions)"
                                            @change="toggleSectionPermissions(permissions)"
                                            class="w-4 h-4 mr-2 rounded border-blueBorder text-blueBorder focus:ring-blueBorder cursor-pointer"
                                        />
                                        <label 
                                            :for="`select-all-${sectionName}`"
                                            class="text-TableText text-[10px] md:text-[11px] xl:text-[10px] 2xl:text-[11px] 3xl:text-[12px] 4xl:text-[13px] cursor-pointer"
                                        >
                                            Pilih Semua
                                        </label>
                                    </div>
                                </div>

                                <!-- Permissions as vertical list -->
                                <div class="flex-grow">
                                    <div v-for="permission in permissions" :key="permission.id"
                                        class=" py-[6px] flex items-center">
                                        <div class="relative flex items-center">
                                            <!-- Custom checkbox design -->
                                            <div class="w-[14px] h-[14px] sm:w-[15px] sm:h-[15px] md:w-[16px] md:h-[16px] xl:w-[16px] xl:h-[16px] 2xl:w-[20px] 2xl:h-[20px] 3xl:w-[22px] 3xl:h-[22px] 4xl:w-[24px] 4xl:h-[24px] rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] border border-blueBorder flex items-center justify-center cursor-pointer"
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
                                            class="ml-[10px] block text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] text-TableText cursor-pointer">
                                            {{ permission.displayName }}
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Permission Counter at bottom -->
                                <div class="text-right mt-auto">
                                    <span class="text-TableText text-[10px] md:text-[11px] xl:text-[10px] 2xl:text-[11px] 3xl:text-[12px] 4xl:text-[13px]">
                                        {{ getSelectedCount(permissions) }} permission dipilih
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>