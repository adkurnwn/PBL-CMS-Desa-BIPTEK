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
            <HeadingMenu title="Edit Pengguna" :hasRightContent="false">
                <template #icon>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="25"
                        height="26"
                        viewBox="0 0 25 26"
                        fill="none"
                    >
                        <path
                            d="M12.4998 13.0003C10.804 13.0003 9.35227 12.3965 8.14463 11.1889C6.93699 9.98123 6.33317 8.52949 6.33317 6.83366C6.33317 5.13783 6.93699 3.68609 8.14463 2.47845C9.35227 1.27081 10.804 0.666992 12.4998 0.666992C14.1957 0.666992 15.6474 1.27081 16.855 2.47845C18.0627 3.68609 18.6665 5.13783 18.6665 6.83366C18.6665 8.52949 18.0627 9.98123 16.855 11.1889C15.6474 12.3965 14.1957 13.0003 12.4998 13.0003ZM0.166504 25.3337V21.017C0.166504 20.1434 0.39133 19.3404 0.840983 18.6081C1.29064 17.8758 1.88803 17.317 2.63317 16.9316C4.22623 16.135 5.84498 15.5377 7.48942 15.1394C9.13386 14.7411 10.804 14.542 12.4998 14.542C14.1957 14.542 15.8658 14.7411 17.5103 15.1394C19.1547 15.5377 20.7734 16.135 22.3665 16.9316C23.1116 17.317 23.709 17.8758 24.1587 18.6081C24.6083 19.3404 24.8332 20.1434 24.8332 21.017V25.3337H0.166504Z"
                            fill="currentColor"
                        />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Edit Pengguna
        </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <div class="overflow-hidden bg-white sm:rounded-lg">
                <div class="">
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-[22px] gap-y-[15px]">
                            <div class="flex flex-col gap-y-[15px]">
                                <!-- First column: Name, Email, Position, Job Description -->
                                
                                <!-- Name Field -->
                                <div>
                                    <SingleLineField
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        fieldName="Nama"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.name"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Email Field -->
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

                                <!-- Position Field -->
                                <div>
                                    <DropdownFieldInput
                                        id="position_id"
                                        v-model="form.position_id"
                                        fieldName="Posisi"
                                        placeholder="Select Position"
                                        :options="positionOptions"
                                        required
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.position_id"
                                        class="mt-2"
                                    />
                                </div>
                                
                                <!-- Job Description Display (if position selected) -->
                                <div v-if="selectedPosition" class="px-[24px]">
                                    <h3 class="font-bold text-[15px] text-menuText italic">
                                        Deskripsi Pekerjaan
                                    </h3>
                                    <ul class="list-disc ml-5 mt-[5px]">
                                        <li
                                            v-for="(duty, index) in selectedPosition.job_description"
                                            :key="index"
                                            class="text-menuText italic font-light"
                                        >
                                            {{ duty }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Second column: Phone, Password, Photo Profile -->
                            <div class="flex flex-col gap-y-[15px]">
                                <!-- Phone Field -->
                                <div>
                                    <SingleLineField
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        fieldName="Nomor Telepon"
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.phone"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Password Field -->
                                <div>
                                    <SingleLineField
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        fieldName="Ubah Password (Opsional)"
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.password"
                                        class="mt-2"
                                    />
                                </div>
                                
                                <!-- Photo Profile Field -->
                                <div>
                                    <PhotoProfileUpload
                                        id="photo_profile"
                                        :model-value="form.photo_profile"
                                        @update:model-value="handlePhotoProfileChange"
                                        :preview-image="photoProfileUrl"
                                        custom-width="100%"
                                    />
                                    <InputError
                                        :message="form.errors.photo_profile"
                                        class="mt-2"
                                    />
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
