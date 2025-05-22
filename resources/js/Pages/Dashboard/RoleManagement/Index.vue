<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import { computed } from "vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import Search from "@/Components/DashboardComponent/Search.vue";

const props = defineProps({
    roles: Array
});

// Table headers configuration
const tableHeaders = [
    {
        label: 'Nama Role',
        field: 'name',
        sortable: false,
        nowrap: true,
        width: '70%',  // Changed from '974px' to percentage
        style: {
            fontSize: '18px',
            fontWeight: '500',
            fontStyle: 'normal',
            lineHeight: 'normal'
        }
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        nowrap: true,
        width: '30%',  // Changed from '450px' to percentage
        style: {
            fontSize: '18px',
            fontWeight: '400',
            fontStyle: 'normal',
            lineHeight: 'normal'
        }
    }
];

// Transform roles to match the table format
const tableItems = computed(() => {
    return props.roles.map(role => ({
        name: role.name,
        actions: null, // Will be handled by custom slot
        // Keep the original data for reference
        raw: role
    }));
});
</script>

<template>

    <Head title="Hak Akses" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Hak Akses" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#40483D"><path d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm0-80h480v-400H240v400Zm240-120q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80ZM240-160v-400 400Z"/></svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Search 
                v-model="searchQuery"
                placeholder="Cari role..."
            />
        </template>

        <div class="h-full">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg h-full">
                <div class="h-full">
                    <Tabel :headers="tableHeaders" :items="tableItems" :pagination="false"
                        empty-state-message="Tidak ada data role ditemukan.">
                        <!-- Custom slot for action column -->
                        <template #item.Aksi="{ item }">
                            <Link :href="route('dashboard.roles.edit', item.raw.id)">
                            <PrimaryButton :icon="false">
                                KELOLA AKSES
                            </PrimaryButton>
                            </Link>
                        </template>
                    </Tabel>
                </div>
            </div>
        </div>


    </AuthenticatedLayout>
</template>