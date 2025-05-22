<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const form = useForm({
    title: "",
    description: "",
    date: "",
    time: "",
    location: "",
});

const submit = () => {
    form.post(route("dashboard.events.store"), {
        // Changed from news.store to events.store
        onSuccess: () => form.reset(),
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Buat Agenda" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Buat Agenda" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="33" viewBox="0 0 32 33" fill="none">
                        <path
                            d="M3.70833 31.4163C2.86042 31.4163 2.13455 31.1144 1.53073 30.5106C0.92691 29.9068 0.625 29.1809 0.625 28.333V6.74967C0.625 5.90176 0.92691 5.17589 1.53073 4.57207C2.13455 3.96825 2.86042 3.66634 3.70833 3.66634H5.25V0.583008H8.33333V3.66634H20.6667V0.583008H23.75V3.66634H25.2917C26.1396 3.66634 26.8655 3.96825 27.4693 4.57207C28.0731 5.17589 28.375 5.90176 28.375 6.74967V13.957C28.375 14.3938 28.2273 14.7599 27.9318 15.0554C27.6363 15.3509 27.2701 15.4986 26.8333 15.4986C26.3965 15.4986 26.0304 15.3509 25.7349 15.0554C25.4394 14.7599 25.2917 14.3938 25.2917 13.957V12.9163H3.70833V28.333H12.65C13.0868 28.333 13.453 28.4808 13.7484 28.7762C14.0439 29.0717 14.1917 29.4379 14.1917 29.8747C14.1917 30.3115 14.0439 30.6776 13.7484 30.9731C13.453 31.2686 13.0868 31.4163 12.65 31.4163H3.70833ZM23.75 32.958C21.6174 32.958 19.7995 32.2064 18.2964 30.7033C16.7932 29.2002 16.0417 27.3823 16.0417 25.2497C16.0417 23.117 16.7932 21.2992 18.2964 19.796C19.7995 18.2929 21.6174 17.5413 23.75 17.5413C25.8826 17.5413 27.7005 18.2929 29.2036 19.796C30.7068 21.2992 31.4583 23.117 31.4583 25.2497C31.4583 27.3823 30.7068 29.2002 29.2036 30.7033C27.7005 32.2064 25.8826 32.958 23.75 32.958ZM26.3323 28.9111L27.4115 27.832L24.5208 24.9413V20.6247H22.9792V25.558L26.3323 28.9111Z"
                            fill="#4C7660" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Title Field -->
                                <div>
                                    <InputLabel for="title" value="Title" />
                                    <TextInput
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.title"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Location Field -->
                                <div>
                                    <InputLabel
                                        for="location"
                                        value="Location"
                                    />
                                    <TextInput
                                        id="location"
                                        v-model="form.location"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.location"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Date Field -->
                                <div>
                                    <InputLabel for="date" value="Date" />
                                    <TextInput
                                        id="date"
                                        v-model="form.date"
                                        type="date"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.date"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Time Field -->
                                <div>
                                    <InputLabel for="time" value="Time" />
                                    <TextInput
                                        id="time"
                                        v-model="form.time"
                                        type="time"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.time"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Description Field -->
                                <div class="col-span-2">
                                    <InputLabel
                                        for="description"
                                        value="Description"
                                    />
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.description"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Create Event
                                </PrimaryButton>
                                <Link :href="route('dashboard.events.index')">
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
