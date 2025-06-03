<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";
import JsonArrayInput from "@/Components/JsonArrayInput.vue";
import ImageInput from "@/Components/DashboardComponent/ImageInput.vue";
import { ref, onMounted, computed, watch } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import axios from "axios";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import DropdownFieldInput from "@/Components/DashboardComponent/DropdownFieldInput.vue";
import TextAreaInput from "@/Components/DashboardComponent/TextAreaInput.vue";

// Add reactive data for regions
const provinces = ref([]);
const regencies = ref([]);
const districts = ref([]);
const villages = ref([]);

// Add reactive data to track loading state
const loadingRegions = ref(true);
const regionDataReady = ref(false);

const props = defineProps({
    profile: Object,
});

// Add this function to parse hero images
const parseHeroImages = () => {
    if (typeof props.profile.hero_images === "string") {
        try {
            return JSON.parse(props.profile.hero_images);
        } catch (e) {
            return [];
        }
    }
    return Array.isArray(props.profile.hero_images)
        ? props.profile.hero_images
        : [];
};

const parseJson = (value) => {
    if (typeof value === "string") {
        try {
            return JSON.parse(value);
        } catch (e) {
            return [];
        }
    }
    return Array.isArray(value) ? value : [];
};

const heroImages = parseHeroImages();

const form = useForm({
    email: props.profile.email,
    phone: props.profile.phone,
    address: props.profile.address,
    logo: "",
    provinsi: props.profile.provinsi,
    kabupaten: props.profile.kabupaten,
    kecamatan: props.profile.kecamatan,
    kelurahan: props.profile.kelurahan,
    kode_pos: props.profile.kode_pos,
    hero_image: "",
    organization_image: "",
    hero_image_1: "",
    hero_image_2: "",
    hero_image_3: "",
    currentHeroImages: heroImages,
    lembaga: parseJson(props.profile.lembaga),
    fasilitas: parseJson(props.profile.fasilitas),
    history: props.profile.history,
    vision: props.profile.vision,
    mission: props.profile.mission,
    primary_color: props.profile.primary_color,
    secondary_color: props.profile.secondary_color,
    _method: "PUT",
});

const submit = () => {
    form.post(route("dashboard.profiles.update", props.profile.name), {
        preserveScroll: true,
        forceFormData: true,
    });
};

// Add computed properties to find the display values for dropdown components
const getSelectedRegion = (id, options) => {
    if (!id || !options || options.length === 0) return null;
    return options.find((option) => option.value === id);
};

// Add a watcher to set regionDataReady when all region data is loaded
watch(
    [provinces, regencies, districts, villages],
    ([newProvinces, newRegencies, newDistricts, newVillages]) => {
        if (
            props.profile.provinsi &&
            props.profile.kabupaten &&
            props.profile.kecamatan &&
            props.profile.kelurahan
        ) {
            if (
                newProvinces.length > 0 &&
                newRegencies.length > 0 &&
                newDistricts.length > 0 &&
                newVillages.length > 0
            ) {
                regionDataReady.value = true;
                loadingRegions.value = false;
            }
        }
    },
    { deep: true }
);

// Add reactive variables to store region names
const provinceName = ref("");
const regencyName = ref("");
const districtName = ref("");
const villageName = ref("");

// Modify the fetch methods to handle the API response structure
const fetchProvinces = async () => {
    try {
        const response = await axios.get("/api/provinces");
        // Handle the response structure with success and data properties
        if (response.data.success) {
            provinces.value = response.data.data;
        } else {
            provinces.value = [];
        }

        // Get the province name if we have a province ID
        if (props.profile.provinsi) {
            const province = provinces.value.find(
                (p) => p.id === props.profile.provinsi
            );
            if (province) {
                provinceName.value = province.name;
            }
            await fetchRegencies(props.profile.provinsi);
        } else {
            loadingRegions.value = false;
        }
    } catch (error) {
        console.error("Error fetching provinces:", error);
        provinces.value = [];
        loadingRegions.value = false;
    }
};

