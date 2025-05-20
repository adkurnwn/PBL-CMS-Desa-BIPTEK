<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import TabDevider from "@/Components/DashboardComponent/TabDevider.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";

const props = defineProps({
    positions: Array,
});

const form = useForm({});

// Delete position function
const deletePosition = (positionId) => {
    if (confirm("Are you sure you want to delete this position?")) {
        form.delete(route("dashboard.positions.destroy", positionId), {
            preserveScroll: true,
        });
    }
};

// Function to parse JSON job descriptions
const parseJobDescription = (description) => {
    if (!description) return [];
    if (Array.isArray(description)) return description;

    try {
        return JSON.parse(description);
    } catch (e) {
        return [];
    }
};
</script>

<template>
    <Head title="Manajemen Posisi" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Manajemen Posisi" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="26" viewBox="0 0 25 26" fill="none">
                        <path
                            d="M12.4998 13.0003C10.804 13.0003 9.35227 12.3965 8.14463 11.1889C6.93699 9.98123 6.33317 8.52949 6.33317 6.83366C6.33317 5.13783 6.93699 3.68609 8.14463 2.47845C9.35227 1.27081 10.804 0.666992 12.4998 0.666992C14.1957 0.666992 15.6474 1.27081 16.855 2.47845C18.0627 3.68609 18.6665 5.13783 18.6665 6.83366C18.6665 8.52949 18.0627 9.98123 16.855 11.1889C15.6474 12.3965 14.1957 13.0003 12.4998 13.0003ZM0.166504 25.3337V21.017C0.166504 20.1434 0.39133 19.3404 0.840983 18.6081C1.29064 17.8758 1.88803 17.317 2.63317 16.9316C4.22623 16.135 5.84498 15.5377 7.48942 15.1394C9.13386 14.7411 10.804 14.542 12.4998 14.542C14.1957 14.542 15.8658 14.7411 17.5103 15.1394C19.1547 15.5377 20.7734 16.135 22.3665 16.9316C23.1116 17.317 23.709 17.8758 24.1587 18.6081C24.6083 19.3404 24.8332 20.1434 24.8332 21.017V25.3337H0.166504Z"
                            fill="currentColor" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Search 
                v-model="searchQuery"
                placeholder="Cari posisi..."
                @search="handleSearch"
            />
            <Link :href="route('dashboard.positions.create')">
                <TopButton :icon="true">
                    <template #icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20"
                            fill="none">
                            <mask id="mask0_1642_3445" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                y="0" width="21" height="20">
                                <rect x="0.5" width="20" height="20" fill="#D9D9D9" />
                            </mask>
                            <g mask="url(#mask0_1642_3445)">
                                <path
                                    d="M9.6665 10.8337H4.6665V9.16699H9.6665V4.16699H11.3332V9.16699H16.3332V10.8337H11.3332V15.8337H9.6665V10.8337Z"
                                    fill="white" />
                            </g>
                        </svg>
                    </template>
                    Create
                </TopButton>
            </Link>
        </template>

        <template #tab-devider>
            <TabDevider
                :tabs="[
                    { name: 'Pengguna', href: route('dashboard.users.index'), active: false },
                    { name: 'Posisi', href: route('dashboard.positions.index'), active: true },
                ]"
            />
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Positions Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full dashboard-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Job Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="position in positions" :key="position.id">
                                    <td>{{ position.name }}</td>
                                    <td>
                                        <ul class="list-disc ml-5">
                                            <li v-for="(duty, index) in parseJobDescription(position.job_description)" 
                                                :key="index" 
                                                class="text-gray-700 truncate" 
                                                v-show="index < 2">
                                                {{ duty }}
                                            </li>
                                            <li v-if="parseJobDescription(position.job_description).length > 2" 
                                                class="text-gray-500">
                                                ... and
                                                {{ parseJobDescription(position.job_description).length - 2 }}
                                                more
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-x-2 justify-center">
                                            <Link :href="route('dashboard.positions.edit', position.id)">
                                                <PrimaryButton>Edit</PrimaryButton>
                                            </Link>
                                            <DangerButton 
                                                @click="deletePosition(position.id)" 
                                                v-if="position.name !== 'Kepala Desa'">
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
