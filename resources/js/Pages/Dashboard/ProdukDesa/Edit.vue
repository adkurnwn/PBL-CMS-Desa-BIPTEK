<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ImageEditor from "@/Components/ImageEditor.vue";
import { ref } from "vue";

const props = defineProps({
    produkDesa: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    product_name: props.produkDesa.product_name,
    description: props.produkDesa.description,
    price: props.produkDesa.price,
    instagram: props.produkDesa.instagram,
    facebook: props.produkDesa.facebook,
    tiktok: props.produkDesa.tiktok,
    youtube: props.produkDesa.youtube,
    x: props.produkDesa.x,
    nama_penjual: props.produkDesa.nama_penjual,
    alamat_penjual: props.produkDesa.alamat_penjual,
    kontak_penjual: props.produkDesa.kontak_penjual,
    status: props.produkDesa.status,
    images: [],
    removed_images: [],
    _method: "PUT",
});

const submit = () => {
    form.post(route("dashboard.produk-desa.update", props.produkDesa.id), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit Produk Desa" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Produk Desa
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form
                            @submit.prevent="submit"
                            enctype="multipart/form-data"
                        >
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Name Field -->
                                <div>
                                    <InputLabel
                                        for="product_name"
                                        value="Nama Produk"
                                    />
                                    <TextInput
                                        id="product_name"
                                        v-model="form.product_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.product_name"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Price Field -->
                                <div>
                                    <InputLabel
                                        for="price"
                                        value="Harga (Rp)"
                                    />
                                    <TextInput
                                        id="price"
                                        v-model="form.price"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.price"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Contact Field -->
                                <div>
                                    <InputLabel
                                        for="kontak_penjual"
                                        value="Kontak Penjual"
                                    />
                                    <TextInput
                                        id="kontak_penjual"
                                        v-model="form.kontak_penjual"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.kontak_penjual"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Nama Penjual Field -->
                                <div>
                                    <InputLabel
                                        for="nama_penjual"
                                        value="Nama Penjual"
                                    />
                                    <TextInput
                                        id="nama_penjual"
                                        v-model="form.nama_penjual"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.nama_penjual"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Alamat Penjual Field -->
                                <div>
                                    <InputLabel
                                        for="alamat_penjual"
                                        value="Alamat Penjual"
                                    />
                                    <TextInput
                                        id="alamat_penjual"
                                        v-model="form.alamat_penjual"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.alamat_penjual"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <div class="flex-row items-center">
                                        <InputLabel
                                            for="status"
                                            value="Status"
                                        />
                                        <input
                                            id="status"
                                            v-model="form.status"
                                            type="checkbox"
                                            class="rounded border-gray-300 mt-2"
                                        />
                                    </div>
                                    <InputError
                                        :message="form.errors.status"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Image Field -->
                                <div>
                                    <ImageEditor
                                        v-model="form.images"
                                        v-model:alts="form.alts"
                                        :existingImages="produkDesa.images"
                                        v-model:removedImages="
                                            form.removed_images
                                        "
                                        label="Product Images"
                                        :error="form.errors"
                                        :required="true"
                                        :altText="false"
                                    />
                                </div>

                                <!-- Social Media Section - Full Width -->
                                <div class="col-span-2 mt-4">
                                    <h3 class="font-medium text-gray-700 mb-2">
                                        Social Media Links
                                    </h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Instagram Field -->
                                        <div>
                                            <InputLabel
                                                for="instagram"
                                                value="Instagram"
                                            />
                                            <TextInput
                                                id="instagram"
                                                v-model="form.instagram"
                                                type="text"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError
                                                :message="form.errors.instagram"
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Facebook Field -->
                                        <div>
                                            <InputLabel
                                                for="facebook"
                                                value="Facebook"
                                            />
                                            <TextInput
                                                id="facebook"
                                                v-model="form.facebook"
                                                type="text"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError
                                                :message="form.errors.facebook"
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- TikTok Field -->
                                        <div>
                                            <InputLabel
                                                for="tiktok"
                                                value="TikTok"
                                            />
                                            <TextInput
                                                id="tiktok"
                                                v-model="form.tiktok"
                                                type="text"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError
                                                :message="form.errors.tiktok"
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- YouTube Field -->
                                        <div>
                                            <InputLabel
                                                for="youtube"
                                                value="YouTube"
                                            />
                                            <TextInput
                                                id="youtube"
                                                v-model="form.youtube"
                                                type="text"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError
                                                :message="form.errors.youtube"
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- X (Twitter) Field -->
                                        <div>
                                            <InputLabel
                                                for="x"
                                                value="X (Twitter)"
                                            />
                                            <TextInput
                                                id="x"
                                                v-model="form.x"
                                                type="text"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError
                                                :message="form.errors.x"
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Description Field - Full Width -->
                                <div class="col-span-2">
                                    <InputLabel
                                        for="description"
                                        value="Description"
                                    />
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.description"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-6 flex gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Update Produk Desa
                                </PrimaryButton>
                                <Link :href="route('dashboard.produk-desa')">
                                    <PrimaryButton type="button">
                                        Cancel
                                    </PrimaryButton>
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
