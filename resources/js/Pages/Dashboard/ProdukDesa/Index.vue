<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    produkDesa: Array,
});

const form = useForm({});

const deleteProduk = (produkId) => {
    if (confirm("Are you sure you want to delete this product?")) {
        form.delete(route("dashboard.produk-desa.destroy", produkId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="UMKM/Produk Unggulan" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="UMKM/Produk Unggulan" :hasRightContent="true">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="30" viewBox="0 0 35 30" fill="none">
                        <path
                            d="M26.7493 29.9587V25.3337H22.1243V22.2503H26.7493V17.6253H29.8327V22.2503H34.4577V25.3337H29.8327V29.9587H26.7493ZM2.08268 25.3337V16.0837H0.541016V13.0003L2.08268 5.29199H25.2077L26.7493 13.0003V16.0837H25.2077V20.7087H22.1243V16.0837H15.9577V25.3337H2.08268ZM5.16602 22.2503H12.8743V16.0837H5.16602V22.2503ZM2.08268 3.75033V0.666992H25.2077V3.75033H2.08268Z"
                            fill="#4C7660" />
                    </svg>
                </template>
                <template #right-content>
                    <Link :href="route('dashboard.produk-desa.create')">
                    <PrimaryButton>Create New Produk Desa</PrimaryButton>
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
                                        Nama Produk
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Harga
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stok
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kontak
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="produk in produkDesa" :key="produk.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ produk.product_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        Rp
                                        {{
                                            Number(
                                                produk.price
                                            ).toLocaleString()
                                        }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ produk.stock }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ produk.kontak_penjual }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex gap-2">
                                            <Link :href="route(
                                                'dashboard.produk-desa.edit',
                                                produk.id
                                            )
                                                ">
                                            <PrimaryButton>
                                                Edit
                                            </PrimaryButton>
                                            </Link>
                                            <DangerButton @click="deleteProduk(produk.id)">
                                                Delete
                                            </DangerButton>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="produkDesa.length === 0">
                                    <td class="px-6 py-4 text-center text-gray-500" colspan="5">
                                        No products found. Create a new product
                                        to get started.
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
