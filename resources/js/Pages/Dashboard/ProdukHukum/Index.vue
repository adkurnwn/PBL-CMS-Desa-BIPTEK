<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
const props = defineProps({
    produkHukum: Array,
});
</script>

<template>

    <Head title="Produk Hukum" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Produk Hukum" :hasRightContent="true">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M160-120v-80h480v80H160Zm226-194L160-540l84-86 228 226-86 86Zm254-254L414-796l86-84 226 226-86 86Zm184 408L302-682l56-56 522 522-56 56Z" />
                    </svg>
                </template>
                <template #right-content>
                    <Link :href="route('dashboard.produk-hukum.create')">
                    <PrimaryButton>Create New Produk Hukum</PrimaryButton>
                    </Link>
                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm  ">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200 dashboard-table">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Judul
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="produk_hukum in produkHukum" :key="produk_hukum.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ produk_hukum.title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link :href="route(
                                            'dashboard.produk-hukum.edit',
                                            produk_hukum.slug
                                        )
                                            ">
                                        <PrimaryButton>
                                            Edit Produk Hukum
                                        </PrimaryButton>
                                        </Link>
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
