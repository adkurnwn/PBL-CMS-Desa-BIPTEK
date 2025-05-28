<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ApplicationLogo from "@/Components/DashboardComponent/ApplicationLogo.vue";
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

defineProps({
    status: {
        type: String,
    },
    profile: Object,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};

// Add Material Icons stylesheet
onMounted(() => {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200';
    document.head.appendChild(link);
});
</script>

<template>
    <GuestLayout :profile="profile">

        <Head title="Forgot Password" />

        <header
            class="flex pb-6 flex-col justify-center self-center max-w-full text-center text-stone-100 sm:w-[175px] md:w-[219px] lg:w-[269px] xl:w-[350px] 2xl:w-[394px] 3xl:w-[459px] 4xl:w-[525px]">
            <ApplicationLogo :logo="profile.logo"
                class="fill-current self-center max-w-full sm:w-[54px] md:w-[68px] lg:w-[84px] xl:w-[109px] 2xl:w-[122px] 3xl:w-[142px] 4xl:w-[163px] h-auto" />
            <h2
                class="sm:text-[10px] md:text-[13px] lg:text-[16px] xl:text-[21px] 2xl:text-[23px] 3xl:text-[27px] 4xl:text-[31px] max-md:max-w-full">
                Content Management System
            </h2>
            <h1
                class="sm:text-[15px] md:text-[19px] lg:text-[23px] xl:text-[30px] 2xl:text-[34px] 3xl:text-[39px] 4xl:text-[45px] font-bold max-md:max-w-full max-md:text-4xl">
                {{ profile.name }}
            </h1>
        </header>

        <div
            class="mb-4 italic sm:text-[7px] md:text-[9px] lg:text-[11px] xl:text-[14.67px] 2xl:text-[16.5px] 3xl:text-[19.25px] text-[22px] text-stone-100 text-center max-w-full sm:max-w-[200px] md:max-w-[250px] lg:max-w-[307px] xl:max-w-[400px] 2xl:max-w-[450px] 3xl:max-w-[525px] 4xl:max-w-[600px]">
            Lupa lupa kata sandi? Masukkan alamat email Anda di bawah ini, dan kami akan mengirimkan tautan untuk
            mengatur ulang kata sandi Anda.
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit"
            class="w-full sm:text-[7px] md:text-[9px] lg:text-[11px] xl:text-[14.67px] 2xl:text-[16.5px] 3xl:text-[19.25px] text-[22px] font-medium whitespace-nowrap max-md:max-w-full">
            <!-- Email Field -->
            <div
                class="flex flex-col place-self-center  w-full sm:max-w-[200px] md:max-w-[250px] lg:max-w-[307px] xl:max-w-[400px] 2xl:max-w-[450px] 3xl:max-w-[525px] 4xl:max-w-[600px] max-md:max-w-full">
                <label for="email"
                    class="sm:text-[7px] md:text-[9px] lg:text-[11px] xl:text-[14.67px] 2xl:text-[16.5px] 3xl:text-[19.25px] text-[22px] text-type1 max-md:max-w-full">
                    Email
                </label>
                <div
                    class="sm:h-[17px] sm:rounded-[1.67px] md:h-[21px] md:rounded-[2.08px] lg:h-[26px] lg:rounded-[2.56px] xl:h-[34px] xl:rounded-[3.33px] 2xl:h-[38px] 2xl:rounded-[3.75px] 3xl:h-[44px] 3xl:rounded-[4.38px] 4xl:h-[51px] 4xl:rounded-[5px] flex overflow-hidden gap-2.5 text-type2 items-center mt-2.5 w-full border-none max-md:max-w-full">
                    <input id="email" type="email" name="email" v-model="form.email"
                        class="w-full sm:text-[7px] sm:p-[6.5px] md:text-[9px] md:p-[8.1px] lg:text-[11px] lg:p-[10px] xl:text-[14.67px] xl:p-[13px] 2xl:text-[16.5px] 2xl:p-[15px] 3xl:text-[19.25px] 3xl:p-[17px] text-[22px] p-5 rounded-lg placeholder:italic"
                        placeholder="email@contoh.com" required autofocus autocomplete="username" />
                </div>
                <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                    {{ form.errors.email }}
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex flex-row sm:gap-1 md:gap-[1.25px] lg:gap-[1.5px] xl:gap-2 2xl:gap-[2.25px] 3xl:gap-[2.6px] gap-3 mt-6 w-full font-medium text-center text-white max-md:max-w-full justify-center sm:max-w-[200px] md:max-w-[250px] lg:max-w-[307px] xl:max-w-[400px] 2xl:max-w-[450px] 3xl:max-w-[525px] 4xl:max-w-[600px] mx-auto">
                <button type="button"
                    class="overflow-hidden flex-1 sm:py-1 sm:text-[7px] sm:h-[17px] sm:rounded-[3.33px] md:py-[1.25px] md:text-[9px] md:h-[21px] md:rounded-[4.17px] lg:py-[1.5px] lg:text-[11px] lg:h-[26px] lg:rounded-[5.13px] xl:py-2 xl:text-[14.67px] xl:h-[34px] xl:rounded-[6.67px] 2xl:py-[9px] 2xl:text-[16.5px] 2xl:h-[38px] 2xl:rounded-[7.5px] 3xl:py-[10px] 3xl:text-[19.25px] 3xl:h-[44px] 3xl:rounded-[8.75px] py-3 text-[22px] bg-accent2 rounded-[10px] h-[51px] max-md:px-5 hover:bg-type2 transition-colors"
                    @click="$inertia.visit(route('login'))">
                    LOGIN
                </button>
                <button type="submit"
                    class="overflow-hidden flex-1 sm:py-1 sm:text-[7px] sm:h-[17px] sm:rounded-[3.33px] md:py-[1.25px] md:text-[9px] md:h-[21px] md:rounded-[4.17px] lg:py-[1.5px] lg:text-[11px] lg:h-[26px] lg:rounded-[5.13px] xl:py-2 xl:text-[14.67px] xl:h-[34px] xl:rounded-[6.67px] 2xl:py-[9px] 2xl:text-[16.5px] 2xl:h-[38px] 2xl:rounded-[7.5px] 3xl:py-[10px] 3xl:text-[19.25px] 3xl:h-[44px] 3xl:rounded-[8.75px] py-3 text-[22px] bg-accent2 rounded-[10px] h-[51px] max-md:px-5 hover:bg-type2 transition-colors disabled:opacity-25"
                    :disabled="form.processing">
                    RESET PASSWORD
                </button>
            </div>
        </form>

        <div v-if="$page.props.flash?.error" class="mt-4 p-4 bg-red-100 text-red-700 rounded-md">
            {{ $page.props.flash.error }}
        </div>
    </GuestLayout>
</template>
