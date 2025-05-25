<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";
import { Head, Link } from "@inertiajs/vue3";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import TextAreaInput from "@/Components/DashboardComponent/TextAreaInput.vue";
import Labels from "@/Components/DashboardComponent/Labels.vue";
import { computed } from 'vue';

const props = defineProps({
    event: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toISOString().split("T")[0];
};

// Compute event status based on date
const eventStatus = computed(() => {
    if (!props.event.date) return { type: 'other', text: 'Tidak diketahui' };

    const eventDate = new Date(props.event.date);
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Reset time to compare only dates
    eventDate.setHours(0, 0, 0, 0);

    if (eventDate > today) {
        return { type: 'mendatang', text: 'Event akan datang' };
    } else if (eventDate.getTime() === today.getTime()) {
        return { type: 'berlangsung', text: 'Event berlangsung' };
    } else {
        return { type: 'berakhir', text: 'Event telah berakhir' };
    }
});
</script>

<template>

    <Head title="Lihat Agenda" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Lihat Agenda" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="33" viewBox="0 0 32 33" fill="none">
                        <path
                            d="M3.70833 31.4163C2.86042 31.4163 2.13455 31.1144 1.53073 30.5106C0.92691 29.9068 0.625 29.1809 0.625 28.333V6.74967C0.625 5.90176 0.92691 5.17589 1.53073 4.57207C2.13455 3.96825 2.86042 3.66634 3.70833 3.66634H5.25V0.583008H8.33333V3.66634H20.6667V0.583008H23.75V3.66634H25.2917C26.1396 3.66634 26.8655 3.96825 27.4693 4.57207C28.0731 5.17589 28.375 5.90176 28.375 6.74967V13.957C28.375 14.3938 28.2273 14.7599 27.9318 15.0554C27.6363 15.3509 27.2701 15.4986 26.8333 15.4986C26.3965 15.4986 26.0304 15.3509 25.7349 15.0554C25.4394 14.7599 25.2917 14.3938 25.2917 13.957V12.9163H3.70833V28.333H12.65C13.0868 28.333 13.453 28.4808 13.7484 28.7762C14.0439 29.0717 14.1917 29.4379 14.1917 29.8747C14.1917 30.3115 14.0439 30.6776 13.7484 30.9731C13.453 31.2686 13.0868 31.4163 12.65 31.4163H3.70833ZM23.75 32.958C21.6174 32.958 19.7995 32.2064 18.2964 30.7033C16.7932 29.2002 16.0417 27.3823 16.0417 25.2497C16.0417 23.117 16.7932 21.2992 18.2964 19.796C19.7995 18.2929 21.6174 17.5413 23.75 17.5413C25.8826 17.5413 27.7005 18.2929 29.2036 19.796C30.7068 21.2992 31.4583 23.117 31.4583 25.2497C31.4583 27.3823 30.7068 29.2002 29.2036 30.7033C27.7005 32.2064 25.8826 32.958 23.75 32.958ZM26.3323 28.9111L27.4115 27.832L24.5208 24.9413V20.6247H22.9792V25.558L26.3323 28.9111Z"
                            fill="#4C7660" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #subtitle-area>
            Lihat Agenda
        </template>

        <div class="h-full pb-[48px] pt-[27px]">

            <div class="overflow-hidden bg-white">
                <div class="">
                     <div class="p-[5.3px] sm:p-[6px] md:p-[6.7px] lg:p-[7.3px] xl:p-[8px] 2xl:p-[9.3px] 3xl:p-[10.7px] 4xl:p-[12px]">
                            <Labels :type="eventStatus.type" :text="eventStatus.text" />
                            </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-[22px] gap-y-[15px]">
                        <div class="flex flex-col gap-y-[15px]">
                            <!-- First column -->
                            <div>
                                <SingleLineField id="title" :modelValue="event.title" type="text" fieldName="Judul"
                                    disabled custom-width="100%" />
                            </div>
                            <!-- Date Field -->
                            <div>
                                <SingleLineField id="date" :modelValue="formatDate(event.date)" type="date"
                                    fieldName="Tanggal" disabled custom-width="100%" />
                            </div>
                        </div>

                        <div class="flex flex-col gap-y-[15px]">
                            <!-- Second column -->
                            <div>
                                <SingleLineField id="location" :modelValue="event.location" type="text"
                                    fieldName="Lokasi" disabled custom-width="100%" />
                            </div>

                            <!-- Time Field -->
                            <div>
                                <SingleLineField id="time" :modelValue="event.time" type="time" fieldName="Waktu"
                                    disabled custom-width="100%" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <TextAreaInput id="description" :modelValue="event.description" type="text"
                            fieldName="Deskripsi" disabled custom-width="100%" />
                    </div>

                    <div class="mt-[15px] flex justify-end">
                        <Link :href="route('dashboard.events.index')">
                        <SecondaryButton type="button">
                            KEMBALI
                        </SecondaryButton>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
