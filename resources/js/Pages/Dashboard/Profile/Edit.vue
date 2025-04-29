<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import JsonArrayInput from "@/Components/JsonArrayInput.vue";
import ImageInput from "@/Components/ImageInput.vue";
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";

// Add reactive data for regions
const provinces = ref([]);
const regencies = ref([]);
const districts = ref([]);
const villages = ref([]);

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
    form.post(route("dashboard.profiles.update", props.profile.id), {
        preserveScroll: true,
        forceFormData: true,
    });
};

// Add methods to fetch region data
const fetchProvinces = async () => {
    const response = await axios.get("/api/provinces");
    provinces.value = response.data;
};

const fetchRegencies = async (provinceId) => {
    if (!provinceId) return;
    try {
        const response = await axios.get(`/api/regencies/${provinceId}`);
        regencies.value = response.data;
        if (!props.profile.kabupaten) {
            form.kabupaten = "";
            form.kecamatan = "";
            form.kelurahan = "";
        }
    } catch (error) {
        console.error("Error fetching regencies:", error);
    }
};

const fetchDistricts = async (regencyId) => {
    if (!regencyId) return;
    try {
        const response = await axios.get(`/api/districts/${regencyId}`);
        districts.value = response.data;
        if (!props.profile.kecamatan) {
            form.kecamatan = "";
            form.kelurahan = "";
        }
    } catch (error) {
        console.error("Error fetching districts:", error);
    }
};

const fetchVillages = async (districtId) => {
    if (!districtId) return;
    try {
        const response = await axios.get(`/api/villages/${districtId}`);
        villages.value = response.data;
        if (!props.profile.kelurahan) {
            form.kelurahan = "";
        }
    } catch (error) {
        console.error("Error fetching villages:", error);
    }
};

