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
        width: '974px',
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
        width: '450px',
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="32" viewBox="0 0 25 32" fill="none">
                        <path
                            d="M10.1873 20.6247H14.8123L13.9259 15.6528C14.4398 15.3959 14.8445 15.0233 15.1399 14.5351C15.4354 14.0469 15.5832 13.5073 15.5832 12.9163C15.5832 12.0684 15.2813 11.3426 14.6774 10.7387C14.0736 10.1349 13.3478 9.83301 12.4998 9.83301C11.6519 9.83301 10.9261 10.1349 10.3222 10.7387C9.71841 11.3426 9.4165 12.0684 9.4165 12.9163C9.4165 13.5073 9.56425 14.0469 9.85973 14.5351C10.1552 15.0233 10.5599 15.3959 11.0738 15.6528L10.1873 20.6247ZM12.4998 31.4163C8.92831 30.517 5.97987 28.4679 3.65452 25.2689C1.32918 22.07 0.166504 18.5177 0.166504 14.6122V5.20801L12.4998 0.583008L24.8332 5.20801V14.6122C24.8332 18.5177 23.6705 22.07 21.3451 25.2689C19.0198 28.4679 16.0714 30.517 12.4998 31.4163Z"
                            fill="#4C7660" />
                    </svg>
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