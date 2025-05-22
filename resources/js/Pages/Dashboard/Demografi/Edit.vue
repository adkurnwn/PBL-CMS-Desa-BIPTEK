<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { Link } from "@inertiajs/vue3";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { ref, watch } from "vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";

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
    _method: "PUT",
});

// Calculate total population when gender populations change
const calculateTotalPopulation = () => {
    if (form.penduduk_pria && form.penduduk_wanita) {
        form.jumlah_penduduk =
            parseInt(form.penduduk_pria) + parseInt(form.penduduk_wanita);
    }
};

const validationErrors = ref({});

// Run validation on specific field
const validateField = (field, label) => {
    const totalPopulation = parseInt(form.jumlah_penduduk || 0);
    const value = parseInt(form[field] || 0);
    
    // Clear previous error for this field
    if (validationErrors.value[field]) {
        delete validationErrors.value[field];
    }
    
    // Check if value exceeds total population
    if (value > totalPopulation) {
        validationErrors.value[field] = `${label} tidak boleh melebihi total penduduk (${totalPopulation})`;
        return false;
    }
    
    return true;
};

// Validate group totals
const validateGroupTotals = () => {
    const totalPopulation = parseInt(form.jumlah_penduduk || 0);
    
    // Check sum of age groups
    const totalByAge = 
        parseInt(form.jumlah_anak || 0) + 
        parseInt(form.jumlah_remaja || 0) + 
        parseInt(form.jumlah_dewasa || 0) + 
        parseInt(form.jumlah_lansia || 0);
    
    // Clear previous errors for age groups
    if (validationErrors.value.age_groups) {
        delete validationErrors.value.age_groups;
    }
    
    if (totalByAge > totalPopulation) {
        validationErrors.value.age_groups = `Total kelompok umur (${totalByAge}) melebihi total penduduk (${totalPopulation})`;
    } else if (totalByAge < totalPopulation && totalPopulation > 0) {
        validationErrors.value.age_groups = `Total kelompok umur (${totalByAge}) harus sama dengan total penduduk (${totalPopulation})`;
    }
    
    // Check sum of education levels
    const totalByEducation = 
        parseInt(form.tamat_sd || 0) + 
        parseInt(form.tamat_smp || 0) + 
        parseInt(form.tamat_sma || 0) + 
        parseInt(form.tamat_pt || 0);
    
    // Clear previous errors for education levels
    if (validationErrors.value.education_levels) {
        delete validationErrors.value.education_levels;
    }
    
    if (totalByEducation > totalPopulation) {
        validationErrors.value.education_levels = `Total tingkat pendidikan (${totalByEducation}) melebihi total penduduk (${totalPopulation})`;
    }
};

// Complete validation of all demographic data
const validateDemographicData = () => {
    // Fields that should not exceed total population
    const fieldsToValidate = [
        { field: 'jumlah_anak', label: 'Jumlah Anak-anak' },
        { field: 'jumlah_remaja', label: 'Jumlah Remaja' },
        { field: 'jumlah_dewasa', label: 'Jumlah Dewasa' },
        { field: 'jumlah_lansia', label: 'Jumlah Lansia' },
        { field: 'tamat_sd', label: 'Tamat SD' },
        { field: 'tamat_smp', label: 'Tamat SMP' },
        { field: 'tamat_sma', label: 'Tamat SMA' },
        { field: 'tamat_pt', label: 'Tamat Perguruan Tinggi' },
        { field: 'jumlah_peserta_bpjs', label: 'Jumlah Peserta BPJS' },
        { field: 'jumlah_pendatang', label: 'Jumlah Pendatang' },
        { field: 'jumlah_keluarga', label: 'Jumlah KK' }
    ];
    
    // Validate each field
    fieldsToValidate.forEach(({ field, label }) => {
        validateField(field, label);
    });
    
    // Validate group totals
    validateGroupTotals();
    
    return Object.keys(validationErrors.value).length === 0;
};

