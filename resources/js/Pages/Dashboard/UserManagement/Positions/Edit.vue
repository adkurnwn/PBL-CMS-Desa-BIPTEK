<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ref } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";

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
            <HeadingMenu title="Pengguna" :hasRightContent="false">
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

        <template #subtitle-area>
            Edit Posisi
        </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <div class="overflow-hidden bg-white sm:rounded-lg">
                <div class="">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-y-[15px]">
                            <!-- Position Name Field -->
                            <div>
                                <SingleLineField id="name" v-model="form.name" type="text" fieldName="Nama Posisi" placeholder="Masukkan nama posisi"
                                    required custom-width="100%" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Job Description Field -->
                            <div>
                                <div class="flex items-center pr-[12px]">
                                    <div class="flex-grow">
                                        <SingleLineField id="job_description" v-model="newJobDescription" type="text"
                                            fieldName="Deskripsi Pekerjaan" placeholder="Masukkan item deskripsi pekerjaan"
                                            custom-width="100%" />
                                    </div>
                                    <div class="py-[12px] mt-[37px]">
                                        <PrimaryButton type="button" @click="addJobDescription">
                                            ADD
                                        </PrimaryButton>
                                    </div>
                                </div>

                                <!-- Display job descriptions as tags -->
                                <div class="mt-2 flex flex-wrap gap-[15px]" v-if="form.job_descriptions.length > 0">
                                    <div v-for="(description, index) in form.job_descriptions" 
                                        :key="index"
                                        class="bg-[#f3f4f6] h-[42px] px-3 rounded-[5px] flex items-center gap-2 shadow-[0_2px_2px_0px_rgba(0,0,0,0.25)]">
                                        <span class="text-[18px]">{{ description }}</span>
                                        <button type="button" 
                                            class="w-[20px] h-[20px] flex items-center justify-center focus:outline-none"
                                            @click="removeJobDescription(index)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <mask id="mask0_1715_7284" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="20">
                                                    <rect width="20" height="20" fill="#D9D9D9"/>
                                                </mask>
                                                <g mask="url(#mask0_1715_7284)">
                                                    <path d="M6.99967 14.166L5.83301 12.9993L8.83301 9.99935L5.83301 7.02018L6.99967 5.85352L9.99967 8.85352L12.9788 5.85352L14.1455 7.02018L11.1455 9.99935L14.1455 12.9993L12.9788 14.166L9.99967 11.166L6.99967 14.166Z" fill="#DC3545"/>
                                                </g>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-menuText text-[14px] ml-[12px]" v-else>
                                    Tambahkan minimal satu deskripsi pekerjaan untuk posisi ini
                                </p>
                                <InputError :message="form.errors.job_descriptions" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-[15px] flex justify-end">
                            <Link :href="route('dashboard.positions.index')" class="mr-[15px]">
                            <SecondaryButton type="button">
                                BATAL
                            </SecondaryButton>
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
