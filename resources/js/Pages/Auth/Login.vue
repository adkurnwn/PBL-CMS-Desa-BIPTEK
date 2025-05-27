<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import ApplicationLogo from "@/Components/DashboardComponent/ApplicationLogo.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, onMounted } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
    profile: Object,
});

const showPassword = ref(false);
const form = useForm({
    email: "",
    password: "",
});

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};

// Add Material Icons stylesheet
onMounted(() => {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=visibility,visibility_off';
    document.head.appendChild(link);
});
</script>

<template>
    <GuestLayout :profile="profile">
        <Head title="Log in" />

        <header class="flex flex-col justify-center self-center max-w-full text-center text-stone-100 sm:w-[175px] md:w-[219px] lg:w-[269px] xl:w-[350px] 2xl:w-[394px] 3xl:w-[459px] 4xl:w-[525px]">
            <ApplicationLogo :logo="profile.logo" class="fill-current self-center max-w-full sm:w-[54px] md:w-[68px] lg:w-[84px] xl:w-[109px] 2xl:w-[122px] 3xl:w-[142px] 4xl:w-[163px] h-auto" />
            <h2 class="sm:text-[10px] md:text-[13px] lg:text-[16px] xl:text-[21px] 2xl:text-[23px] 3xl:text-[27px] 4xl:text-[31px] max-md:max-w-full">
                Content Management System
            </h2>
            <h1 class="sm:text-[15px] md:text-[19px] lg:text-[23px] xl:text-[30px] 2xl:text-[34px] 3xl:text-[39px] 4xl:text-[45px] font-bold max-md:max-w-full max-md:text-4xl">
                {{ profile.name }}
            </h1>
        </header>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <!-- Form Section -->
        <form @submit.prevent="submit" class="mt-6 w-full sm:text-[7px] md:text-[9px] lg:text-[11px] xl:text-[14.67px] 2xl:text-[16.5px] 3xl:text-[19.25px] text-[22px] font-medium whitespace-nowrap max-md:max-w-full" autocomplete="on">
            <!-- Email Field -->
            <div class="flex flex-col place-self-center sm:p-[4px] md:p-[5px] lg:p-[6px] xl:p-2 2xl:p-[9px] 3xl:p-[10.5px] w-full sm:max-w-[200px] md:max-w-[250px] lg:max-w-[307px] xl:max-w-[400px] 2xl:max-w-[450px] 3xl:max-w-[525px] 4xl:max-w-[600px] max-md:max-w-full">
                <label for="email" class="sm:text-[7px] md:text-[9px] lg:text-[11px] xl:text-[14.67px] 2xl:text-[16.5px] 3xl:text-[19.25px] text-[22px] text-type1 max-md:max-w-full">
                    Email
                </label>
                <div class="sm:h-[17px] sm:rounded-[1.67px] md:h-[21px] md:rounded-[2.08px] lg:h-[26px] lg:rounded-[2.56px] xl:h-[34px] xl:rounded-[3.33px] 2xl:h-[38px] 2xl:rounded-[3.75px] 3xl:h-[44px] 3xl:rounded-[4.38px] 4xl:h-[51px] 4xl:rounded-[5px] flex overflow-hidden gap-2.5 text-type2 items-center mt-2.5 w-full border-none max-md:max-w-full">
                    <input
                        id="email"
                        type="email"
                        name="email"
                        v-model="form.email"
                        class="w-full sm:text-[7px] sm:p-[6.5px] md:text-[9px] md:p-[8.1px] lg:text-[11px] lg:p-[10px] xl:text-[14.67px] xl:p-[13px] 2xl:text-[16.5px] 2xl:p-[15px] 3xl:text-[19.25px] 3xl:p-[17px] text-[22px] p-5 rounded-lg placeholder:italic"
                        placeholder="email@contoh.com"
                        required
                        autofocus
                        autocomplete="email"
                    />
                </div>
                <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                    {{ form.errors.email }}
                </div>
            </div>

            <!-- Password Field -->
            <div class="flex flex-col place-self-center sm:p-[4px] md:p-[5px] lg:p-[6px] xl:p-2 2xl:p-[9px] 3xl:p-[10.5px] p-3 w-full sm:max-w-[200px] md:max-w-[250px] lg:max-w-[307px] xl:max-w-[400px] 2xl:max-w-[450px] 3xl:max-w-[525px] 4xl:max-w-[600px] max-md:max-w-full">
                <label for="password" class="sm:text-[7px] md:text-[9px] lg:text-[11px] xl:text-[14.67px] 2xl:text-[16.5px] 3xl:text-[19.25px] text-[22px] text-type1 max-md:max-w-full">
                    Password
                </label>
                <div class="flex overflow-hidden gap-2.5 text-type2 items-center mt-2.5 w-full border-none max-md:max-w-full">
                    <div class="relative flex items-center w-full">
                        <input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            v-model="form.password"
                            class="sm:h-[17px] sm:rounded-[1.67px] sm:text-[7px] md:h-[21px] md:rounded-[2.08px] md:text-[9px] lg:h-[26px] lg:rounded-[2.56px] lg:text-[11px] xl:h-[34px] xl:rounded-[3.33px] xl:text-[14.67px] 2xl:h-[38px] 2xl:rounded-[3.75px] 2xl:text-[16.5px] 3xl:h-[44px] 3xl:rounded-[4.38px] 3xl:text-[19.25px] 4xl:h-[51px] 4xl:rounded-[5px] text-[22px] flex overflow-hidden gap-2.5 text-type2 items-center w-full border-none max-md:max-w-full outline-none pr-12 placeholder:italic"
                            placeholder="Password"
                            required
                            autocomplete="current-password"
                        />
                        <span 
                            class="material-symbols-outlined cursor-pointer absolute sm:right-[5.5px] md:right-[6.9px] lg:right-[8.5px] xl:right-[11px] 2xl:right-[12px] 3xl:right-[13px] right-4 select-none"
                            @click="togglePasswordVisibility"
                            style="background-color: transparent; font-size: 20px;"
                            :style="{
                                fontSize: 'clamp(6.67px, 2.08vw, 20px)'
                            }"
                        >
                            {{ showPassword ? 'visibility_off' : 'visibility' }}
                        </span>
                    </div>
                </div>
                <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                    {{ form.errors.password }}
                </div>
            </div>

            <!-- Login Button and Forgot Password -->
            <div class="flex flex-col mt-6 w-full font-medium text-center text-white max-md:max-w-full">
                <button
                    type="submit"
                    class="overflow-hidden gap-2.5 self-center sm:px-2 sm:py-1 sm:text-[7px] sm:w-[83px] sm:h-[17px] sm:rounded-[3.33px] md:px-[2.5px] md:py-[1.25px] md:text-[9px] md:w-[104px] md:h-[21px] md:rounded-[4.17px] lg:px-3 lg:py-[1.5px] lg:text-[11px] lg:w-[128px] lg:h-[26px] lg:rounded-[5.13px] xl:px-4 xl:py-2 xl:text-[14.67px] xl:w-[167px] xl:h-[34px] xl:rounded-[6.67px] 2xl:px-[18px] 2xl:py-[9px] 2xl:text-[16.5px] 2xl:w-[188px] 2xl:h-[38px] 2xl:rounded-[7.5px] 3xl:px-5 3xl:py-[10px] 3xl:text-[19.25px] 3xl:w-[219px] 3xl:h-[44px] 3xl:rounded-[8.75px] px-6 py-3 max-w-full text-[22px] bg-accent2 rounded-[10px] w-[250px] h-[51px] max-md:px-5 hover:bg-type2 transition-colors disabled:opacity-25"
                    :disabled="form.processing"
                >
                    LOG IN
                </button>
                <a
                    v-if="canResetPassword"
                     @click="$inertia.visit(route('password.request'))"
                    class="mt-2.5 sm:text-[6px] md:text-[7.5px] lg:text-[9.23px] xl:text-[12px] 2xl:text-[13.5px] 3xl:text-[15.75px] text-[18px] underline underline-offset-2 cursor-pointer max-md:max-w-full hover:text-gray-300 transition-colors"
                >
                    Lupa Password?
                </a>
            </div>
        </form>

        <div v-if="$page.props.flash?.error" class="mt-4 p-4 bg-red-100 text-red-700 rounded-md">
            {{ $page.props.flash.error }}
        </div>
    </GuestLayout>
</template>
