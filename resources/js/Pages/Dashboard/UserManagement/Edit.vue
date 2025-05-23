<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ref, computed } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import DropdownFieldInput from "@/Components/DashboardComponent/DropdownFieldInput.vue";
import PhotoProfileUpload from "@/Components/DashboardComponent/PhotoProfileUpload.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";

const props = defineProps({
    user: Object,
    positions: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "",
    phone: props.user.phone,
    photo_profile: "",
    position_id: props.user.position_id,
    _method: "PUT",
});

// Get the full path for photo profile preview
const photoProfileUrl = computed(() => {
    if (props.user.photo_profile) {
        // Handle both relative and absolute URLs
        if (props.user.photo_profile.startsWith('http')) {
            return props.user.photo_profile;
        } else {
            // For relative paths, prepend /storage/ since the photos are stored in public storage
            // This is because the UserManagementController stores files in 'profile-photos' under 'public' disk
            return `/storage/${props.user.photo_profile}`;
        }
    }
    return '';
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

// Format positions for dropdown
const positionOptions = computed(() => {
    return props.positions.map((position) => ({
        value: position.id,
        label: position.name,
    }));
});

// Replace the handleFileChange function
const handlePhotoProfileChange = (file) => {
    form.photo_profile = file;
};

const submit = () => {
    form.post(route("dashboard.users.update", props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset("password");
        },
        forceFormData: true,
    });
};
</script>

<template>

    <Head title="Edit Pengguna" />
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
            Edit Pengguna
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
                                <div v-if="selectedPosition" class="px-[10.7px] sm:px-[12.1px] md:px-[13.6px] lg:px-[14.8px] xl:px-[16px] 2xl:px-[18.5px] 3xl:px-[21.3px] 4xl:px-[24px]">
                                    <h3 class="font-bold text-[6.7px] sm:text-[7.5px] md:text-[8.5px] lg:text-[9.2px] xl:text-[10px] 2xl:text-[11.5px] 3xl:text-[13.3px] 4xl:text-[15px] text-menuText italic">
                                        Deskripsi Pekerjaan
                                    </h3>
                                    <ul class="list-disc ml-[2.2px] sm:ml-[2.5px] md:ml-[2.9px] lg:ml-[3.1px] xl:ml-[3.33px] 2xl:ml-[3.8px] 3xl:ml-[4.4px] 4xl:ml-[5px] mt-[2.2px] sm:mt-[2.5px] md:mt-[2.9px] lg:mt-[3.1px] xl:mt-[3.33px] 2xl:mt-[3.8px] 3xl:mt-[4.4px] 4xl:mt-[5px]">
                                        <li v-for="(duty, index) in selectedPosition.job_description" :key="index"
                                            class="text-menuText italic font-light text-[5.3px] sm:text-[6px] md:text-[6.7px] lg:text-[7.3px] xl:text-[8px] 2xl:text-[9.3px] 3xl:text-[10.7px] 4xl:text-[12px] mb-[2.2px] sm:mb-[2.5px] md:mb-[2.9px] lg:mb-[3.1px] xl:mb-[3.33px] 2xl:mb-[3.8px] 3xl:mb-[4.4px] 4xl:mb-[5px]">
                                        {{ duty }}
                                        </li>
                                    </ul>
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
                                        fieldName="Ubah Password (Opsional)" custom-width="100%" />
                                    <InputError :message="form.errors.password" class="mt-2" />
                                </div>

                                <!-- Photo Profile Field -->
                                <div>
                                    <PhotoProfileUpload id="photo_profile" :model-value="form.photo_profile"
                                        @update:model-value="handlePhotoProfileChange" :preview-image="photoProfileUrl"
                                        custom-width="100%" />
                                    <InputError :message="form.errors.photo_profile" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-[15px] flex flex-row justify-end">
                            <Link :href="route('dashboard.users.index')" class="mr-[15px]">
                            <SecondaryButton>BATAL</SecondaryButton>
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
