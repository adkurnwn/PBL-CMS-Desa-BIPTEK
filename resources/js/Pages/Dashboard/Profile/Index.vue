<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import { onMounted } from "vue";

const props = defineProps({
    profiles: Array,
});

onMounted(() => {
    // If profiles exist, redirect to edit page of the first profile
    if (props.profiles && props.profiles.length > 0) {
        router.visit(route('dashboard.profiles.edit', props.profiles[0].name));
    }
});
</script>

<template>
    <Head title="Profile Desa" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Profile Desa">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#40483D"><path d="M80-160v-400l240-240 240 240v400H80Zm80-80h120v-120h80v120h120v-287L320-687 160-527v287Zm120-200v-80h80v80h-80Zm360 280v-433L433-800h113l174 174v466h-80Zm160 0v-499L659-800h113l108 108v532h-80Zm-640-80h320-320Z"/></svg>
                </template>
            </HeadingMenu>
        </template>

        <div class="hidden">
                        <table class="min-w-full divide-y divide-gray-200 dashboard-table">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="profile in profiles"
                                    :key="profile.id"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.profiles.edit',
                                                    profile.name
                                                )
                                            "
                                        >
                                            <PrimaryButton>
                                                Edit Profile
                                            </PrimaryButton>
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
    </AuthenticatedLayout>
</template>