const fetchRegencies = async (provinceId) => {
    if (!provinceId) {
        if (props.profile.provinsi) loadingRegions.value = false;
        return;
    }

    try {
        const response = await axios.get(`/api/regencies/${provinceId}`);
        // Handle the response structure with success and data properties
        if (response.data.success) {
            regencies.value = response.data.data;
        } else {
            regencies.value = [];
        }

        // Get the regency name if we have a regency ID
        if (props.profile.kabupaten) {
            const regency = regencies.value.find(
                (r) => r.id === props.profile.kabupaten
            );
            if (regency) {
                regencyName.value = regency.name;
            }
            await fetchDistricts(props.profile.kabupaten);
        } else {
            loadingRegions.value = false;
        }
    } catch (error) {
        console.error("Error fetching regencies:", error);
        regencies.value = [];
        loadingRegions.value = false;
    }
};

const fetchDistricts = async (regencyId) => {
    if (!regencyId) {
        if (props.profile.kabupaten) loadingRegions.value = false;
        return;
    }

    try {
        const response = await axios.get(`/api/districts/${regencyId}`);
        // Handle the response structure with success and data properties
        if (response.data.success) {
            districts.value = response.data.data;
        } else {
            districts.value = [];
        }

        // Get the district name if we have a district ID
        if (props.profile.kecamatan) {
            const district = districts.value.find(
                (d) => d.id === props.profile.kecamatan
            );
            if (district) {
                districtName.value = district.name;
            }
            await fetchVillages(props.profile.kecamatan);
        } else {
            loadingRegions.value = false;
        }
    } catch (error) {
        console.error("Error fetching districts:", error);
        districts.value = [];
        loadingRegions.value = false;
    }
};

const fetchVillages = async (districtId) => {
    if (!districtId) {
        if (props.profile.kecamatan) loadingRegions.value = false;
        return;
    }

    try {
        const response = await axios.get(`/api/villages/${districtId}`);
        // Handle the response structure with success and data properties
        if (response.data.success) {
            villages.value = response.data.data;
        } else {
            villages.value = [];
        }

        // Get the village name if we have a village ID
        if (props.profile.kelurahan) {
            const village = villages.value.find(
                (v) => v.id === props.profile.kelurahan
            );
            if (village) {
                villageName.value = village.name;
            }
        }
        loadingRegions.value = false;
    } catch (error) {
        console.error("Error fetching villages:", error);
        villages.value = [];
        loadingRegions.value = false;
    }
};

// Handle changes to provinces in the dropdown
const handleProvinceChange = (provinceId) => {
    // Reset child dropdowns if province changes
    if (provinceId !== props.profile.provinsi) {
        form.kabupaten = "";
        form.kecamatan = "";
        form.kelurahan = "";
        regencies.value = [];
        districts.value = [];
        villages.value = [];
    }

    fetchRegencies(provinceId);
};

// Handle changes to regencies in the dropdown
const handleRegencyChange = (regencyId) => {
    // Reset child dropdowns if regency changes
    if (regencyId !== props.profile.kabupaten) {
        form.kecamatan = "";
        form.kelurahan = "";
        districts.value = [];
        villages.value = [];
    }

    fetchDistricts(regencyId);
};

// Handle changes to districts in the dropdown
const handleDistrictChange = (districtId) => {
    // Reset village dropdown if district changes
    if (districtId !== props.profile.kecamatan) {
        form.kelurahan = "";
        villages.value = [];
    }

    fetchVillages(districtId);
};

onMounted(() => {
    // Start the chain of fetching region data
    fetchProvinces();
});

// Add refs for managing fasilitas and lembaga inputs
const newFasilitas = ref("");
const newLembaga = ref("");

// Add methods to add and remove items
const addFasilitas = (event) => {
    if (event && event.preventDefault) {
        event.preventDefault();
    }
    if (newFasilitas.value.trim()) {
        // Create a new array by spreading the existing items and adding the new one
        // This is important to ensure reactivity
        form.fasilitas = [...form.fasilitas, newFasilitas.value];
        newFasilitas.value = "";
    }
};

const removeFasilitas = (index) => {
    // Create a new array by filtering out the item at the specified index
    form.fasilitas = form.fasilitas.filter((_, i) => i !== index);
};

