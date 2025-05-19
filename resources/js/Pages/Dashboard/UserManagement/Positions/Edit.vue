<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ref } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

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
    <Head title="Edit Posisi" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Edit Posisi" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="26" viewBox="0 0 25 26" fill="none">
                        <path
                            d="M12.4998 13.0003C10.804 13.0003 9.35227 12.3965 8.14463 11.1889C6.93699 9.98123 6.33317 8.52949 6.33317 6.83366C6.33317 5.13783 6.93699 3.68609 8.14463 2.47845C9.35227 1.27081 10.804 0.666992 12.4998 0.666992C14.1957 0.666992 15.6474 1.27081 16.855 2.47845C18.0627 3.68609 18.6665 5.13783 18.6665 6.83366C18.6665 8.52949 18.0627 9.98123 16.855 11.1889C15.6474 12.3965 14.1957 13.0003 12.4998 13.0003ZM0.166504 25.3337V21.017C0.166504 20.1434 0.39133 19.3404 0.840983 18.6081C1.29064 17.8758 1.88803 17.317 2.63317 16.9316C4.22623 16.135 5.84498 15.5377 7.48942 15.1394C9.13386 14.7411 10.804 14.542 12.4998 14.542C14.1957 14.542 15.8658 14.7411 17.5103 15.1394C19.1547 15.5377 20.7734 16.135 22.3665 16.9316C23.1116 17.317 23.709 17.8758 24.1587 18.6081C24.6083 19.3404 24.8332 20.1434 24.8332 21.017V25.3337H0.166504Z"
                            fill="#4C7660" />
                    </svg>
                </template>
                <template #right-content>
                </template>
            </HeadingMenu>
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