// Set up watchers for live validation
const setupWatchers = () => {
    // Watch fields that need population validation
    const fieldsToWatch = [
        { field: 'jumlah_anak', label: 'Jumlah Anak-anak' },
        { field: 'jumlah_remaja', label: 'Jumlah Remaja' },
        { field: 'jumlah_dewasa', label: 'Jumlah Dewasa' },
        { field: 'jumlah_lansia', label: 'Jumlah Lansia' },
        { field: 'tamat_sd', label: 'Tamat SD' },
        { field: 'tamat_smp', label: 'Tamat SMP' },
        { field: 'tamat_sma', label: 'Tamat SMA' },
        { field: 'tamat_pt', label: 'Tamat Perguruan Tinggi' },
        { field: 'jumlah_peserta_bpjs', label: 'Jumlah Peserta BPJS' },
        { field: 'jumlah_pendatang', label: 'Jumlah Pendatang' },
        { field: 'jumlah_keluarga', label: 'Jumlah KK' }
    ];
    
    // Watch gender population fields to recalculate total and validate other fields
    watch([() => form.penduduk_pria, () => form.penduduk_wanita], () => {
        calculateTotalPopulation();
        validateDemographicData();
    });
    
    // Watch total population to trigger validation of all fields
    watch(() => form.jumlah_penduduk, () => {
        validateDemographicData();
    });
    
    // Watch individual demographic fields
    fieldsToWatch.forEach(({ field, label }) => {
        watch(() => form[field], () => {
            validateField(field, label);
            validateGroupTotals();
        });
    });
};

// Initialize watchers
setupWatchers();

const submit = () => {
    // Validate one last time before submission
    const isValid = validateDemographicData();
    
    // Only proceed if validation passes
    if (isValid) {
        form.post(route("dashboard.demografi.update", props.demografi.tahun), {
            preserveScroll: true,
        });
    } else {
        // Scroll to top to show error messages
        window.scrollTo(0, 0);
    }
};
</script>

