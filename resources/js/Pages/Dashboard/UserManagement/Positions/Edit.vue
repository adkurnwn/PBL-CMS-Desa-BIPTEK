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
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                        fill="#40483D">
                        <path
                            d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h168q14-36 44-58t68-22q38 0 68 22t44 58h168q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm280-670q13 0 21.5-8.5T510-820q0-13-8.5-21.5T480-850q-13 0-21.5 8.5T450-820q0 13 8.5 21.5T480-790ZM200-246q54-53 125.5-83.5T480-360q83 0 154.5 30.5T760-246v-514H200v514Zm280-194q58 0 99-41t41-99q0-58-41-99t-99-41q-58 0-99 41t-41 99q0 58 41 99t99 41ZM280-200h400v-10q-42-35-93-52.5T480-280q-56 0-107 17.5T280-210v10Zm200-320q-25 0-42.5-17.5T420-580q0-25 17.5-42.5T480-640q25 0 42.5 17.5T540-580q0 25-17.5 42.5T480-520Zm0 17Z" />
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
            <div class="overflow-hidden bg-white  ">
                <div class="">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-y-[15px]">
                            <!-- Position Name Field -->
                            <div>
                                <SingleLineField id="name" v-model="form.name" type="text" fieldName="Nama Posisi"
                                    placeholder="Masukkan nama posisi" required custom-width="100%" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Job Description Field -->
                            <div>
                                <div class="flex items-center pr-[12px]">
                                    <div class="flex-grow">
                                        <SingleLineField id="job_description" v-model="newJobDescription" type="text"
                                            fieldName="Deskripsi Pekerjaan"
                                            placeholder="Masukkan item deskripsi pekerjaan" custom-width="100%" />
                                    </div>
                                    <div class="py-[12px] mt-[37px]">
                                        <PrimaryButton type="button" @click="addJobDescription">
                                            ADD
                                        </PrimaryButton>
                                    </div>
                                </div>

                                <!-- Display job descriptions as tags -->
                                <div class="mt-[0.9px] sm:mt-[1px] md:mt-[1.1px] lg:mt-[1.2px] xl:mt-[1.33px] 2xl:mt-[1.6px] 3xl:mt-[1.8px] 4xl:mt-[2px] flex flex-wrap gap-[6.7px] sm:gap-[7.5px] md:gap-[8.5px] lg:gap-[9.2px] xl:gap-[10px] 2xl:gap-[11.5px] 3xl:gap-[13.3px] 4xl:gap-[15px]" v-if="form.job_descriptions.length > 0">
                                    <div v-for="(description, index) in form.job_descriptions" :key="index"
                                        class="bg-[#f3f4f6] h-[18.7px] sm:h-[21.1px] md:h-[23.7px] lg:h-[25.7px] xl:h-[28px] 2xl:h-[32px] 3xl:h-[37px] 4xl:h-[42px] px-[1.3px] sm:px-[1.5px] md:px-[1.7px] lg:px-[1.8px] xl:px-[2px] 2xl:px-[2.3px] 3xl:px-[2.6px] 4xl:px-[3px] rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] flex items-center gap-[0.9px] sm:gap-[1px] md:gap-[1.1px] lg:gap-[1.2px] xl:gap-[1.33px] 2xl:gap-[1.6px] 3xl:gap-[1.8px] 4xl:gap-[2px] shadow-[0_0.9px_0.9px_0px_rgba(0,0,0,0.25)] sm:shadow-[0_1px_1px_0px_rgba(0,0,0,0.25)] md:shadow-[0_1.1px_1.1px_0px_rgba(0,0,0,0.25)] lg:shadow-[0_1.2px_1.2px_0px_rgba(0,0,0,0.25)] xl:shadow-[0_1.33px_1.33px_0px_rgba(0,0,0,0.25)] 2xl:shadow-[0_1.6px_1.6px_0px_rgba(0,0,0,0.25)] 3xl:shadow-[0_1.8px_1.8px_0px_rgba(0,0,0,0.25)] 4xl:shadow-[0_2px_2px_0px_rgba(0,0,0,0.25)]">
                                        <span class="text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">{{ description }}</span>
                                        <button type="button"
                                            class="w-[8.9px] h-[8.9px] sm:w-[10.1px] sm:h-[10.1px] md:w-[11.5px] md:h-[11.5px] lg:w-[12.5px] lg:h-[12.5px] xl:w-[13.3px] xl:h-[13.3px] 2xl:w-[15.4px] 2xl:h-[15.4px] 3xl:w-[17.5px] 3xl:h-[17.5px] 4xl:w-[20px] 4xl:h-[20px] flex items-center justify-center focus:outline-none"
                                            @click="removeJobDescription(index)">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none"
                                                class="w-[8.9px] h-[8.9px] sm:w-[10.1px] sm:h-[10.1px] md:w-[11.5px] md:h-[11.5px] lg:w-[12.5px] lg:h-[12.5px] xl:w-[13.3px] xl:h-[13.3px] 2xl:w-[15.4px] 2xl:h-[15.4px] 3xl:w-[17.5px] 3xl:h-[17.5px] 4xl:w-[20px] 4xl:h-[20px]">
                                                <mask id="mask0_1715_7284" style="mask-type:alpha"
                                                    maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="20">
                                                    <rect width="20" height="20" fill="#D9D9D9" />
                                                </mask>
                                                <g mask="url(#mask0_1715_7284)">
                                                    <path
                                                        d="M6.99967 14.166L5.83301 12.9993L8.83301 9.99935L5.83301 7.02018L6.99967 5.85352L9.99967 8.85352L12.9788 5.85352L14.1455 7.02018L11.1455 9.99935L14.1455 12.9993L12.9788 14.166L9.99967 11.166L6.99967 14.166Z"
                                                        fill="#DC3545" />
                                                </g>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-menuText text-[6.2px] sm:text-[7px] md:text-[8px] lg:text-[8.7px] xl:text-[9.33px] 2xl:text-[10.8px] 3xl:text-[12.4px] 4xl:text-[14px] ml-[5.3px] sm:ml-[6px] md:ml-[6.7px] lg:ml-[7.3px] xl:ml-[8px] 2xl:ml-[9.3px] 3xl:ml-[10.7px] 4xl:ml-[12px]" v-else>
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
