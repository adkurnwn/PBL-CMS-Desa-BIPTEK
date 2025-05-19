<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30" fill="none">
                        <path
                            d="M0.0839844 29.8747V26.7913H13.959V9.56322C13.2909 9.33197 12.7128 8.97224 12.2246 8.48405C11.7364 7.99585 11.3767 7.41773 11.1454 6.74967H6.25065L10.8757 17.5413C10.8757 18.8261 10.3489 19.9181 9.29544 20.8174C8.24197 21.7167 6.9701 22.1663 5.47982 22.1663C3.98954 22.1663 2.71766 21.7167 1.66419 20.8174C0.61072 19.9181 0.0839844 18.8261 0.0839844 17.5413L4.70898 6.74967H1.62565V3.66634H11.1454C11.4538 2.76704 12.0062 2.02832 12.8027 1.4502C13.5993 0.87207 14.4986 0.583008 15.5007 0.583008C16.5027 0.583008 17.402 0.87207 18.1986 1.4502C18.9951 2.02832 19.5475 2.76704 19.8559 3.66634H29.3757V6.74967H26.2923L30.9173 17.5413C30.9173 18.8261 30.3906 19.9181 29.3371 20.8174C28.2836 21.7167 27.0118 22.1663 25.5215 22.1663C24.0312 22.1663 22.7593 21.7167 21.7059 20.8174C20.6524 19.9181 20.1257 18.8261 20.1257 17.5413L24.7507 6.74967H19.8559C19.6246 7.41773 19.2649 7.99585 18.7767 8.48405C18.2885 8.97224 17.7104 9.33197 17.0423 9.56322V26.7913H30.9173V29.8747H0.0839844ZM22.6309 17.5413H28.4121L25.5215 10.8351L22.6309 17.5413ZM2.58919 17.5413H8.37044L5.47982 10.8351L2.58919 17.5413ZM15.5007 6.74967C15.9375 6.74967 16.3036 6.60193 16.5991 6.30645C16.8946 6.01096 17.0423 5.64481 17.0423 5.20801C17.0423 4.7712 16.8946 4.40506 16.5991 4.10957C16.3036 3.81408 15.9375 3.66634 15.5007 3.66634C15.0638 3.66634 14.6977 3.81408 14.4022 4.10957C14.1067 4.40506 13.959 4.7712 13.959 5.20801C13.959 5.64481 14.1067 6.01096 14.4022 6.30645C14.6977 6.60193 15.0638 6.74967 15.5007 6.74967Z"
                            fill="#4C7660" />
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
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
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
                                            produk_hukum.id
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
