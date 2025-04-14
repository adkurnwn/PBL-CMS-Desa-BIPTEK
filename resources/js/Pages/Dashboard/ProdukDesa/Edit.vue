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
    categories: {
        type: Array,
        required: false,
        default: () => [],
    },
});

const form = useForm({
    name: props.produkDesa.name,
    description: props.produkDesa.description,
    price: props.produkDesa.price,
    stock: props.produkDesa.stock,
    kontak_penjual: props.produkDesa.kontak_penjual,
    image: null,
    category_id: props.produkDesa.category_id,
    new_category: "",
    _method: "PUT",
});

const isNewCategory = ref(false);

const handleImageChange = (e) => {
    form.image = e.target.files[0];
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

                                <!-- Category Field -->
                                <div v-if="categories.length > 0">
                                    <InputLabel
                                        for="category"
                                        value="Kategori"
                                    />
                                    <div class="flex gap-4">
                                        <select
                                            v-if="!isNewCategory"
                                            id="category_id"
                                            v-model="form.category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">
                                                Select Category
                                            </option>
                                            <option
                                                v-for="category in categories"
                                                :key="category.id"
                                                :value="category.id"
                                            >
                                                {{ category.name }}
                                            </option>
                                        </select>

                                        <TextInput
                                            v-else
                                            id="new_category"
                                            v-model="form.new_category"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="Enter new category name"
                                        />

                                        <button
                                            type="button"
                                            class="mt-1 px-4 py-2 text-sm text-blue-600 hover:text-blue-800"
                                            @click="
                                                isNewCategory = !isNewCategory
                                            "
                                        >
                                            {{
                                                isNewCategory
                                                    ? "Select Existing"
                                                    : "Add New"
                                            }}
                                        </button>
                                    </div>
                                    <InputError
                                        :message="
                                            form.errors.category_id ||
                                            form.errors.new_category
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div v-else>
                                    <InputLabel
                                        for="new_category"
                                        value="New Category"
                                    />
                                    <TextInput
                                        id="new_category"
                                        v-model="form.new_category"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="Enter category name"
                                    />
                                    <InputError
                                        :message="form.errors.new_category"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Image Field -->
                                <div>
                                    <InputLabel
                                        for="image"
                                        value="Product Image"
                                    />

                                    <!-- Show current image if exists -->
                                    <div v-if="produkDesa.image" class="mb-2">
                                        <p class="text-sm text-gray-600">
                                            Current image:
                                        </p>
                                        <img
                                            :src="`/storage/${produkDesa.image}`"
                                            alt="Product image"
                                            class="h-32 object-cover rounded-md mt-1"
                                        />
                                    </div>

                                    <input
                                        id="image"
                                        type="file"
                                        @input="handleImageChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="image/png, image/jpeg, image/jpg"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        Leave empty to keep the current image
                                    </p>
                                    <InputError
                                        :message="form.errors.image"
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
                                    <DangerButton type="button">
                                        Cancel
                                    </DangerButton>
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
