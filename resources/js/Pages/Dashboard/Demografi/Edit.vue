<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    demografi: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    tahun: props.demografi.tahun,
    penduduk_pria: props.demografi.penduduk_pria,
    penduduk_wanita: props.demografi.penduduk_wanita,
    jumlah_peserta_bpjs: props.demografi.jumlah_peserta_bpjs,
    jumlah_pindah_keluar: props.demografi.jumlah_pindah_keluar,
    jumlah_pendatang: props.demografi.jumlah_pendatang,
    tamat_pt: props.demografi.tamat_pt,
    tamat_sma: props.demografi.tamat_sma,
    tamat_smp: props.demografi.tamat_smp,
    tamat_sd: props.demografi.tamat_sd,
    jumlah_lansia: props.demografi.jumlah_lansia,
    jumlah_dewasa: props.demografi.jumlah_dewasa,
    jumlah_remaja: props.demografi.jumlah_remaja,
    jumlah_anak: props.demografi.jumlah_anak,
    jumlah_penduduk: props.demografi.jumlah_penduduk,
    jumlah_keluarga: props.demografi.jumlah_keluarga,
    _method: "PUT"
});

// Calculate total population when gender populations change
const calculateTotalPopulation = () => {
    if (form.penduduk_pria && form.penduduk_wanita) {
        form.jumlah_penduduk = parseInt(form.penduduk_pria) + parseInt(form.penduduk_wanita);
    }
};

const submit = () => {
    form.post(route("dashboard.demografis.update", props.demografi.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit Demografi" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Demographic Data
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Year Field -->
                                <div>
                                    <InputLabel for="tahun" value="Tahun" />
                                    <TextInput
                                        id="tahun"
                                        v-model="form.tahun"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="1900"
                                        max="2100"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.tahun"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Gender Population -->
                                <div>
                                    <InputLabel
                                        for="penduduk_pria"
                                        value="Jumlah Penduduk Pria"
                                    />
                                    <TextInput
                                        id="penduduk_pria"
                                        v-model="form.penduduk_pria"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                        @input="calculateTotalPopulation"
                                    />
                                    <InputError
                                        :message="form.errors.penduduk_pria"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="penduduk_wanita"
                                        value="Jumlah Penduduk Wanita"
                                    />
                                    <TextInput
                                        id="penduduk_wanita"
                                        v-model="form.penduduk_wanita"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                        @input="calculateTotalPopulation"
                                    />
                                    <InputError
                                        :message="form.errors.penduduk_wanita"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Age Group Population -->
                                <div>
                                    <InputLabel
                                        for="jumlah_anak"
                                        value="Jumlah Anak-anak"
                                    />
                                    <TextInput
                                        id="jumlah_anak"
                                        v-model="form.jumlah_anak"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.jumlah_anak"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="jumlah_remaja"
                                        value="Jumlah Remaja"
                                    />
                                    <TextInput
                                        id="jumlah_remaja"
                                        v-model="form.jumlah_remaja"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.jumlah_remaja"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="jumlah_dewasa"
                                        value="Jumlah Dewasa"
                                    />
                                    <TextInput
                                        id="jumlah_dewasa"
                                        v-model="form.jumlah_dewasa"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.jumlah_dewasa"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="jumlah_lansia"
                                        value="Jumlah Lansia"
                                    />
                                    <TextInput
                                        id="jumlah_lansia"
                                        v-model="form.jumlah_lansia"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.jumlah_lansia"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Education Levels -->
                                <div>
                                    <InputLabel
                                        for="tamat_sd"
                                        value="Tamat SD"
                                    />
                                    <TextInput
                                        id="tamat_sd"
                                        v-model="form.tamat_sd"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.tamat_sd"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="tamat_smp"
                                        value="Tamat SMP"
                                    />
                                    <TextInput
                                        id="tamat_smp"
                                        v-model="form.tamat_smp"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.tamat_smp"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="tamat_sma"
                                        value="Tamat SMA"
                                    />
                                    <TextInput
                                        id="tamat_sma"
                                        v-model="form.tamat_sma"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.tamat_sma"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="tamat_pt"
                                        value="Tamat Perguruan Tinggi"
                                    />
                                    <TextInput
                                        id="tamat_pt"
                                        v-model="form.tamat_pt"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.tamat_pt"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Other Demographics -->
                                <div>
                                    <InputLabel
                                        for="jumlah_penduduk"
                                        value="Total Jumlah Penduduk"
                                    />
                                    <TextInput
                                        id="jumlah_penduduk"
                                        v-model="form.jumlah_penduduk"
                                        type="number"
                                        class="mt-1 block w-full bg-gray-100"
                                        readonly
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.jumlah_penduduk"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for
