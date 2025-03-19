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

const logoPreview = ref(null);
const orgImagePreview = ref(null);
const heroImage1Preview = ref(null);
const heroImage2Preview = ref(null);
const heroImage3Preview = ref(null);

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
    hero_image: null,
    organization_image: null,
    hero_image_1: null,
    hero_image_2: null,
    hero_image_3: null,
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
    logoPreview.value = URL.createObjectURL(e.target.files[0]);
};

// Add new file handlers
const handleLogoChange = (e) => {
    form.logo = e.target.files[0];
};

const handleHeroImagesChange = (e) => {
    form.hero_images = Array.from(e.target.files);
};

const handleOrgImageChange = (e) => {
    form.organization_image = e.target.files[0];
    orgImagePreview.value = URL.createObjectURL(e.target.files[0]);
};

const handleHeroImage1Change = (e) => {
    form.hero_image_1 = e.target.files[0];
    heroImage1Preview.value = URL.createObjectURL(e.target.files[0]);
};

const handleHeroImage2Change = (e) => {
    form.hero_image_2 = e.target.files[0];
    heroImage2Preview.value = URL.createObjectURL(e.target.files[0]);
};

const handleHeroImage3Change = (e) => {
    form.hero_image_3 = e.target.files[0];
    heroImage3Preview.value = URL.createObjectURL(e.target.files[0]);
};
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
                                    <InputLabel for="address" value="Address" />
                                    <TextInput
                                        id="address"
                                        v-model="form.address"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.address"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Logo Input -->
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
                                    <div
                                        v-if="logoPreview || props.profile.logo"
                                        class="mt-2"
                                    >
                                        <img
                                            :src="
                                                logoPreview ||
                                                `/storage/${props.profile.logo}`
                                            "
                                            class="h-32 w-32 object-cover rounded-lg"
                                        />
                                    </div>
                                    <InputError
                                        :message="form.errors.logo"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Organization Image Input -->
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
                                    <div
                                        v-if="
                                            orgImagePreview ||
                                            props.profile.organization_image
                                        "
                                        class="mt-2"
                                    >
                                        <img
                                            :src="
                                                orgImagePreview ||
                                                `/storage/${props.profile.organization_image}`
                                            "
                                            class="h-32 w-auto object-cover rounded-lg"
                                        />
                                    </div>
                                    <InputError
                                        :message="
                                            form.errors.organization_image
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Hero Images -->
                                <div class="col-span-2 grid grid-cols-3 gap-4">
                                    <div>
                                        <InputLabel
                                            for="hero_image_1"
                                            value="Hero Image 1"
                                        />
                                        <input
                                            id="hero_image_1"
                                            type="file"
                                            @input="handleHeroImage1Change"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            accept="image/png, image/jpeg, image/jpg"
                                        />
                                        <div
                                            v-if="
                                                heroImage1Preview ||
                                                props.profile.hero_images?.[0]
                                            "
                                            class="mt-2"
                                        >
                                            <img
                                                :src="
                                                    heroImage1Preview ||
                                                    `/storage/${
                                                        JSON.parse(
                                                            props.profile
                                                                .hero_images
                                                        )[0]
                                                    }`
                                                "
                                                class="h-32 w-full object-cover rounded-lg"
                                            />
                                        </div>
                                        <InputError
                                            :message="form.errors.hero_image_1"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="hero_image_2"
                                            value="Hero Image 2"
                                        />
                                        <input
                                            id="hero_image_2"
                                            type="file"
                                            @input="handleHeroImage2Change"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            accept="image/png, image/jpeg, image/jpg"
                                        />
                                        <div
                                            v-if="
                                                heroImage2Preview ||
                                                props.profile.hero_images?.[1]
                                            "
                                            class="mt-2"
                                        >
                                            <img
                                                :src="
                                                    heroImage2Preview ||
                                                    `/storage/${
                                                        JSON.parse(
                                                            props.profile
                                                                .hero_images
                                                        )[1]
                                                    }`
                                                "
                                                class="h-32 w-full object-cover rounded-lg"
                                            />
                                        </div>
                                        <InputError
                                            :message="form.errors.hero_image_2"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="hero_image_3"
                                            value="Hero Image 3"
                                        />
                                        <input
                                            id="hero_image_3"
                                            type="file"
                                            @input="handleHeroImage3Change"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            accept="image/png, image/jpeg, image/jpg"
                                        />
                                        <div
                                            v-if="
                                                heroImage3Preview ||
                                                props.profile.hero_images?.[2]
                                            "
                                            class="mt-2"
                                        >
                                            <img
                                                :src="
                                                    heroImage3Preview ||
                                                    `/storage/${
                                                        JSON.parse(
                                                            props.profile
                                                                .hero_images
                                                        )[2]
                                                    }`
                                                "
                                                class="h-32 w-full object-cover rounded-lg"
                                            />
                                        </div>
                                        <InputError
                                            :message="form.errors.hero_image_3"
                                            class="mt-2"
                                        />
                                    </div>
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
