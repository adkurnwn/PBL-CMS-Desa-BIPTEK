<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import { ref, computed } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import DropdownFieldInput from "@/Components/DashboardComponent/DropdownFieldInput.vue";
import PhotoProfileUpload from "@/Components/DashboardComponent/PhotoProfileUpload.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";

const props = defineProps({
    positions: Array,
});

const form = useForm({
    name: "",
    email: "",
    password: "",
    phone: "",
    photo_profile: "",
    position_id: "",
});

// Format positions for dropdown
const positionOptions = computed(() => {
    return props.positions.map((position) => ({
        value: position.id,
        label: position.name,
    }));
});

// Parse the job description JSON if it's a string
const parseJobDescription = (description) => {
    if (!description) return [];
    if (Array.isArray(description)) return description;

    try {
        return JSON.parse(description);
    } catch (e) {
        console.error("Error parsing job description:", e);
        return [];
    }
};

// Get selected position's job description
const selectedPosition = computed(() => {
    const position = props.positions?.find(
        (position) => position.id === form.position_id
    );

    if (position) {
        return {
            ...position,
            job_description: parseJobDescription(position.job_description),
        };
    }

    return null;
});

const handlePhotoProfileChange = (file) => {
    form.photo_profile = file;
};

const submit = () => {
    form.post(route("dashboard.users.store"), {
        onSuccess: () => {
            form.reset();
        },
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>

    <Head title="Tambah Pengguna" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Pengguna" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M400-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18h14q6 0 12 2-8 18-13.5 37.5T404-360h-4q-71 0-127.5 18T180-306q-9 5-14.5 14t-5.5 20v32h252q6 21 16 41.5t22 38.5H80Zm560 40-12-60q-12-5-22.5-10.5T584-204l-58 18-40-68 46-40q-2-14-2-26t2-26l-46-40 40-68 58 18q11-8 21.5-13.5T628-460l12-60h80l12 60q12 5 22.5 11t21.5 15l58-20 40 70-46 40q2 12 2 25t-2 25l46 40-40 68-58-18q-11 8-21.5 13.5T732-180l-12 60h-80Zm40-120q33 0 56.5-23.5T760-320q0-33-23.5-56.5T680-400q-33 0-56.5 23.5T600-320q0 33 23.5 56.5T680-240ZM400-560q33 0 56.5-23.5T480-640q0-33-23.5-56.5T400-720q-33 0-56.5 23.5T320-640q0 33 23.5 56.5T400-560Zm0-80Zm12 400Z" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Tambah Pengguna
        </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <div class="overflow-hidden bg-white  ">
                <div class="">
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-[22px] gap-y-[15px]">
                            <div class="flex flex-col gap-y-[15px]">
                                <!-- First column: Name, Email, Position, Job Description -->

                                <!-- Name Field -->
                                <div>
                                    <SingleLineField id="name" v-model="form.name" type="text" fieldName="Nama" required
                                        custom-width="100%" />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>

                                <!-- Email Field -->
                                <div>
                                    <SingleLineField id="email" v-model="form.email" type="email" fieldName="Email"
                                        required custom-width="100%" />
                                    <InputError :message="form.errors.email" class="mt-2" />
                                </div>

                                <!-- Position Field -->
                                <div>
                                    <DropdownFieldInput id="position_id" v-model="form.position_id" fieldName="Posisi"
                                        placeholder="Select Position" :options="positionOptions" required
                                        custom-width="100%" />
                                    <InputError :message="form.errors.position_id" class="mt-2" />
                                </div>

                                <!-- Job Description Display (if position selected) -->
                                <!-- Job Description Display (if position selected) -->
                                <div class="px-[24px]" :style="{ minHeight: '150px' }">
                                    <template v-if="selectedPosition">
                                        <h3 class="font-bold text-[15px] text-menuText italic">
                                            Deskripsi Pekerjaan
                                        </h3>
                                        <ul class="list-disc ml-5 mt-[5px]">
                                            <li v-for="(duty, index) in selectedPosition.job_description" :key="index"
                                                class="text-menuText italic font-light">
                                                {{ duty }}
                                            </li>
                                        </ul>
                                    </template>
                                </div>
                            </div>

                            <!-- Second column: Phone, Password, Photo Profile -->
                            <div class="flex flex-col gap-y-[15px]">
                                <!-- Phone Field -->
                                <div>
                                    <SingleLineField id="phone" v-model="form.phone" type="text"
                                        fieldName="Nomor Telepon" custom-width="100%" />
                                    <InputError :message="form.errors.phone" class="mt-2" />
                                </div>

                                <!-- Password Field -->
                                <div>
                                    <SingleLineField id="password" v-model="form.password" type="password"
                                        fieldName="Password" required custom-width="100%" />
                                    <InputError :message="form.errors.password" class="mt-2" />
                                </div>

                                <!-- Photo Profile Field -->
                                <div>
                                    <PhotoProfileUpload id="photo_profile" :model-value="form.photo_profile"
                                        @update:model-value="handlePhotoProfileChange" custom-width="100%" required />
                                    <InputError :message="form.errors.photo_profile" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-[15px] flex flex-row justify-end">
                            <Link :href="route('dashboard.users.index')" class="mr-[15px]">
                            <SecondaryButton>BATAL</SecondaryButton>
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
