<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    produkDesa: Array,
});

const form = useForm({});

const toggleProdukStatus = (slug) => {
    form.delete(route("dashboard.produk-desa.destroy", slug), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Produk Desa" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Produk Desa
                </h2>

                <Link :href="route('dashboard.produk-desa.create')">
                    <PrimaryButton>Create New Produk Desa</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Nama Produk
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Harga
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Stok
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Kontak
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="produk in produkDesa"
                                    :key="produk.id"
                                >
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
                                            <Link
                                                :href="
                                                    route(
                                                        'dashboard.produk-desa.edit',
                                                        produk.slug
                                                    )
                                                "
                                            >
                                                <PrimaryButton>
                                                    Edit
                                                </PrimaryButton>
                                            </Link>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <button
                                                @click="
                                                    toggleProdukStatus(
                                                        produk.slug
                                                    )
                                                "
                                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                :class="
                                                    produk.status
                                                        ? 'bg-indigo-600'
                                                        : 'bg-gray-200'
                                                "
                                                role="switch"
                                                :aria-checked="produk.status"
                                            >
                                                <span
                                                    aria-hidden="true"
                                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                    :class="
                                                        produk.status
                                                            ? 'translate-x-5'
                                                            : 'translate-x-0'
                                                    "
                                                ></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="produkDesa.length === 0">
                                    <td
                                        class="px-6 py-4 text-center text-gray-500"
                                        colspan="5"
                                    >
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
