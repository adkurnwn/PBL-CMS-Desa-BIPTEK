<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
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

        <header class="flex flex-col justify-center self-center max-w-full text-center text-stone-100 w-[525px]">
            <ApplicationLogo :logo="profile.logo" class="fill-current self-center max-w-full w-[163px] h-[163px]" />
            <h2 class="text-3xl max-md:max-w-full">
                Content Management System
            </h2>
            <h1 class="text-5xl font-bold max-md:max-w-full max-md:text-4xl">
                {{ profile.name }}
            </h1>
        </header>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <!-- Form Section -->
        <form @submit.prevent="submit" class="mt-6 w-full text-2xl font-medium whitespace-nowrap max-md:max-w-full" autocomplete="on">
            <!-- Email Field -->
            <div class="flex flex-col place-self-center p-3 w-full max-w-[576px] max-md:max-w-full">
                <label for="email" class="text-2xl text-type1 max-md:max-w-full">
                    Email
                </label>
                <div class="flex overflow-hidden gap-2.5 text-type2 items-center mt-2.5 w-full border-none max-md:max-w-full">
                    <input
                        id="email"
                        type="email"
                        name="email"
                        v-model="form.email"
                        class="w-full text-2xl p-5 rounded-lg placeholder:italic"
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
            <div class="flex flex-col place-self-center p-3 w-full max-w-[576px] max-md:max-w-full">
                <label for="password" class="text-2xl text-type1 max-md:max-w-full">
                    Password
                </label>
                <div class="flex overflow-hidden gap-2.5 text-type2 items-center mt-2.5 w-full border-none max-md:max-w-full">
                    <div class="relative flex items-center w-full">
                        <input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            v-model="form.password"
                            class="w-full text-2xl p-5 bg-white rounded-lg outline-none pr-12 placeholder:italic"
                            placeholder="Password"
                            required
                            autocomplete="current-password"
                        />
                        <span 
                            class="material-symbols-outlined cursor-pointer absolute right-4 select-none"
                            @click="togglePasswordVisibility"
                            style="background-color: white;"
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
                    class="overflow-hidden gap-2.5 self-center px-6 py-3 max-w-full text-2xl bg-accent2 rounded-xl w-[250px] max-md:px-5 hover:bg-type2 transition-colors disabled:opacity-25"
                    :disabled="form.processing"
                >
                    LOG IN
                </button>
                <a
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="mt-2.5 text-lg underline underline-offset-2 cursor-pointer max-md:max-w-full hover:text-gray-300 transition-colors"
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
