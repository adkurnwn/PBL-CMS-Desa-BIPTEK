<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import { ref } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";

const form = useForm({
    tahun: new Date().getFullYear(),
    penduduk_pria: "",
    penduduk_wanita: "",
    jumlah_peserta_bpjs: "",
    jumlah_pindah_keluar: "",
    jumlah_pendatang: "",
    tamat_pt: "",
    tamat_sma: "",
    tamat_smp: "",
    tamat_sd: "",
    jumlah_lansia: "",
    jumlah_dewasa: "",
    jumlah_remaja: "",
    jumlah_anak: "",
    jumlah_penduduk: "",
    jumlah_keluarga: "",
});

// Calculate total population when gender populations change
const calculateTotalPopulation = () => {
    if (form.penduduk_pria && form.penduduk_wanita) {
        form.jumlah_penduduk =
            parseInt(form.penduduk_pria) + parseInt(form.penduduk_wanita);
    }
};

const submit = () => {
    form.post(route("dashboard.demografi.store"), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Tambah Demografi" />
    <AuthenticatedLayout>
                <template #header>
            <HeadingMenu title="Tambah Demografi" :hasRightContent="false">
                <template #icon>
                    <svg width="37" height="38" viewBox="0 0 37 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_1439_7132" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="37" height="38">
                            <rect y="0.5" width="37" height="37" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1439_7132)">
                            <path
                                d="M27.75 35.9583C25.6174 35.9583 23.7995 35.2068 22.2964 33.7036C20.7932 32.2005 20.0417 30.3826 20.0417 28.25C20.0417 26.1174 20.7932 24.2995 22.2964 22.7964C23.7995 21.2932 25.6174 20.5417 27.75 20.5417C29.8826 20.5417 31.7005 21.2932 33.2036 22.7964C34.7068 24.2995 35.4583 26.1174 35.4583 28.25C35.4583 30.3826 34.7068 32.2005 33.2036 33.7036C31.7005 35.2068 29.8826 35.9583 27.75 35.9583ZM10.7917 14.375H26.2083V11.2917H10.7917V14.375ZM17.999 32.875H7.70833C6.86042 32.875 6.13455 32.5731 5.53073 31.9693C4.92691 31.3655 4.625 30.6396 4.625 29.7917V8.20833C4.625 7.36042 4.92691 6.63455 5.53073 6.03073C6.13455 5.42691 6.86042 5.125 7.70833 5.125H29.2917C30.1396 5.125 30.8655 5.42691 31.4693 6.03073C32.0731 6.63455 32.375 7.36042 32.375 8.20833V18.5375C31.6299 18.1778 30.8783 17.908 30.1203 17.7281C29.3623 17.5483 28.5722 17.4583 27.75 17.4583C27.4674 17.4583 27.204 17.4648 26.9599 17.4776C26.7158 17.4905 26.4653 17.5226 26.2083 17.574V17.4583H10.7917V20.5417H20.2344C19.7719 20.9785 19.3543 21.4538 18.9818 21.9677C18.6092 22.4816 18.2816 23.034 17.999 23.625H10.7917V26.7083H17.074C17.0226 26.9653 16.9905 27.2158 16.9776 27.4599C16.9648 27.704 16.9583 27.9674 16.9583 28.25C16.9583 29.0979 17.0354 29.888 17.1896 30.6203C17.3438 31.3526 17.6135 32.1042 17.999 32.875ZM27.75 28.25C28.3924 28.25 28.9384 28.0252 29.388 27.5755C29.8377 27.1259 30.0625 26.5799 30.0625 25.9375C30.0625 25.2951 29.8377 24.7491 29.388 24.2995C28.9384 23.8498 28.3924 23.625 27.75 23.625C27.1076 23.625 26.5616 23.8498 26.112 24.2995C25.6623 24.7491 25.4375 25.2951 25.4375 25.9375C25.4375 26.5799 25.6623 27.1259 26.112 27.5755C26.5616 28.0252 27.1076 28.25 27.75 28.25ZM27.75 32.875C28.5208 32.875 29.2403 32.6951 29.9083 32.3354C30.5764 31.9757 31.1288 31.4747 31.5656 30.8323C30.9747 30.4726 30.358 30.2092 29.7156 30.0422C29.0733 29.8752 28.4181 29.7917 27.75 29.7917C27.0819 29.7917 26.4267 29.8752 25.7844 30.0422C25.142 30.2092 24.5253 30.4726 23.9344 30.8323C24.3712 31.4747 24.9236 31.9757 25.5917 32.3354C26.2597 32.6951 26.9792 32.875 27.75 32.875Z"
                                fill="#4C7660" />
                        </g>
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
                                        for="jumlah_keluarga"
                                        value="Jumlah KK"
                                    />
                                    <TextInput
                                        id="jumlah_keluarga"
                                        v-model="form.jumlah_keluarga"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.jumlah_keluarga"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="jumlah_peserta_bpjs"
                                        value="Jumlah Peserta BPJS"
                                    />
                                    <TextInput
                                        id="jumlah_peserta_bpjs"
                                        v-model="form.jumlah_peserta_bpjs"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="
                                            form.errors.jumlah_peserta_bpjs
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="jumlah_pendatang"
                                        value="Jumlah Pendatang"
                                    />
                                    <TextInput
                                        id="jumlah_pendatang"
                                        v-model="form.jumlah_pendatang"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.jumlah_pendatang"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="jumlah_pindah_keluar"
                                        value="Jumlah Pindah Keluar"
                                    />
                                    <TextInput
                                        id="jumlah_pindah_keluar"
                                        v-model="form.jumlah_pindah_keluar"
                                        type="number"
                                        class="mt-1 block w-full"
                                        min="0"
                                        required
                                    />
                                    <InputError
                                        :message="
                                            form.errors.jumlah_pindah_keluar
                                        "
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Create Demographic Data
                                </PrimaryButton>
                                <Link
                                    :href="route('dashboard.demografi.index')"
                                >
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
