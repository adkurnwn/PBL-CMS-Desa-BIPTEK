<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";
import ImageInput from "@/Components/DashboardComponent/ImageInput.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import DropdownFieldInput from "@/Components/DashboardComponent/DropdownFieldInput.vue";
import TextAreaInput from "@/Components/DashboardComponent/TextAreaInput.vue";

// Add reactive data for regions
const provinces = ref([]);
const regencies = ref([]);
const districts = ref([]);
const villages = ref([]);
const loadingRegions = ref(true);

// Form
const form = useForm({
    name: "",
    email: "",
    phone: "",
    logo: null,
    provinsi: "",
    kabupaten: "",
    kecamatan: "",
    kelurahan: "",
    hero_images: [],
    organization_image: null,
    history: "",
    vision: "",
    mission: "",
});

// Fetch region data
const fetchProvinces = async () => {
    try {
        const response = await axios.get("/api/provinces");
        if (response.data.success) {
            provinces.value = response.data.data;
        }
        loadingRegions.value = false;
    } catch (error) {
        console.error("Error fetching provinces:", error);
        loadingRegions.value = false;
    }
};

const fetchRegencies = async (provinceId) => {
    if (!provinceId) return;

    try {
        loadingRegions.value = true;
        const response = await axios.get(`/api/regencies/${provinceId}`);
        if (response.data.success) {
            regencies.value = response.data.data;
        } else {
            regencies.value = [];
        }
        loadingRegions.value = false;
    } catch (error) {
        console.error("Error fetching regencies:", error);
        regencies.value = [];
        loadingRegions.value = false;
    }
};

const fetchDistricts = async (regencyId) => {
    if (!regencyId) return;

    try {
        loadingRegions.value = true;
        const response = await axios.get(`/api/districts/${regencyId}`);
        if (response.data.success) {
            districts.value = response.data.data;
        } else {
            districts.value = [];
        }
        loadingRegions.value = false;
    } catch (error) {
        console.error("Error fetching districts:", error);
        districts.value = [];
        loadingRegions.value = false;
    }
};

const fetchVillages = async (districtId) => {
    if (!districtId) return;

    try {
        loadingRegions.value = true;
        const response = await axios.get(`/api/villages/${districtId}`);
        if (response.data.success) {
            villages.value = response.data.data;
        } else {
            villages.value = [];
        }
        loadingRegions.value = false;
    } catch (error) {
        console.error("Error fetching villages:", error);
        villages.value = [];
        loadingRegions.value = false;
    }
};

// Handle region changes
const handleProvinceChange = (provinceId) => {
    form.kabupaten = "";
    form.kecamatan = "";
    form.kelurahan = "";
    regencies.value = [];
    districts.value = [];
    villages.value = [];

    fetchRegencies(provinceId);
};

const handleRegencyChange = (regencyId) => {
    form.kecamatan = "";
    form.kelurahan = "";
    districts.value = [];
    villages.value = [];

    fetchDistricts(regencyId);
};

const handleDistrictChange = (districtId) => {
    form.kelurahan = "";
    villages.value = [];

    fetchVillages(districtId);
};

// Submit the form
const submit = () => {
    form.post(route("install.storeProfile"), {
        preserveScroll: true,
        forceFormData: true,
    });
};

onMounted(() => {
    fetchProvinces();
});
</script>

