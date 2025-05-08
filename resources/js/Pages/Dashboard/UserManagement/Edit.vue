<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ref, computed } from "vue";

const props = defineProps({
    user: Object,
    positions: Array,
});

// Add new position toggle
const isNewPosition = ref(false);
const newJobDescription = ref("");
const jobDescriptions = ref([]);

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "",
    phone: props.user.phone,
    photo_profile: "",
    position_id: props.user.position_id,
    new_position: "",
    job_descriptions: [],
    _method: "PUT",
});

// Add toggle function to properly clear fields when switching modes
const togglePositionMode = () => {
    isNewPosition.value = !isNewPosition.value;

    // Clear the appropriate field based on the new mode
    if (isNewPosition.value) {
        form.position_id = ""; // Clear position_id when switching to new position mode
    } else {
        form.new_position = ""; // Clear new_position when switching to existing position mode
        form.job_descriptions = []; // Also clear job descriptions
    }
};

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
    if (isNewPosition.value) return null;

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

// Add job description to the list
const addJobDescription = () => {
    if (newJobDescription.value.trim()) {
        form.job_descriptions.push(newJobDescription.value);
        newJobDescription.value = "";
    }
};

// Remove job description from the list
const removeJobDescription = (index) => {
    form.job_descriptions.splice(index, 1);
};

const submit = () => {
    form.post(route("dashboard.users.update", props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset("password");
            isNewPosition.value = false;
        },
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit User" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit User
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
                                    <div class="flex gap-4">
                                        <select
                                            v-if="!isNewPosition"
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

                                        <TextInput
                                            v-else
                                            id="new_position"
                                            v-model="form.new_position"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="Enter new position name"
                                            required
                                        />

                                        <button
                                            type="button"
                                            class="mt-1 px-4 py-2 text-sm text-blue-600 hover:text-blue-800"
                                            @click="togglePositionMode"
                                        >
                                            {{
                                                isNewPosition
                                                    ? "Select Existing"
                                                    : "Add New"
                                            }}
                                        </button>
                                    </div>
                                    <InputError
                                        :message="
                                            form.errors.position_id ||
                                            form.errors.new_position
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Job Description Field (only shown when creating a new position) -->
                                <div
                                    v-if="isNewPosition"
                                    class="col-span-2 mt-4"
                                >
                                    <InputLabel
                                        for="job_description"
                                        value="Job Description"
                                    />
                                    <div class="flex mt-1">
                                        <TextInput
                                            id="job_description"
                                            v-model="newJobDescription"
                                            type="text"
                                            class="block w-full"
                                            placeholder="Enter job description item"
                                        />
                                        <PrimaryButton
                                            type="button"
                                            class="ml-2"
                                            @click="addJobDescription"
                                        >
                                            Add
                                        </PrimaryButton>
                                    </div>

                                    <!-- Display job descriptions as tags -->
                                    <div
                                        class="mt-2 flex flex-wrap gap-2"
                                        v-if="form.job_descriptions.length > 0"
                                    >
                                        <div
                                            v-for="(
                                                description, index
                                            ) in form.job_descriptions"
                                            :key="index"
                                            class="bg-gray-100 px-2 py-1 rounded-md flex items-center gap-1"
                                        >
                                            <span>{{ description }}</span>
                                            <button
                                                type="button"
                                                class="text-red-500 hover:text-red-700 focus:outline-none"
                                                @click="
                                                    removeJobDescription(index)
                                                "
                                            >
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                    <p
                                        class="text-sm text-gray-500 mt-1"
                                        v-else
                                    >
                                        Add at least one job description for the
                                        new position
                                    </p>
                                    <InputError
                                        :message="form.errors.job_descriptions"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Job Description Display (Read-only for existing positions) -->
                                <div
                                    v-else-if="selectedPosition"
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