<template>
    <Head title="Edit Demografi" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Edit Demografi" :hasRightContent="false">
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
        
        <template #subtitle-area>
            Edit Data Demografi
        </template>

        <div class="h-full pb-[48px] pt-[27px]">
            <!-- Global Validation Errors -->
            <div v-if="Object.keys(validationErrors).length > 0" 
                class="mb-4 p-4 bg-red-50 border border-red-500 text-red-700 rounded-md ml-[12px]">
                <h3 class="font-bold">Mohon perbaiki data berikut:</h3>
                <ul class="list-disc ml-5 mt-2">
                    <li v-for="(error, field) in validationErrors" :key="field">
                        {{ error }}
                    </li>
                </ul>
            </div>
            
            <div class="overflow-hidden bg-white sm:rounded-lg">
                <div class="">
                    <form @submit.prevent="submit">
                        <!-- Year Field (Standalone) -->
                        <div class="mb-6">
                            <div>
                                <SingleLineField
                                    id="tahun"
                                    v-model="form.tahun"
                                    type="number"
                                    fieldName="Tahun"
                                    min="1900"
                                    max="2100"
                                    required
                                    custom-width="50%"
                                />
                                <InputError
                                    :message="form.errors.tahun"
                                    class="mt-2"
                                />
                            </div>
                        </div>

                        <!-- Population Data Section -->
                        <div class="mb-6">
                            <h3 class="font-medium text-lg text-menuText mb-3 pl-[12px]">Data Umum</h3>
                            <div class="grid grid-cols-2 gap-x-[22px] gap-y-[15px]">
                                <!-- First column -->
                                <div class="flex flex-col gap-y-[15px]">
                                    <!-- Gender Population -->
                                    <div>
                                        <SingleLineField
                                            id="penduduk_pria"
                                            v-model="form.penduduk_pria"
                                            type="number"
                                            fieldName="Jumlah Penduduk Pria"
                                            min="0"
                                            required
                                            @input="calculateTotalPopulation"
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.penduduk_pria"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <SingleLineField
                                            id="penduduk_wanita"
                                            v-model="form.penduduk_wanita"
                                            type="number"
                                            fieldName="Jumlah Penduduk Wanita"
                                            min="0"
                                            required
                                            @input="calculateTotalPopulation"
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.penduduk_wanita"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Second column -->
                                <div class="flex flex-col gap-y-[15px]">
                                    <div>
                                        <SingleLineField
                                            id="jumlah_penduduk"
                                            v-model="form.jumlah_penduduk"
                                            type="number"
                                            fieldName="Total Jumlah Penduduk"
                                            readonly
                                            disabled
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.jumlah_penduduk"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <SingleLineField
                                            id="jumlah_keluarga"
                                            v-model="form.jumlah_keluarga"
                                            type="number"
                                            fieldName="Jumlah KK"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.jumlah_keluarga"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Age Group Section -->
                        <div class="mb-6">
                            <h3 class="font-medium text-lg text-menuText mb-3 pl-[12px]">Berdasarkan Kelompok Umur</h3>
                            <div class="grid grid-cols-2 gap-x-[22px] gap-y-[15px]">
                                <div class="flex flex-col gap-y-[15px]">
                                    <div>
                                        <SingleLineField
                                            id="jumlah_anak"
                                            v-model="form.jumlah_anak"
                                            type="number"
                                            fieldName="Jumlah Anak-anak"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.jumlah_anak"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <SingleLineField
                                            id="jumlah_remaja"
                                            v-model="form.jumlah_remaja"
                                            type="number"
                                            fieldName="Jumlah Remaja"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.jumlah_remaja"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-y-[15px]">
                                    <div>
                                        <SingleLineField
                                            id="jumlah_dewasa"
                                            v-model="form.jumlah_dewasa"
                                            type="number"
                                            fieldName="Jumlah Dewasa"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.jumlah_dewasa"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <SingleLineField
                                            id="jumlah_lansia"
                                            v-model="form.jumlah_lansia"
                                            type="number"
                                            fieldName="Jumlah Lansia"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.jumlah_lansia"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Education Section -->
                        <div class="mb-6">
                            <h3 class="font-medium text-lg text-menuText mb-3 pl-[12px]">Berdasarkan Tingkat Pendidikan</h3>
                            <div class="grid grid-cols-2 gap-x-[22px] gap-y-[15px]">
                                <div class="flex flex-col gap-y-[15px]">
                                    <div>
                                        <SingleLineField
                                            id="tamat_sd"
                                            v-model="form.tamat_sd"
                                            type="number"
                                            fieldName="Tamat SD"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.tamat_sd"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <SingleLineField
                                            id="tamat_smp"
                                            v-model="form.tamat_smp"
                                            type="number"
                                            fieldName="Tamat SMP"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.tamat_smp"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-y-[15px]">
                                    <div>
                                        <SingleLineField
                                            id="tamat_sma"
                                            v-model="form.tamat_sma"
                                            type="number"
                                            fieldName="Tamat SMA"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.tamat_sma"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <SingleLineField
                                            id="tamat_pt"
                                            v-model="form.tamat_pt"
                                            type="number"
                                            fieldName="Tamat Perguruan Tinggi"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.tamat_pt"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Other Demographics Section -->
                        <div class="mb-6">
                            <h3 class="font-medium text-lg text-menuText mb-3 pl-[12px]">Data Lainnya</h3>
                            <div class="grid grid-cols-2 gap-x-[22px] gap-y-[15px]">
                                <div class="flex flex-col gap-y-[15px]">
                                    <div>
                                        <SingleLineField
                                            id="jumlah_peserta_bpjs"
                                            v-model="form.jumlah_peserta_bpjs"
                                            type="number"
                                            fieldName="Jumlah Peserta BPJS"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.jumlah_peserta_bpjs"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-y-[15px]">
                                    <div>
                                        <SingleLineField
                                            id="jumlah_pendatang"
                                            v-model="form.jumlah_pendatang"
                                            type="number"
                                            fieldName="Jumlah Pendatang"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.jumlah_pendatang"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <SingleLineField
                                            id="jumlah_pindah_keluar"
                                            v-model="form.jumlah_pindah_keluar"
                                            type="number"
                                            fieldName="Jumlah Pindah Keluar"
                                            min="0"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.jumlah_pindah_keluar"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-[15px] flex flex-row justify-end">
                            <Link :href="route('dashboard.demografi.index')" class="mr-[15px]">
                                <SecondaryButton>BATAL</SecondaryButton>
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
