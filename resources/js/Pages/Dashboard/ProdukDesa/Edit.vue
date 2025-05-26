<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";
import ImageEditor from "@/Components/DashboardComponent/ImageEditor.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import TextAreaInput from "@/Components/DashboardComponent/TextAreaInput.vue";
import CheckboxField from "@/Components/DashboardComponent/CheckboxField.vue";

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

// Store display value separately from form value
const displayPrice = ref("");

// Initialize display price on component mount
const initializeDisplayPrice = () => {
    if (props.produkDesa.price) {
        const numericValue = props.produkDesa.price.toString().replace(/\D/g, '');
        if (numericValue) {
            const formatted = parseInt(numericValue).toLocaleString('id-ID');
            displayPrice.value = `Rp ${formatted}`;
        }
    }
};

// Initialize on mount
initializeDisplayPrice();

// Currency formatting function
const formatCurrency = (value) => {
    // Remove all non-digit characters
    const numericValue = value.replace(/\D/g, '');
    
    if (!numericValue) return '';
    
    // Format with thousand separators
    const formatted = parseInt(numericValue).toLocaleString('id-ID');
    
    return `Rp ${formatted}`;
};

// Handle price input with live formatting
const handlePriceInput = (event) => {
    const rawValue = event.target.value;
    const numericValue = rawValue.replace(/\D/g, '');
    
    // Store plain numeric value in form
    form.price = numericValue;
    
    // Format for display
    const formattedValue = formatCurrency(rawValue);
    displayPrice.value = formattedValue;
    
    // Set the input value to the formatted version
    event.target.value = formattedValue;
};

const submit = () => {
    form.post(route("dashboard.produk-desa.update", props.produkDesa.slug), {
            preserveScroll: true,
            forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit UMKM/Produk Unggulan" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Edit UMKM/Produk Unggulan" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="30" viewBox="0 0 35 30" fill="none">
                        <path
                            d="M26.7493 29.9587V25.3337H22.1243V22.2503H26.7493V17.6253H29.8327V22.2503H34.4577V25.3337H29.8327V29.9587H26.7493ZM2.08268 25.3337V16.0837H0.541016V13.0003L2.08268 5.29199H25.2077L26.7493 13.0003V16.0837H25.2077V20.7087H22.1243V16.0837H15.9577V25.3337H2.08268ZM5.16602 22.2503H12.8743V16.0837H5.16602V22.2503ZM2.08268 3.75033V0.666992H25.2077V3.75033H2.08268Z"
                            fill="#4C7660" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Edit UMKM/Produk Unggulan
        </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <div class="overflow-hidden bg-white">
                <div class="">
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-[22px] gap-y-[15px]">
                            <div class="flex flex-col gap-y-[15px]">
                                <!-- First column -->
                                <div>
                                    <SingleLineField
                                        id="product_name"
                                        v-model="form.product_name"
                                        type="text"
                                        fieldName="Nama Produk"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.product_name" class="mt-2" />
                                </div>

                                <div>
                                    <SingleLineField
                                        id="price"
                                        v-model="displayPrice"
                                        type="text"
                                        fieldName="Harga (Rp)"
                                        required
                                        custom-width="100%"
                                        @input="handlePriceInput"
                                    />
                                    <InputError :message="form.errors.price" class="mt-2" />
                                </div>

                                <div>
                                    <SingleLineField
                                        id="kontak_penjual"
                                        v-model="form.kontak_penjual"
                                        type="text"
                                        fieldName="Kontak Whatsapp Penjual"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.kontak_penjual" class="mt-2" />
                                </div>

                                <div>
                                    <SingleLineField
                                        id="nama_penjual"
                                        v-model="form.nama_penjual"
                                        type="text"
                                        fieldName="Nama Penjual"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.nama_penjual" class="mt-2" />
                                </div>

                                <div>
                                    <SingleLineField
                                        id="alamat_penjual"
                                        v-model="form.alamat_penjual"
                                        type="text"
                                        fieldName="Alamat Penjual (Google Maps)"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.alamat_penjual" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex flex-col gap-y-[15px]">
                                <!-- Second column - Social Media -->
                                <div>
                                    <SingleLineField
                                        id="instagram"
                                        v-model="form.instagram"
                                        type="text"
                                        fieldName="Tautan Instagram"
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.instagram" class="mt-2" />
                                </div>

                                <div>
                                    <SingleLineField
                                        id="facebook"
                                        v-model="form.facebook"
                                        type="text"
                                        fieldName="Tautan Facebook"
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.facebook" class="mt-2" />
                                </div>

                                <div>
                                    <SingleLineField
                                        id="tiktok"
                                        v-model="form.tiktok"
                                        type="text"
                                        fieldName="Tautan TikTok"
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.tiktok" class="mt-2" />
                                </div>

                                <div>
                                    <SingleLineField
                                        id="youtube"
                                        v-model="form.youtube"
                                        type="text"
                                        fieldName="Tautan YouTube"
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.youtube" class="mt-2" />
                                </div>

                                <div>
                                    <SingleLineField
                                        id="x"
                                        v-model="form.x"
                                        type="text"
                                        fieldName="Tautan X (Twitter)"
                                        custom-width="100%"
                                    />
                                    <InputError :message="form.errors.x" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Status Field -->
                        <div class="mt-[15px]">
                            <CheckboxField
                                id="status"
                                v-model="form.status"
                                fieldName="Status"
                                labelText="Aktif"
                                custom-width="100%"
                            />
                            <InputError :message="form.errors.status" class="mt-2" />
                        </div>

                        <!-- Description Field -->
                        <div class="mt-[15px]">
                            <TextAreaInput
                                id="description"
                                v-model="form.description"
                                type="text"
                                fieldName="Deskripsi"
                                required
                                custom-width="100%"
                                :rows="4"
                            />
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <!-- Image Editor -->
                        <div class="mt-[15px]">
                            <ImageEditor
                                label="Gambar Produk"
                                v-model="form.images"
                                :existingImages="produkDesa.images"
                                v-model:removedImages="form.removed_images"
                                :error="form.errors"
                                :required="true"
                                :altText="false"
                                :maxImages="3"
                            />
                        </div>

                        <div class="mt-[15px] flex justify-end">
                            <Link :href="route('dashboard.produk-desa.index')" class="mr-[15px]">
                                <SecondaryButton type="button">
                                    BATAL
                                </SecondaryButton>
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                SIMPAN
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