<template>
    <Head title="Setup Profile Desa" />

    <div class="min-h-screen bg-gray-100 py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center">
                <h1
                    class="text-4xl font-extrabold text-accent1 tracking-tight mb-2"
                >
                    Setup Profile Desa
                </h1>
                <h2 class="text-xl text-gray-600">
                    Langkah 5/6: Lengkapi profil desa Anda
                </h2>
            </div>

            <!-- Content -->
            <div class="bg-white shadow overflow-hidden rounded-lg mt-8">
                <div class="p-6">
                    <form
                        @submit.prevent="submit"
                        enctype="multipart/form-data"
                    >
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4"
                        >
                            <!-- First Column -->
                            <div class="space-y-4">
                                <div>
                                    <SingleLineField
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        fieldName="Nama Desa"
                                        placeholder="Masukkan nama desa"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.name"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <SingleLineField
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        fieldName="Email"
                                        placeholder="Masukkan email desa"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.email"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <SingleLineField
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        fieldName="Nomor Telepon"
                                        placeholder="Masukkan nomor telepon desa"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.phone"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <DropdownFieldInput
                                        id="provinsi"
                                        v-model="form.provinsi"
                                        fieldName="Provinsi"
                                        placeholder="Pilih Provinsi"
                                        :options="
                                            provinces.map((p) => ({
                                                value: p.id,
                                                label: p.name,
                                            }))
                                        "
                                        @update:model-value="
                                            handleProvinceChange
                                        "
                                        required
                                        custom-width="100%"
                                        :loading="loadingRegions"
                                    />
                                    <InputError
                                        :message="form.errors.provinsi"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <DropdownFieldInput
                                        id="kabupaten"
                                        v-model="form.kabupaten"
                                        fieldName="Kabupaten"
                                        placeholder="Pilih Kabupaten"
                                        :options="
                                            regencies.map((r) => ({
                                                value: r.id,
                                                label: r.name,
                                            }))
                                        "
                                        @update:model-value="
                                            handleRegencyChange
                                        "
                                        required
                                        custom-width="100%"
                                        :disabled="
                                            !form.provinsi || loadingRegions
                                        "
                                        :loading="loadingRegions"
                                    />
                                    <InputError
                                        :message="form.errors.kabupaten"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <DropdownFieldInput
                                        id="kecamatan"
                                        v-model="form.kecamatan"
                                        fieldName="Kecamatan"
                                        placeholder="Pilih Kecamatan"
                                        :options="
                                            districts.map((d) => ({
                                                value: d.id,
                                                label: d.name,
                                            }))
                                        "
                                        @update:model-value="
                                            handleDistrictChange
                                        "
                                        required
                                        custom-width="100%"
                                        :disabled="
                                            !form.kabupaten || loadingRegions
                                        "
                                        :loading="loadingRegions"
                                    />
                                    <InputError
                                        :message="form.errors.kecamatan"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <DropdownFieldInput
                                        id="kelurahan"
                                        v-model="form.kelurahan"
                                        fieldName="Kelurahan"
                                        placeholder="Pilih Kelurahan"
                                        :options="
                                            villages.map((v) => ({
                                                value: v.id,
                                                label: v.name,
                                            }))
                                        "
                                        required
                                        custom-width="100%"
                                        :disabled="
                                            !form.kecamatan || loadingRegions
                                        "
                                        :loading="loadingRegions"
                                    />
                                    <InputError
                                        :message="form.errors.kelurahan"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <ImageInput
                                        v-model="form.logo"
                                        label="Logo Desa"
                                        :error="form.errors.logo"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.logo"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <!-- Second Column -->
                            <div class="space-y-4">
                                <div>
                                    <ImageInput
                                        v-model="form.organization_image"
                                        label="Struktur Organisasi"
                                        :error="form.errors.organization_image"
                                        required
                                    />
                                    <InputError
                                        :message="
                                            form.errors.organization_image
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Gambar Hero (3 gambar)</label
                                    >
                                    <input
                                        type="file"
                                        multiple
                                        @input="
                                            form.hero_images =
                                                $event.target.files
                                        "
                                        accept="image/jpeg,image/png,image/jpg"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-accent1 focus:border-accent1"
                                    />
                                    <InputError
                                        :message="
                                            form.errors['hero_images.0'] ||
                                            form.errors['hero_images.1'] ||
                                            form.errors['hero_images.2']
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <TextAreaInput
                                        id="vision"
                                        v-model="form.vision"
                                        type="text"
                                        fieldName="Visi"
                                        placeholder="Masukkan visi desa"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.vision"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <TextAreaInput
                                        id="mission"
                                        v-model="form.mission"
                                        type="text"
                                        fieldName="Misi"
                                        placeholder="Masukkan misi desa (gunakan format list dengan nomor 1., 2., dst)"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.mission"
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- History Field - Full Width -->
                        <div class="mt-6">
                            <TextAreaInput
                                id="history"
                                v-model="form.history"
                                type="text"
                                fieldName="Sejarah Desa"
                                placeholder="Masukkan sejarah desa"
                                required
                                custom-width="100%"
                                rows="5"
                            />
                            <InputError
                                :message="form.errors.history"
                                class="mt-2"
                            />
                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-8 flex justify-end space-x-4">
                            <Link :href="route('install.admin')">
                                <SecondaryButton type="button"
                                    >Kembali</SecondaryButton
                                >
                            </Link>
                            <PrimaryButton :disabled="form.processing"
                                >Simpan Profile</PrimaryButton
                            >
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
