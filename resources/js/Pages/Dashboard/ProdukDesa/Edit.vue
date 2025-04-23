<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ref } from "vue";

const props = defineProps({
    produkDesa: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.produkDesa.name,
    description: props.produkDesa.description,
    price: props.produkDesa.price,
    stock: props.produkDesa.stock,
    kontak_penjual: props.produkDesa.kontak_penjual,
    images: [],
    removed_images: [],
    _method: "PUT",
});

const handleImageChange = (e) => {
    form.images = [...e.target.files];
};

const removeImage = (imageId) => {
    if (confirm("Are you sure you want to remove this image?")) {
        form.removed_images.push(imageId);
    }
};

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
                                        for="name"
                                        value="Nama Produk"
                                    />
                                    <TextInput
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.name"
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
                                        type="number"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.price"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Stock Field -->
                                <div>
                                    <InputLabel for="stock" value="Stock" />
                                    <TextInput
                                        id="stock"
                                        v-model="form.stock"
                                        type="number"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.stock"
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

                                <!-- Image Field -->
                                <div>
                                    <InputLabel
                                        for="images"
                                        value="Product Images"
                                    />

                                    <!-- Show current images if they exist -->
                                    <div
                                        v-if="
                                            produkDesa.images &&
                                            produkDesa.images.length > 0
                                        "
                                        class="mb-2"
                                    >
                                        <p class="text-sm text-gray-600">
                                            Current images:
                                        </p>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <div
                                                v-for="(
                                                    image, index
                                                ) in produkDesa.images"
                                                :key="index"
                                                class="relative"
                                            >
                                                <img
                                                    :src="`/storage/${image.path}`"
                                                    alt="Product image"
                                                    class="h-32 object-cover rounded-md"
                                                />
                                                <DangerButton
                                                    type="button"
                                                    @click="
                                                        removeImage(image.id)
                                                    "
                                                    class="absolute top-2 right-2"
                                                >
                                                    ×
                                                </DangerButton>
                                            </div>
                                        </div>
                                    </div>

                                    <input
                                        id="images"
                                        type="file"
                                        @input="handleImageChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="image/png, image/jpeg, image/jpg"
                                        multiple
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        You can select multiple images
                                    </p>
                                    <InputError
                                        :message="form.errors.images"
                                        class="mt-2"
                                    />
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
                                    <PrimaryButton type="button"
                                        >Cancel</PrimaryButton
                                    >
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
