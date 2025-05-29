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

// Add these to handle lembaga and fasilitas
const newLembaga = ref("");
const newFasilitas = ref("");

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
    // Add individual hero images for consistency with Edit component
    hero_image_1: null,
    hero_image_2: null,
    hero_image_3: null,
    organization_image: null,
    history: "",
    vision: "",
    mission: "",
    // Add these arrays for lembaga and fasilitas
    lembaga: [],
    fasilitas: [],
    // Default colors
    primary_color: "#000000",
    secondary_color: "#000000",
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

// Add methods for lembaga and fasilitas (from Edit.vue)
const addLembaga = () => {
    if (newLembaga.value.trim()) {
        // Create a new array by spreading the existing items and adding the new one
        form.lembaga = [...form.lembaga, newLembaga.value];
        newLembaga.value = "";
    }
};

const removeLembaga = (index) => {
    // Create a new array by filtering out the item at the specified index
    form.lembaga = form.lembaga.filter((_, i) => i !== index);
};

const addFasilitas = () => {
    if (newFasilitas.value.trim()) {
        // Create a new array by spreading the existing items and adding the new one
        form.fasilitas = [...form.fasilitas, newFasilitas.value];
        newFasilitas.value = "";
    }
};

const removeFasilitas = (index) => {
    // Create a new array by filtering out the item at the specified index
    form.fasilitas = form.fasilitas.filter((_, i) => i !== index);
};

// Submit the form
const submit = () => {
    // Prepare the form for submission
    // Individual hero images are a fallback if hero_images array is empty
    const formData = new FormData();
    for (const key in form) {
        // Special handling for arrays and objects
        if (key === "lembaga" || key === "fasilitas") {
            formData.append(key, JSON.stringify(form[key]));
        } else {
            formData.append(key, form[key]);
        }
    }

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

                                <!-- Fasilitas Input -->
                                <div>
                                    <div>
                                        <SingleLineField
                                            id="fasilitas"
                                            v-model="newFasilitas"
                                            type="add"
                                            fieldName="Fasilitas"
                                            placeholder="Masukkan fasilitas desa"
                                            custom-width="100%"
                                            @click.prevent="addFasilitas"
                                        />
                                    </div>

                                    <!-- Display fasilitas as tags -->
                                    <div
                                        class="mt-2 flex flex-wrap gap-2"
                                        v-if="form.fasilitas.length > 0"
                                    >
                                        <div
                                            v-for="(
                                                fasilitas, index
                                            ) in form.fasilitas"
                                            :key="index"
                                            class="bg-gray-100 px-3 py-1 rounded flex items-center gap-2 shadow-sm"
                                        >
                                            <span>{{ fasilitas }}</span>
                                            <button
                                                type="button"
                                                class="w-5 h-5 flex items-center justify-center focus:outline-none"
                                                @click.prevent="
                                                    removeFasilitas(index)
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="none"
                                                    class="w-4 h-4"
                                                >
                                                    <path
                                                        d="M6.99967 14.166L5.83301 12.9993L8.83301 9.99935L5.83301 7.02018L6.99967 5.85352L9.99967 8.85352L12.9788 5.85352L14.1455 7.02018L11.1455 9.99935L14.1455 12.9993L12.9788 14.166L9.99967 11.166L6.99967 14.166Z"
                                                        fill="#DC3545"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p
                                        class="text-gray-500 text-sm ml-2"
                                        v-else
                                    >
                                        Tambahkan fasilitas yang ada di desa
                                    </p>
                                    <InputError
                                        :message="form.errors.fasilitas"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <!-- Second Column -->
                            <div class="space-y-4">
                                <!-- Lembaga Input -->
                                <div>
                                    <div>
                                        <SingleLineField
                                            id="lembaga"
                                            v-model="newLembaga"
                                            type="add"
                                            fieldName="Lembaga"
                                            placeholder="Masukkan nama lembaga desa"
                                            custom-width="100%"
                                            @click.prevent="addLembaga"
                                        />
                                    </div>

                                    <!-- Display lembaga as tags -->
                                    <div
                                        class="mt-2 flex flex-wrap gap-2"
                                        v-if="form.lembaga.length > 0"
                                    >
                                        <div
                                            v-for="(
                                                lembaga, index
                                            ) in form.lembaga"
                                            :key="index"
                                            class="bg-gray-100 px-3 py-1 rounded flex items-center gap-2 shadow-sm"
                                        >
                                            <span>{{ lembaga }}</span>
                                            <button
                                                type="button"
                                                class="w-5 h-5 flex items-center justify-center focus:outline-none"
                                                @click.prevent="
                                                    removeLembaga(index)
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="none"
                                                    class="w-4 h-4"
                                                >
                                                    <path
                                                        d="M6.99967 14.166L5.83301 12.9993L8.83301 9.99935L5.83301 7.02018L6.99967 5.85352L9.99967 8.85352L12.9788 5.85352L14.1455 7.02018L11.1455 9.99935L14.1455 12.9993L12.9788 14.166L9.99967 11.166L6.99967 14.166Z"
                                                        fill="#DC3545"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p
                                        class="text-gray-500 text-sm ml-2"
                                        v-else
                                    >
                                        Tambahkan lembaga yang ada di desa
                                    </p>
                                    <InputError
                                        :message="form.errors.lembaga"
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

                                <!-- Updated Hero Images to match Edit.vue -->
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <ImageInput
                                            v-model="form.hero_image_1"
                                            label="Hero Image 1"
                                            :error="form.errors.hero_image_1"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <ImageInput
                                            v-model="form.hero_image_2"
                                            label="Hero Image 2"
                                            :error="form.errors.hero_image_2"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <ImageInput
                                            v-model="form.hero_image_3"
                                            label="Hero Image 3"
                                            :error="form.errors.hero_image_3"
                                            required
                                        />
                                    </div>
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
                                        type="numbers"
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
