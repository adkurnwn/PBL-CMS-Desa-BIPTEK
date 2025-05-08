<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ref } from "vue";

const props = defineProps({
    position: Object,
});

const newJobDescription = ref("");

// Parse job descriptions from JSON if they're in string format
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

const form = useForm({
    name: props.position.name,
    job_descriptions: parseJobDescription(props.position.job_description),
    _method: "PUT",
});

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
    form.post(route("dashboard.positions.update", props.position.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit Position" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Position
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Position Name Field -->
                                <div>
                                    <InputLabel
                                        for="name"
                                        value="Position Name"
                                    />
                                    <TextInput
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <p class="text-xs text-gray-500 mt-1">
                                        Position name must be unique
                                    </p>
                                    <InputError
                                        :message="form.errors.name"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Job Description Field -->
                                <div>
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
                                        Add at least one job description for
                                        this position
                                    </p>
                                    <InputError
                                        :message="form.errors.job_descriptions"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-6 flex justify-between">
                                <PrimaryButton :disabled="form.processing">
                                    Update Position
                                </PrimaryButton>
                                <Link :href="route('dashboard.users.index')">
                                    <DangerButton type="button">
                                        Cancel
                                    </DangerButton>
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
