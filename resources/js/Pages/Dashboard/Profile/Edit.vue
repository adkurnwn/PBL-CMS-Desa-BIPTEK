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
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
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

    <Head title="Profile Desa" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Profile Desa">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="26" viewBox="0 0 25 26" fill="none">
                        <path
                            d="M12.4998 13.0003C10.804 13.0003 9.35227 12.3965 8.14463 11.1889C6.93699 9.98123 6.33317 8.52949 6.33317 6.83366C6.33317 5.13783 6.93699 3.68609 8.14463 2.47845C9.35227 1.27081 10.804 0.666992 12.4998 0.666992C14.1957 0.666992 15.6474 1.27081 16.855 2.47845C18.0627 3.68609 18.6665 5.13783 18.6665 6.83366C18.6665 8.52949 18.0627 9.98123 16.855 11.1889C15.6474 12.3965 14.1957 13.0003 12.4998 13.0003ZM0.166504 25.3337V21.017C0.166504 20.1434 0.39133 19.3404 0.840983 18.6081C1.29064 17.8758 1.88803 17.317 2.63317 16.9316C4.22623 16.135 5.84498 15.5377 7.48942 15.1394C9.13386 14.7411 10.804 14.542 12.4998 14.542C14.1957 14.542 15.8658 14.7411 17.5103 15.1394C19.1547 15.5377 20.7734 16.135 22.3665 16.9316C23.1116 17.317 23.709 17.8758 24.1587 18.6081C24.6083 19.3404 24.8332 20.1434 24.8332 21.017V25.3337H0.166504Z"
                            fill="#4C7660" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="email" value="Email" />
                                    <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full"
                                        required />
                                    <InputError :message="form.errors.email" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="phone" value="Phone Number" />
                                    <TextInput id="phone" v-model="form.phone" type="text" class="mt-1 block w-full"
                                        required />
                                    <InputError :message="form.errors.phone" class="mt-2" />
                                </div>

                                <!-- Logo Input -->
                                <ImageInput v-model="form.logo" label="Village Logo" :error="form.errors.logo"
                                    :current-image="props.profile.logo"
                                    image-class="h-32 w-32 object-cover rounded-lg" />

                                <!-- Region Fields -->
                                <div>
                                    <InputLabel for="provinsi" value="Provinsi" />
                                    <select id="provinsi" v-model="form.provinsi" @change="
                                        fetchRegencies($event.target.value)
                                        "
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                        <option value="">
                                            Select Province
                                        </option>
                                        <option v-for="province in provinces" :key="province.id" :value="province.id">
                                            {{ province.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.provinsi" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="kabupaten" value="Kabupaten" />
                                    <select id="kabupaten" v-model="form.kabupaten" @change="
                                        fetchDistricts($event.target.value)
                                        "
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required :disabled="!form.provinsi">
                                        <option value="">Select Regency</option>
                                        <option v-for="regency in regencies" :key="regency.id" :value="regency.id">
                                            {{ regency.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.kabupaten" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="kecamatan" value="Kecamatan" />
                                    <select id="kecamatan" v-model="form.kecamatan" @change="
                                        fetchVillages($event.target.value)
                                        "
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required :disabled="!form.kabupaten">
                                        <option value="">
                                            Select District
                                        </option>
                                        <option v-for="district in districts" :key="district.id" :value="district.id">
                                            {{ district.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.kecamatan" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="kelurahan" value="Kelurahan" />
                                    <select id="kelurahan" v-model="form.kelurahan"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required :disabled="!form.kecamatan">
                                        <option value="">Select Village</option>
                                        <option v-for="village in villages" :key="village.id" :value="village.id">
                                            {{ village.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.kelurahan" class="mt-2" />
                                </div>

                                <!-- Organization Image Input -->
                                <ImageInput v-model="form.organization_image" label="Organization Image"
                                    :error="form.errors.organization_image" :current-image="props.profile.organization_image
                                        " image-class="h-32 w-auto object-cover rounded-lg" />

                                <!-- Hero Images -->
                                <div class="col-span-2 grid grid-cols-3 gap-4">
                                    <ImageInput v-model="form.hero_image_1" label="Hero Image 1"
                                        :error="form.errors.hero_image_1" :current-image="form.currentHeroImages[0]
                                            " image-class="h-32 w-full object-cover rounded-lg" />
                                    <ImageInput v-model="form.hero_image_2" label="Hero Image 2"
                                        :error="form.errors.hero_image_2" :current-image="form.currentHeroImages[1]
                                            " image-class="h-32 w-full object-cover rounded-lg" />
                                    <ImageInput v-model="form.hero_image_3" label="Hero Image 3"
                                        :error="form.errors.hero_image_3" :current-image="form.currentHeroImages[2]
                                            " image-class="h-32 w-full object-cover rounded-lg" />
                                </div>

                                <div class="col-span-2 mb-4">
                                    <JsonArrayInput v-model="form.fasilitas" label="Fasilitas"
                                        placeholder="Masukkan fasilitas desa" :error="form.errors.fasilitas" />
                                </div>

                                <!-- Add new lembaga input -->
                                <div class="col-span-2 mb-4">
                                    <JsonArrayInput v-model="form.lembaga" label="Lembaga"
                                        placeholder="Masukkan nama lembaga desa" :error="form.errors.lembaga" />
                                </div>

                                <!-- Colors -->
                                <div>
                                    <InputLabel for="primary_color" value="Primary Color" />
                                    <select id="primary_color" v-model="form.primary_color"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                        <option value="#000000">Black</option>
                                        <option value="#ffffff">White</option>
                                    </select>
                                    <InputError :message="form.errors.primary_color" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="secondary_color" value="Secondary Color" />
                                    <select id="secondary_color" v-model="form.secondary_color"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                        <option value="#000000">Black</option>
                                        <option value="#ffffff">White</option>
                                    </select>
                                    <InputError :message="form.errors.secondary_color" class="mt-2" />
                                </div>

                                <div class="col-span-2">
                                    <InputLabel for="vision" value="Vision" />
                                    <TextInput id="vision" v-model="form.vision" type="text" class="mt-1 block w-full"
                                        required />
                                    <InputError :message="form.errors.vision" class="mt-2" />
                                </div>

                                <div class="col-span-2">
                                    <InputLabel for="mission" value="Mission" />
                                    <textarea id="mission" v-model="form.mission"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        rows="4" required></textarea>
                                    <InputError :message="form.errors.mission" class="mt-2" />
                                </div>

                                <div class="col-span-2">
                                    <InputLabel for="history" value="Village History" />
                                    <textarea id="history" v-model="form.history"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        rows="6" required></textarea>
                                    <InputError :message="form.errors.history" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Update Village Profile
                                </PrimaryButton>
                                <Link :href="route('dashboard.profiles.index')">
                                <PrimaryButton type="button">Cancel</PrimaryButton>
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
