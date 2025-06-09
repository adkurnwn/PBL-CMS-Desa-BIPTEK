<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";

const props = defineProps({
    backups: Array,
});

const downloading = ref(false);
const restoring = ref(false);

const downloadBackup = (fileName) => {
    window.location.href = route("dashboard.recovery.download", fileName);
};

const createBackup = () => {
    router.post(
        route("dashboard.recovery.create"),
        {},
        {
            preserveScroll: true,
        }
    );
};

const restoreBackup = (path) => {
    if (
        confirm(
            "Are you sure you want to restore this backup? This will replace all current data."
        )
    ) {
        restoring.value = true;
        router.post(
            route("dashboard.recovery.restore", path),
            {},
            {
                preserveScroll: true,
                onFinish: () => {
                    restoring.value = false;
                },
            }
        );
    }
};

const deleteBackup = (path) => {
    if (confirm("Are you sure you want to delete this backup?")) {
        router.delete(route("dashboard.recovery.delete", path), {
            preserveScroll: true,
        });
    }
};

// Define table headers for the backups table
const tableHeaders = [
    {
        label: 'Date',
        field: 'date',
        sortable: true,
        nowrap: true
    },
    {
        label: 'Size',
        field: 'size',
        sortable: false,
        nowrap: true
    },
    {
        label: 'Actions',
        field: 'actions',
        sortable: false,
        nowrap: true
    }
];

// Empty state message
const emptyStateMessage = "Tidak ada data pencadangan yang tersedia. Lakukan pencadangan data untuk melihat data cadangan.";
</script>

<template>

    <Head title="Data Recovery" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Cadangkan dan Pulihkan Data">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M480-400q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0 280q-139 0-241-91.5T122-440h82q14 104 92.5 172T480-200q117 0 198.5-81.5T760-480q0-117-81.5-198.5T480-760q-69 0-129 32t-101 88h110v80H120v-240h80v94q51-64 124.5-99T480-840q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-120Z" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <PrimaryButton @click="createBackup">Cadangkan Sekarang</PrimaryButton>
        </template>

        <div class="bg-selectedBG p-4 mb-6 rounded-lg">
            <h4
                class="text-[18px] sm:text-[18px] md:text-[18px] xl:text-[20px] 2xl:text-[23px] 3xl:text-[27px] 4xl:text-[31px] font-semibold leading-tight text-accent2 mb-2">
                Terkait Pencadangan dan Pemulihan Data
            </h4>
            <p
                class="text-[10px] sm:text-[11px] md:text-[12px] lg:text-[13px] xl:text-[14px] 2xl:text-[16px] 3xl:text-[18px] 4xl:text-[20px] text-gray-600">
                Dengan halaman ini Anda dapat membuat, mengunduh, dan memulihkan cadangan data untuk website CMS Desa
                Anda.
                Membuat cadangan akan menyimpan database dan semua file dari website CMS Desa. Secara default, pencadangan data
                dilakukan secara
                otomatis setiap minggu. Anda dapat mengatur frekuensi pencadangan di halaman Pengaturan. 
            </p>
        </div>

        <div class="h-full">
            <div class="overflow-hidden bg-white shadow-sm h-full">
                <Tabel 
                    :headers="tableHeaders"
                    :items="backups || []"
                    :empty-state-message="emptyStateMessage"
                    :pagination="backups && backups.length > 10"
                    :items-per-page="10"
                >
                    <!-- Custom slot for Actions column -->
                    <template #item.Actions="{ item }">
                        <div class="flex justify-left space-x-2">
                            <PrimaryButton 
                                @click="downloadBackup(item.path)" 
                                :disabled="downloading"
                            >
                                Download
                            </PrimaryButton>
                            <PrimaryButton 
                                class="bg-yellow-500 hover:bg-yellow-600" 
                                @click="restoreBackup(item.path)" 
                                :disabled="restoring"
                            >
                                Restore
                            </PrimaryButton>
                        </div>
                    </template>
                </Tabel>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
