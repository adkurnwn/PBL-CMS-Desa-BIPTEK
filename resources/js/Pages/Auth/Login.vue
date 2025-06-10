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

        <Head title="Login" />
        <div class="justify-center items-center flex flex-col px-[3%]">
            <header
                class="flex flex-col text-center text-stone-100">
                <ApplicationLogo :logo="profile.logo"
                    class="fill-current self-center w-28 h-auto" />
                <h2
                    class="text-xl xl:text-2xl">
                    Content Management System
                </h2>
                <h1
                    class="text-2xl xl:text-3xl font-bold">
                    {{ profile.name }}
                </h1>
            </header>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <!-- Form Section -->
            <form @submit.prevent="submit"
                class="mt-6 w-full px-[3%] md:px-0 max-w-sm font-medium whitespace-nowrap items-center justify-center flex flex-col"
                autocomplete="on">
                <!-- Email Field -->
                <div
                    class="flex flex-col place-self-center p-[4px] w-full">
                    <label for="email"
                        class="text-xl xl:text-lg text-type1 w-full">
                        Email
                    </label>
                    <div
                        class="">
                        <input id="email" type="email" name="email" v-model="form.email"
                            class="w-full rounded-lg placeholder:italic"
                            placeholder="email@contoh.com" required autofocus autocomplete="email" />
                    </div>
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>

                <!-- Password Field -->
                <div
                    class="flex flex-col place-self-center p-[4px] w-full mt-2">
                    <label for="password"
                        class="text-xl xl:text-lg text-type1">
                        Password
                    </label>
                    <div
                        class="flex overflow-hidden gap-2.5 text-type2 items-center w-full border-none max-md:max-w-full">
                        <div class="relative flex items-center w-full">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                                v-model="form.password"
                                class="w-full rounded-lg placeholder:italic"
                                placeholder="Password" required autocomplete="current-password" />
                            <span
                                class="material-symbols-outlined cursor-pointer absolute sm:right-[5.5px] md:right-[6.9px] lg:right-[8.5px] xl:right-[11px] 2xl:right-[12px] 3xl:right-[13px] right-4 select-none"
                                @click="togglePasswordVisibility"
                                style="background-color: transparent; font-size: 20px;" :style="{
                                    fontSize: 'clamp(6.67px, 2.08vw, 20px)'
                                }">
                                {{ showPassword ? 'visibility_off' : 'visibility' }}
                            </span>
                        </div>
                    </div>
                    <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                        {{ form.errors.password }}
                    </div>
                </div>

                <!-- Login Button and Forgot Password -->
                <div class="flex flex-col mt-6 items-center w-full max-w-48 justify-center font-medium text-center text-white">
                    <button type="submit"
                        class="overflow-hidden gap-2.5 self-center rounded-lg w-full py-2 bg-accent2 hover:opacity-40 transition-colors duration-300 disabled:opacity-25"
                        :disabled="form.processing">
                        LOG IN
                    </button>
                    <a v-if="canResetPassword" @click="$inertia.visit(route('password.request'))"
                        class="mt-2.5 underline underline-offset-2 cursor-pointer max-md:max-w-full hover:text-gray-300 transition-colors">
                        Lupa Password?
                    </a>
                </div>
            </form>

            <div v-if="$page.props.flash?.error" class="mt-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ $page.props.flash.error }}
            </div>
        </div>
    </GuestLayout>
</template>
