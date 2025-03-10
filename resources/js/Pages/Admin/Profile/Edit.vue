<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref, onMounted } from "vue";
import axios from "axios";

// Add reactive data for regions
const provinces = ref([]);
const regencies = ref([]);
const districts = ref([]);
const villages = ref([]);

const newFasilitas = ref("");
const newLembaga = ref("");

const props = defineProps({
    profile: Object,
});

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

const form = useForm({
    name: props.profile.name,
    email: props.profile.email,
    phone: props.profile.phone,
    address: props.profile.address,
    logo: null,
    provinsi: props.profile.provinsi,
    kabupaten: props.profile.kabupaten,
    kecamatan: props.profile.kecamatan,
    kelurahan: props.profile.kelurahan,
    hero_image: null,
    organization_image: null,
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
    form.post(route("admin.profiles.update", props.profile.id), {
        preserveScroll: true,
        forceFormData: true,
    });
};

const addFasilitas = (e) => {
    e.preventDefault();
    if (newFasilitas.value.trim()) {
        form.fasilitas.push(newFasilitas.value.trim());
        newFasilitas.value = "";
    }
};

// Remove job description at specific index
const removeFasilitas = (index) => {
    form.fasilitas.splice(index, 1);
};

const addLembaga = (e) => {
    e.preventDefault();
    if (newLembaga.value.trim()) {
        form.lembaga.push(newLembaga.value.trim());
        newLembaga.value = "";
    }
};

const removeLembaga = (index) => {
    form.lembaga.splice(index, 1);
};

const handleFileChange = (e) => {
    form.logo = e.target.files[0];
};

// Add new file handlers
const handleLogoChange = (e) => {
    form.logo = e.target.files[0];
};

const handleHeroImageChange = (e) => {
    form.hero_image = e.target.files[0];
};

const handleOrgImageChange = (e) => {
    form.organization_image = e.target.files[0];
};

// Add methods to fetch region data
const fetchProvinces = async () => {
    const response = await axios.get("/api/provinces");
    provinces.value = response.data;
};

const fetchRegencies = async (provinceId) => {
    const response = await axios.get(`/api/regencies/${provinceId}`);
    regencies.value = response.data;
    form.kabupaten = ""; // Reset dependent fields
    form.kecamatan = "";
    form.kelurahan = "";
};

const fetchDistricts = async (regencyId) => {
    const response = await axios.get(`/api/districts/${regencyId}`);
    districts.value = response.data;
    form.kecamatan = "";
    form.kelurahan = "";
};

const fetchVillages = async (districtId) => {
    const response = await axios.get(`/api/villages/${districtId}`);
    villages.value = response.data;
    form.kelurahan = "";
};

// Load provinces when component mounts
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
                                    <InputLabel
                                        for="name"
                                        value="Village Name"
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

                                <div>
                                    <InputLabel
                                        for="logo"
                                        value="Village Logo"
                                    />
                                    <input
                                        id="logo"
                                        type="file"
                                        @input="handleFileChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="image/png, image/jpeg, image/jpg"
                                    />
                                    <InputError
                                        :message="form.errors.logo"
                                        class="mt-2"
                                    />
                                </div>

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

                                <!-- Images -->
                                <div>
                                    <InputLabel
                                        for="hero_image"
                                        value="Hero Image"
                                    />
                                    <input
                                        id="hero_image"
                                        type="file"
                                        @input="handleHeroImageChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="image/png, image/jpeg, image/jpg"
                                    />
                                    <InputError
                                        :message="form.errors.hero_image"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="organization_image"
                                        value="Organization Image"
                                    />
                                    <input
                                        id="organization_image"
                                        type="file"
                                        @input="handleOrgImageChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="image/png, image/jpeg, image/jpg"
                                    />
                                    <InputError
                                        :message="
                                            form.errors.organization_image
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div class="col-span-2 mb-4">
                                    <InputLabel
                                        for="fasilitas"
                                        value="Fasilitas"
                                    />
                                    <div class="flex mt-1">
                                        <TextInput
                                            id="fasilitas"
                                            v-model="newFasilitas"
                                            type="text"
                                            class="block w-full"
                                            placeholder="Masukkan fasilitas desa"
                                            @keydown.enter.prevent="
                                                addFasilitas
                                            "
                                        />
                                        <PrimaryButton
                                            type="button"
                                            class="ml-2"
                                            @click="addFasilitas"
                                        >
                                            Tambah
                                        </PrimaryButton>
                                    </div>

                                    <!-- Display fasilitas as tags -->
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <div
                                            v-for="(
                                                fas, index
                                            ) in form.fasilitas"
                                            :key="index"
                                            class="bg-gray-100 px-2 py-1 rounded-md flex items-center gap-1"
                                        >
                                            <span>{{ fas }}</span>
                                            <button
                                                type="button"
                                                class="text-red-500 hover:text-red-700 focus:outline-none"
                                                @click="removeFasilitas(index)"
                                            >
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                    <InputError
                                        :message="form.errors.fasilitas"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Add new lembaga input -->
                                <div class="col-span-2 mb-4">
                                    <InputLabel for="lembaga" value="Lembaga" />
                                    <div class="flex mt-1">
                                        <TextInput
                                            id="lembaga"
                                            v-model="newLembaga"
                                            type="text"
                                            class="block w-full"
                                            placeholder="Masukkan nama lembaga desa"
                                            @keydown.enter.prevent="addLembaga"
                                        />
                                        <PrimaryButton
                                            type="button"
                                            class="ml-2"
                                            @click="addLembaga"
                                        >
                                            Tambah
                                        </PrimaryButton>
                                    </div>

                                    <!-- Display lembaga as tags -->
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <div
                                            v-for="(lem, index) in form.lembaga"
                                            :key="index"
                                            class="bg-gray-100 px-2 py-1 rounded-md flex items-center gap-1"
                                        >
                                            <span>{{ lem }}</span>
                                            <button
                                                type="button"
                                                class="text-red-500 hover:text-red-700 focus:outline-none"
                                                @click="removeLembaga(index)"
                                            >
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                    <InputError
                                        :message="form.errors.lembaga"
                                        class="mt-2"
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
                                <Link :href="route('admin.profiles')">
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
