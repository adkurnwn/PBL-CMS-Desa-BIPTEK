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

const newJobDescription = ref("");

const form = useForm({
    name: "",
    job_descriptions: [],
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
    form.post(route("dashboard.positions.store"), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Buat Posisi Baru" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Pengguna" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#40483D"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h168q14-36 44-58t68-22q38 0 68 22t44 58h168q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm280-670q13 0 21.5-8.5T510-820q0-13-8.5-21.5T480-850q-13 0-21.5 8.5T450-820q0 13 8.5 21.5T480-790ZM200-246q54-53 125.5-83.5T480-360q83 0 154.5 30.5T760-246v-514H200v514Zm280-194q58 0 99-41t41-99q0-58-41-99t-99-41q-58 0-99 41t-41 99q0 58 41 99t99 41ZM280-200h400v-10q-42-35-93-52.5T480-280q-56 0-107 17.5T280-210v10Zm200-320q-25 0-42.5-17.5T420-580q0-25 17.5-42.5T480-640q25 0 42.5 17.5T540-580q0 25-17.5 42.5T480-520Zm0 17Z"/></svg>
                </template>
                <template #right-content>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Buat Posisi Baru
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
                                SIMPAN
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
