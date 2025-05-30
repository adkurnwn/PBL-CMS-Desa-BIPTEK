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

    <div
        class="min-h-screen bg-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-5xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h1
                    class="text-4xl font-extrabold text-accent1 tracking-tight mb-2"
                >
                    Setup Profile Desa
                </h1>
                <h2 class="text-xl text-gray-600">
                    Langkah 6/7: Lengkapi profil desa Anda
                </h2>
            </div>

            <!-- Content -->
            <div class="bg-white shadow overflow-hidden rounded-lg mt-8">
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-center mb-8">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="80"
                            viewBox="0 -960 960 960"
                            width="80"
                            class="mx-auto text-accent1 fill-current"
                        >
                            <path
                                d="M400-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18h14q6 0 12 2-8 18-13.5 37.5T404-360h-4q-71 0-127.5 18T180-306q-9 5-14.5 14t-5.5 20v32h252q6 21 16 41.5t22 38.5H80Zm560 40-12-60q-12-5-22.5-10.5T584-204l-58 18-40-68 46-40q-2-14-2-26t2-26l-46-40 40-68 58 18q11-7 21.5-12.5T628-460l12-60h80l12 60q12 5 22.5 10.5T776-436l58-18 40 68-46 40q2 14 2 26t-2 26l46 40-40 68-58-18q-11 7-21.5 12.5T732-180l-12 60h-80Zm40-120q33 0 56.5-23.5T760-320q0-33-23.5-56.5T680-400q-33 0-56.5 23.5T600-320q0 33 23.5 56.5T680-240Z"
                            />
                        </svg>
                        <h3 class="mt-4 text-2xl font-medium text-gray-900">
                            Profile Desa
                        </h3>
                    </div>
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
                                    <div class="flex items-center gap-2">
                                        <SingleLineField
                                            id="fasilitas"
                                            v-model="newFasilitas"
                                            type="text"
                                            fieldName="Fasilitas"
                                            placeholder="Masukkan fasilitas desa"
                                            custom-width="100%"
                                        />
                                        <button
                                            type="button"
                                            @click="addFasilitas"
                                            class="px-3 py-2 bg-accent1 text-white rounded hover:bg-accent1/90"
                                        >
                                            Tambah
                                        </button>
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
                                    <div class="flex items-center gap-2">
                                        <SingleLineField
                                            id="lembaga"
                                            v-model="newLembaga"
                                            type="text"
                                            fieldName="Lembaga"
                                            placeholder="Masukkan nama lembaga desa"
                                            custom-width="100%"
                                        />
                                        <button
                                            type="button"
                                            @click="addLembaga"
                                            class="px-3 py-2 bg-accent1 text-white rounded hover:bg-accent1/90"
                                        >
                                            Tambah
                                        </button>
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
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between">
                    <Link
                        :href="route('install.admin')"
                        class="inline-flex justify-center rounded-md border border-gray-300 py-2 px-4 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent1 focus:ring-offset-2"
                    >
                        Kembali
                    </Link>
                    <button
                        type="button"
                        @click="submit"
                        :disabled="form.processing"
                        class="inline-flex justify-center rounded-md border border-transparent bg-accent1 py-2 px-6 text-base font-medium text-white shadow-sm hover:bg-accent1/90 focus:outline-none focus:ring-2 focus:ring-accent1 focus:ring-offset-2 disabled:opacity-70"
                    >
                        <span v-if="form.processing">
                            <svg
                                class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            Sedang Memproses...
                        </span>
                        <span v-else>Simpan Profil</span>
                    </button>
                </div>
            </div>
            <!-- Installation progress -->
            <div class="mt-8">
                <div class="flex justify-between">
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            1
                        </div>
                        <div class="text-xs mt-2 text-gray-600">
                            Selamat Datang
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            2
                        </div>
                        <div class="text-xs mt-2 text-gray-600">
                            Persyaratan
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            3
                        </div>
                        <div class="text-xs mt-2 text-gray-600">
                            Izin Direktori
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            4
                        </div>
                        <div class="text-xs mt-2 text-gray-600">Database</div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            5
                        </div>
                        <div class="text-xs mt-2 text-gray-600">Admin</div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-accent1 text-white font-bold"
                        >
                            6
                        </div>
                        <div class="text-xs mt-2 text-gray-600 font-bold">
                            Profile Desa
                        </div>
                    </div>
                    <div class="w-1/5 text-center">
                        <div
                            class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold"
                        >
                            7
                        </div>
                        <div class="text-xs mt-2 text-gray-400">Selesai</div>
                    </div>
                </div>
                <div class="relative flex items-center justify-center mt-2">
                    <div class="h-1 w-full bg-gray-200">
                        <div class="h-1 bg-accent1" style="width: 79%"></div>
                    </div>
                </div>
            </div>

            <!-- Disclaimer -->
            <div class="text-center text-xs text-gray-500">
                <p>CMS Desa BIPTEK &copy; {{ new Date().getFullYear() }}</p>
                <p>
                    Dibuat untuk membantu pembangunan digital desa di Indonesia
                </p>
            </div>
        </div>
    </div>
</template>
