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

// Form - Add missing fields
const form = useForm({
    name: "",
    email: "",
    phone: "",
    logo: null,
    tipe_pemerintahan: "",
    provinsi: "",
    kabupaten: "",
    kecamatan: "",
    kelurahan: "",
    kode_pos: "",
    hero_image_1: null,
    hero_image_2: null,
    hero_image_3: null,
    organization_image: null,
    history: "",
    vision: "",
    mission: "",
    lembaga: [],
    fasilitas: [],
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
                        <!-- Kalimat Pengingat -->
                        <div
                            class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg"
                        >
                            <div class="flex items-center justify-center">
                                <svg
                                    class="w-5 h-5 text-yellow-600 mr-2"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                <p class="text-yellow-800 text-sm font-medium">
                                    Mohon lengkapi semua kolom untuk melanjutkan
                                    ke tahap berikutnya
                                </p>
                            </div>
                        </div>
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
                                        fieldName="Email *"
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
                                        fieldName="Nomor Telepon *"
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
                                        id="tipe_pemerintahan"
                                        v-model="form.tipe_pemerintahan"
                                        fieldName="Tipe Pemerintahan *"
                                        placeholder="Pilih Tipe Pemerintahan"
                                        :options="[
                                            { value: 'desa', label: 'Desa' },
                                            {
                                                value: 'kelurahan',
                                                label: 'Kelurahan',
                                            },
                                        ]"
                                        required
                                        custom-width="100%"
                                    />
                                </div>

                                <div>
                                    <DropdownFieldInput
                                        id="provinsi"
                                        v-model="form.provinsi"
                                        fieldName="Provinsi *"
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
                                        fieldName="Kabupaten *"
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
                                        fieldName="Kecamatan *"
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
                                        fieldName="Kelurahan *"
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
                                    <SingleLineField
                                        id="kode_pos"
                                        v-model="form.kode_pos"
                                        type="text"
                                        fieldName="Kode Pos *"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.kode_pos"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Fasilitas Input -->
                                <div>
                                    <div class="flex items-center gap-2">
                                        <SingleLineField
                                            id="fasilitas"
                                            v-model="newFasilitas"
                                            type="add"
                                            fieldName="Fasilitas"
                                            placeholder="Masukkan fasilitas desa"
                                            custom-width="100%"
                                            @click="addFasilitas"
                                        />
                                    </div>

                                    <!-- Display fasilitas as tags -->
                                    <div
                                        class="mt-[0.9px] sm:mt-[1px] md:mt-[1.1px] lg:mt-[1.2px] xl:mt-[1.33px] 2xl:mt-[1.6px] 3xl:mt-[1.8px] 4xl:mt-[2px] flex flex-wrap gap-[6.7px] sm:gap-[7.5px] md:gap-[8.5px] lg:gap-[9.2px] xl:gap-[10px] 2xl:gap-[11.5px] 3xl:gap-[13.3px] 4xl:gap-[15px]"
                                        v-if="form.fasilitas.length > 0"
                                    >
                                        <div
                                            v-for="(
                                                fasilitas, index
                                            ) in form.fasilitas"
                                            :key="index"
                                            class="bg-[#f3f4f6] h-[18.7px] sm:h-[21.1px] md:h-[23.7px] lg:h-[25.7px] xl:h-[28px] 2xl:h-[32px] 3xl:h-[37px] 4xl:h-[42px] px-[6px] sm:px-[7px] md:px-[8px] lg:px-[9px] xl:px-[10px] 2xl:px-[11.5px] 3xl:px-[13.3px] 4xl:px-[15px] rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] flex items-center gap-[0.9px] sm:gap-[1px] md:gap-[1.1px] lg:gap-[1.2px] xl:gap-[1.33px] 2xl:gap-[1.6px] 3xl:gap-[1.8px] 4xl:gap-[2px] shadow-[0_0.9px_0.9px_0px_rgba(0,0,0,0.25)] sm:shadow-[0_1px_1px_0px_rgba(0,0,0,0.25)] md:shadow-[0_1.1px_1.1px_0px_rgba(0,0,0,0.25)] lg:shadow-[0_1.2px_1.2px_0px_rgba(0,0,0,0.25)] xl:shadow-[0_1.33px_1.33px_0px_rgba(0,0,0,0.25)] 2xl:shadow-[0_1.6px_1.6px_0px_rgba(0,0,0,0.25)] 3xl:shadow-[0_1.8px_1.8px_0px_rgba(0,0,0,0.25)] 4xl:shadow-[0_2px_2px_0px_rgba(0,0,0,0.25)]"
                                        >
                                            <span
                                                class="text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                                                >{{ fasilitas }}</span
                                            >
                                            <button
                                                type="button"
                                                class="w-[8.9px] h-[8.9px] sm:w-[10.1px] sm:h-[10.1px] md:w-[11.5px] md:h-[11.5px] lg:w-[12.5px] lg:h-[12.5px] xl:w-[13.3px] xl:h-[13.3px] 2xl:w-[15.4px] 2xl:h-[15.4px] 3xl:w-[17.5px] 3xl:h-[17.5px] 4xl:w-[20px] 4xl:h-[20px] flex items-center justify-center focus:outline-none ml-[4px]"
                                                @click.prevent="
                                                    removeFasilitas(index)
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="none"
                                                    class="w-[8.9px] h-[8.9px] sm:w-[10.1px] sm:h-[10.1px] md:w-[11.5px] md:h-[11.5px] lg:w-[12.5px] lg:h-[12.5px] xl:w-[13.3px] xl:h-[13.3px] 2xl:w-[15.4px] 2xl:h-[15.4px] 3xl:w-[17.5px] 3xl:h-[17.5px] 4xl:w-[20px] 4xl:h-[20px]"
                                                >
                                                    <mask
                                                        id="mask0_fasilitas"
                                                        style="mask-type: alpha"
                                                        maskUnits="userSpaceOnUse"
                                                        x="0"
                                                        y="0"
                                                        width="20"
                                                        height="20"
                                                    >
                                                        <rect
                                                            width="20"
                                                            height="20"
                                                            fill="#D9D9D9"
                                                        />
                                                    </mask>
                                                    <g
                                                        mask="url(#mask0_fasilitas)"
                                                    >
                                                        <path
                                                            d="M6.99967 14.166L5.83301 12.9993L8.83301 9.99935L5.83301 7.02018L6.99967 5.85352L9.99967 8.85352L12.9788 5.85352L14.1455 7.02018L11.1455 9.99935L14.1455 12.9993L12.9788 14.166L9.99967 11.166L6.99967 14.166Z"
                                                            fill="#DC3545"
                                                        />
                                                    </g>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p
                                        class="text-gray-500 text-sm ml-2"
                                        v-else
                                    >
                                        Tambahkan minimal satu fasilitas yang
                                        ada di desa
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
                                            type="add"
                                            fieldName="Lembaga"
                                            placeholder="Masukkan nama lembaga desa"
                                            custom-width="100%"
                                            @click="addLembaga"
                                        />
                                    </div>

                                    <!-- Display lembaga as tags -->
                                    <div
                                        class="mt-[0.9px] sm:mt-[1px] md:mt-[1.1px] lg:mt-[1.2px] xl:mt-[1.33px] 2xl:mt-[1.6px] 3xl:mt-[1.8px] 4xl:mt-[2px] flex flex-wrap gap-[6.7px] sm:gap-[7.5px] md:gap-[8.5px] lg:gap-[9.2px] xl:gap-[10px] 2xl:gap-[11.5px] 3xl:gap-[13.3px] 4xl:gap-[15px]"
                                        v-if="form.lembaga.length > 0"
                                    >
                                        <div
                                            v-for="(
                                                lembaga, index
                                            ) in form.lembaga"
                                            :key="index"
                                            class="bg-[#f3f4f6] h-[18.7px] sm:h-[21.1px] md:h-[23.7px] lg:h-[25.7px] xl:h-[28px] 2xl:h-[32px] 3xl:h-[37px] 4xl:h-[42px] px-[6px] sm:px-[7px] md:px-[8px] lg:px-[9px] xl:px-[10px] 2xl:px-[11.5px] 3xl:px-[13.3px] 4xl:px-[15px] rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] flex items-center gap-[0.9px] sm:gap-[1px] md:gap-[1.1px] lg:gap-[1.2px] xl:gap-[1.33px] 2xl:gap-[1.6px] 3xl:gap-[1.8px] 4xl:gap-[2px] shadow-[0_0.9px_0.9px_0px_rgba(0,0,0,0.25)] sm:shadow-[0_1px_1px_0px_rgba(0,0,0,0.25)] md:shadow-[0_1.1px_1.1px_0px_rgba(0,0,0,0.25)] lg:shadow-[0_1.2px_1.2px_0px_rgba(0,0,0,0.25)] xl:shadow-[0_1.33px_1.33px_0px_rgba(0,0,0,0.25)] 2xl:shadow-[0_1.6px_1.6px_0px_rgba(0,0,0,0.25)] 3xl:shadow-[0_1.8px_1.8px_0px_rgba(0,0,0,0.25)] 4xl:shadow-[0_2px_2px_0px_rgba(0,0,0,0.25)]"
                                        >
                                            <span
                                                class="text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                                                >{{ lembaga }}</span
                                            >
                                            <button
                                                type="button"
                                                class="w-[8.9px] h-[8.9px] sm:w-[10.1px] sm:h-[10.1px] md:w-[11.5px] md:h-[11.5px] lg:w-[12.5px] lg:h-[12.5px] xl:w-[13.3px] xl:h-[13.3px] 2xl:w-[15.4px] 2xl:h-[15.4px] 3xl:w-[17.5px] 3xl:h-[17.5px] 4xl:w-[20px] 4xl:h-[20px] flex items-center justify-center focus:outline-none ml-[4px]"
                                                @click.prevent="
                                                    removeLembaga(index)
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="none"
                                                    class="w-[8.9px] h-[8.9px] sm:w-[10.1px] sm:h-[10.1px] md:w-[11.5px] md:h-[11.5px] lg:w-[12.5px] lg:h-[12.5px] xl:w-[13.3px] xl:h-[13.3px] 2xl:w-[15.4px] 2xl:h-[15.4px] 3xl:w-[17.5px] 3xl:h-[17.5px] 4xl:w-[20px] 4xl:h-[20px]"
                                                >
                                                    <mask
                                                        id="mask0_lembaga"
                                                        style="mask-type: alpha"
                                                        maskUnits="userSpaceOnUse"
                                                        x="0"
                                                        y="0"
                                                        width="20"
                                                        height="20"
                                                    >
                                                        <rect
                                                            width="20"
                                                            height="20"
                                                            fill="#D9D9D9"
                                                        />
                                                    </mask>
                                                    <g
                                                        mask="url(#mask0_lembaga)"
                                                    >
                                                        <path
                                                            d="M6.99967 14.166L5.83301 12.9993L8.83301 9.99935L5.83301 7.02018L6.99967 5.85352L9.99967 8.85352L12.9788 5.85352L14.1455 7.02018L11.1455 9.99935L14.1455 12.9993L12.9788 14.166L9.99967 11.166L6.99967 14.166Z"
                                                            fill="#DC3545"
                                                        />
                                                    </g>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p
                                        class="text-gray-500 text-sm ml-2"
                                        v-else
                                    >
                                        Tambahkan minimal satu lembaga yang ada
                                        di desa
                                    </p>
                                    <InputError
                                        :message="form.errors.lembaga"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <ImageInput
                                        v-model="form.logo"
                                        label="Village Logo"
                                        :error="form.errors.logo"
                                        image-height="80px"
                                    />
                                    <InputError
                                        :message="form.errors.logo"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <ImageInput
                                        v-model="form.organization_image"
                                        label="Struktur Organisasi *"
                                        :error="form.errors.organization_image"
                                        required
                                        image-height="250px"
                                    />
                                    <InputError
                                        :message="
                                            form.errors.organization_image
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Hero Images -->
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <ImageInput
                                            v-model="form.hero_image_1"
                                            label="Hero Image 1 *"
                                            :error="form.errors.hero_image_1"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <ImageInput
                                            v-model="form.hero_image_2"
                                            label="Hero Image 2 *"
                                            :error="form.errors.hero_image_2"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <ImageInput
                                            v-model="form.hero_image_3"
                                            label="Hero Image 3 *"
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
                                        fieldName="Visi *"
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
                                        fieldName="Misi *"
                                        placeholder="Masukkan misi desa"
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
                                fieldName="Sejarah Desa *"
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
                    <Link :href="route('install.admin')">
                        <SecondaryButton
                            :disabled="form.processing"
                            type="button"
                        >
                            Kembali
                        </SecondaryButton>
                    </Link>
                    <PrimaryButton :disabled="form.processing" @click="submit">
                        Simpan Profile Desa
                    </PrimaryButton>
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
