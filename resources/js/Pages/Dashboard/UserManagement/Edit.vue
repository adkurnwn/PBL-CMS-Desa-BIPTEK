<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ref, computed } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

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

const handleFileChange = (e) => {
    form.photo_profile = e.target.files[0];
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="26" viewBox="0 0 25 26" fill="none">
                        <path d="M12.4998 13.0003C10.804 13.0003 9.35227 12.3965 8.14463 11.1889C6.93699 9.98123 6.33317 8.52949 6.33317 6.83366C6.33317 5.13783 6.93699 3.68609 8.14463 2.47845C9.35227 1.27081 10.804 0.666992 12.4998 0.666992C14.1957 0.666992 15.6474 1.27081 16.855 2.47845C18.0627 3.68609 18.6665 5.13783 18.6665 6.83366C18.6665 8.52949 18.0627 9.98123 16.855 11.1889C15.6474 12.3965 14.1957 13.0003 12.4998 13.0003ZM0.166504 25.3337V21.017C0.166504 20.1434 0.39133 19.3404 0.840983 18.6081C1.29064 17.8758 1.88803 17.317 2.63317 16.9316C4.22623 16.135 5.84498 15.5377 7.48942 15.1394C9.13386 14.7411 10.804 14.542 12.4998 14.542C14.1957 14.542 15.8658 14.7411 17.5103 15.1394C19.1547 15.5377 20.7734 16.135 22.3665 16.9316C23.1116 17.317 23.709 17.8758 24.1587 18.6081C24.6083 19.3404 24.8332 20.1434 24.8332 21.017V25.3337H0.166504Z" fill="currentColor" />
                    </svg>
                </template>
            </HeadingMenu>
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
                                <!-- Name Field -->
                                <div>
                                    <InputLabel for="name" value="Name" />
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

                                <!-- Email Field -->
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

                                <!-- Password Field -->
                                <div>
                                    <InputLabel
                                        for="password"
                                        value="Password (leave empty to keep current)"
                                    />
                                    <TextInput
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.password"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Phone Field -->
                                <div>
                                    <InputLabel for="phone" value="Phone" />
                                    <TextInput
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.phone"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Photo Profile Field -->
                                <div>
                                    <InputLabel
                                        for="photo_profile"
                                        value="Profile Photo"
                                    />
                                    <input
                                        id="photo_profile"
                                        type="file"
                                        @input="handleFileChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="image/png, image/jpeg, image/jpg"
                                    />
                                    <p
                                        v-if="user.photo_profile"
                                        class="mt-1 text-sm text-gray-500"
                                    >
                                        Current:
                                        {{
                                            user.photo_profile.split("/").pop()
                                        }}
                                    </p>
                                    <InputError
                                        :message="form.errors.photo_profile"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Position Field -->
                                <div>
                                    <InputLabel
                                        for="position"
                                        value="Position"
                                    />
                                    <select
                                        id="position_id"
                                        v-model="form.position_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="" disabled>
                                            Select Position
                                        </option>
                                        <option
                                            v-for="position in positions"
                                            :key="position.id"
                                            :value="position.id"
                                        >
                                            {{ position.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.position_id"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Job Description Display (Read-only for positions) -->
                                <div
                                    v-if="selectedPosition"
                                    class="col-span-2"
                                >
                                    <h3 class="font-semibold mt-2">
                                        Job Description:
                                    </h3>
                                    <ul class="list-disc ml-5 mt-1">
                                        <li
                                            v-for="(
                                                duty, index
                                            ) in selectedPosition.job_description"
                                            :key="index"
                                            class="text-gray-700"
                                        >
                                            {{ duty }}
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-row justify-between">
                                <PrimaryButton :disabled="form.processing">
                                    Update User
                                </PrimaryButton>
                                <Link :href="route('dashboard.users.index')">
                                    <DangerButton>Cancel</DangerButton>
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