const addLembaga = (event) => {
    if (event && event.preventDefault) {
        event.preventDefault();
    }
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
</script>
<template>
    <Head title="Profile Desa" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Profile Desa">
                <template #icon>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        height="24px"
                        viewBox="0 -960 960 960"
                        width="24px"
                        fill="#40483D"
                    >
                        <path
                            d="M80-160v-400l240-240 240 240v400H80Zm80-80h120v-120h80v120h120v-287L320-687 160-527v287Zm120-200v-80h80v80h-80Zm360 280v-433L433-800h113l174 174v466h-80Zm160 0v-499L659-800h113l108 108v532h-80Zm-640-80h320-320Z"
                        />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area> Edit Profile Desa </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <div class="overflow-hidden bg-white">
                <div class="">
                    <form
                        @submit.prevent="submit"
                        enctype="multipart/form-data"
                    >
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-[22px] gap-y-[15px]"
                        >
                            <div class="flex flex-col gap-y-[15px]">
                                <!-- First column -->
                                <div>
                                    <SingleLineField
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        fieldName="Email"
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
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.phone"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Region Fields -->
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
                                        :initial-label="provinceName"
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
                                        :initial-label="regencyName"
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
                                        :initial-label="districtName"
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
                                        :initial-label="villageName"
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
                                        fieldName="Kode Pos"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.kode_pos"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="">
                                    <div class="">
                                        <div class="">
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
                                        class="text-menuText text-[6.2px] sm:text-[7px] md:text-[8px] lg:text-[8.7px] xl:text-[9.33px] 2xl:text-[10.8px] 3xl:text-[12.4px] 4xl:text-[14px] ml-[5.3px] sm:ml-[6px] md:ml-[6.7px] lg:ml-[7.3px] xl:ml-[8px] 2xl:ml-[9.3px] 3xl:ml-[10.7px] 4xl:ml-[12px]"
                                        v-else
                                    >
                                        Tambahkan fasilitas yang ada di desa
                                    </p>
                                    <InputError
                                        :message="form.errors.fasilitas"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="">
                                    <div class="">
                                        <div class="">
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
                                        class="text-menuText text-[6.2px] sm:text-[7px] md:text-[8px] lg:text-[8.7px] xl:text-[9.33px] 2xl:text-[10.8px] 3xl:text-[12.4px] 4xl:text-[14px] ml-[5.3px] sm:ml-[6px] md:ml-[6.7px] lg:ml-[7.3px] xl:ml-[8px] 2xl:ml-[9.3px] 3xl:ml-[10.7px] 4xl:ml-[12px]"
                                        v-else
                                    >
                                        Tambahkan lembaga yang ada di desa
                                    </p>
                                    <InputError
                                        :message="form.errors.lembaga"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="flex flex-col gap-y-[15px]">
                                <!-- Second column -->
                                <ImageInput
                                    v-model="form.logo"
                                    label="Village Logo"
                                    :error="form.errors.logo"
                                    :current-image="props.profile.logo"
                                    image-height="80px"
                                />
                                <!-- Organization Image Input -->
                                <ImageInput
                                    v-model="form.organization_image"
                                    label="Organization Image"
                                    :error="form.errors.organization_image"
                                    :current-image="
                                        props.profile.organization_image
                                    "
                                />

                                <!-- Hero Images -->
                                <div class="col-span-2 grid grid-cols-3 gap-1">
                                    <ImageInput
                                        v-model="form.hero_image_1"
                                        label="Hero Image 1"
                                        :error="form.errors.hero_image_1"
                                        :current-image="
                                            form.currentHeroImages[0]
                                        "
                                    />
                                    <ImageInput
                                        v-model="form.hero_image_2"
                                        label="Hero Image 2"
                                        :error="form.errors.hero_image_2"
                                        :current-image="
                                            form.currentHeroImages[1]
                                        "
                                    />
                                    <ImageInput
                                        v-model="form.hero_image_3"
                                        label="Hero Image 3"
                                        :error="form.errors.hero_image_3"
                                        :current-image="
                                            form.currentHeroImages[2]
                                        "
                                    />
                                </div>

                                <div>
                                    <TextAreaInput
                                        id="vision"
                                        v-model="form.vision"
                                        type="text"
                                        fieldName="Visi"
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
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.vision"
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Full-width fields -->
                        <div class="mt-[15px]">
                            <div>
                                <TextAreaInput
                                    id="history"
                                    v-model="form.history"
                                    type="text"
                                    fieldName="Sejarah Desa"
                                    required
                                    custom-width="100%"
                                />
                                <InputError
                                    :message="form.errors.vision"
                                    class="mt-2"
                                />
                            </div>
                        </div>

                        <div class="mt-[15px] flex justify-end">
                            <Link :href="route('dashboard')" class="mr-[15px]">
                                <SecondaryButton type="button">
                                    BATAL
                                </SecondaryButton>
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                UPDATE
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