onMounted(async () => {
    await fetchProvinces();

    if (props.profile.provinsi) {
        await fetchRegencies(props.profile.provinsi);
        if (props.profile.kabupaten) {
            await fetchDistricts(props.profile.kabupaten);
            if (props.profile.kecamatan) {
                await fetchVillages(props.profile.kecamatan);
            }
        }
    }
});
</script>
<template>
    <Head title="Edit Profile Desa" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Village Profile
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
                                <div>
                                    <InputLabel for="email" value="Email" />
                                    <TextInput
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.email"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="phone"
                                        value="Phone Number"
                                    />
                                    <TextInput
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.phone"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Logo Input -->
                                <ImageInput
                                    v-model="form.logo"
                                    label="Village Logo"
                                    :error="form.errors.logo"
                                    :current-image="props.profile.logo"
                                    image-class="h-32 w-32 object-cover rounded-lg"
                                />

                                <!-- Region Fields -->
                                <div>
                                    <InputLabel
                                        for="provinsi"
                                        value="Provinsi"
                                    />
                                    <select
                                        id="provinsi"
                                        v-model="form.provinsi"
                                        @change="
                                            fetchRegencies($event.target.value)
                                        "
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="">
                                            Select Province
                                        </option>
                                        <option
                                            v-for="province in provinces"
                                            :key="province.id"
                                            :value="province.id"
                                        >
                                            {{ province.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.provinsi"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="kabupaten"
                                        value="Kabupaten"
                                    />
                                    <select
                                        id="kabupaten"
                                        v-model="form.kabupaten"
                                        @change="
                                            fetchDistricts($event.target.value)
                                        "
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                        :disabled="!form.provinsi"
                                    >
                                        <option value="">Select Regency</option>
                                        <option
                                            v-for="regency in regencies"
                                            :key="regency.id"
                                            :value="regency.id"
                                        >
                                            {{ regency.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.kabupaten"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="kecamatan"
                                        value="Kecamatan"
                                    />
                                    <select
                                        id="kecamatan"
                                        v-model="form.kecamatan"
                                        @change="
                                            fetchVillages($event.target.value)
                                        "
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                        :disabled="!form.kabupaten"
                                    >
                                        <option value="">
                                            Select District
                                        </option>
                                        <option
                                            v-for="district in districts"
                                            :key="district.id"
                                            :value="district.id"
                                        >
                                            {{ district.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.kecamatan"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="kelurahan"
                                        value="Kelurahan"
                                    />
                                    <select
                                        id="kelurahan"
                                        v-model="form.kelurahan"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                        :disabled="!form.kecamatan"
                                    >
                                        <option value="">Select Village</option>
                                        <option
                                            v-for="village in villages"
                                            :key="village.id"
                                            :value="village.id"
                                        >
                                            {{ village.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.kelurahan"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Organization Image Input -->
                                <ImageInput
                                    v-model="form.organization_image"
                                    label="Organization Image"
                                    :error="form.errors.organization_image"
                                    :current-image="
                                        props.profile.organization_image
                                    "
                                    image-class="h-32 w-auto object-cover rounded-lg"
                                />

                                <!-- Hero Images -->
                                <div class="col-span-2 grid grid-cols-3 gap-4">
                                    <ImageInput
                                        v-model="form.hero_image_1"
                                        label="Hero Image 1"
                                        :error="form.errors.hero_image_1"
                                        :current-image="
                                            form.currentHeroImages[0]
                                        "
                                        image-class="h-32 w-full object-cover rounded-lg"
                                    />
                                    <ImageInput
                                        v-model="form.hero_image_2"
                                        label="Hero Image 2"
                                        :error="form.errors.hero_image_2"
                                        :current-image="
                                            form.currentHeroImages[1]
                                        "
                                        image-class="h-32 w-full object-cover rounded-lg"
                                    />
                                    <ImageInput
                                        v-model="form.hero_image_3"
                                        label="Hero Image 3"
                                        :error="form.errors.hero_image_3"
                                        :current-image="
                                            form.currentHeroImages[2]
                                        "
                                        image-class="h-32 w-full object-cover rounded-lg"
                                    />
                                </div>

                                <div class="col-span-2 mb-4">
                                    <JsonArrayInput
                                        v-model="form.fasilitas"
                                        label="Fasilitas"
                                        placeholder="Masukkan fasilitas desa"
                                        :error="form.errors.fasilitas"
                                    />
                                </div>

                                <!-- Add new lembaga input -->
                                <div class="col-span-2 mb-4">
                                    <JsonArrayInput
                                        v-model="form.lembaga"
                                        label="Lembaga"
                                        placeholder="Masukkan nama lembaga desa"
                                        :error="form.errors.lembaga"
                                    />
                                </div>

                                <!-- Colors -->
                                <div>
                                    <InputLabel
                                        for="primary_color"
                                        value="Primary Color"
                                    />
                                    <select
                                        id="primary_color"
                                        v-model="form.primary_color"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="#000000">Black</option>
                                        <option value="#ffffff">White</option>
                                    </select>
                                    <InputError
                                        :message="form.errors.primary_color"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="secondary_color"
                                        value="Secondary Color"
                                    />
                                    <select
                                        id="secondary_color"
                                        v-model="form.secondary_color"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="#000000">Black</option>
                                        <option value="#ffffff">White</option>
                                    </select>
                                    <InputError
                                        :message="form.errors.secondary_color"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="col-span-2">
                                    <InputLabel for="vision" value="Vision" />
                                    <TextInput
                                        id="vision"
                                        v-model="form.vision"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.vision"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="col-span-2">
                                    <InputLabel for="mission" value="Mission" />
                                    <textarea
                                        id="mission"
                                        v-model="form.mission"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        rows="4"
                                        required
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.mission"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="col-span-2">
                                    <InputLabel
                                        for="history"
                                        value="Village History"
                                    />
                                    <textarea
                                        id="history"
                                        v-model="form.history"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        rows="6"
                                        required
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.history"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Update Village Profile
                                </PrimaryButton>
                                <Link :href="route('dashboard.profiles')">
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
