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

            <div
                class="mb-4 italic text-sm text-stone-100 text-center max-w-md mt-6">
                Lupa kata sandi? Masukkan alamat email Anda di bawah ini, dan kami akan mengirimkan tautan untuk
                mengatur ulang kata sandi Anda.
            </div>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div>

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
                            placeholder="email@contoh.com" required autofocus autocomplete="username" />
                    </div>
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex flex-row gap-3 mt-6 p-[4px] w-full font-medium text-white">
                    <button type="button"
                        class="overflow-hidden gap-2.5 self-center rounded-lg w-full py-2 bg-accent2 hover:opacity-40 transition-colors duration-300"
                        @click="$inertia.visit(route('login'))">
                        LOGIN
                    </button>
                    <button type="submit"
                        class="overflow-hidden gap-2.5 self-center rounded-lg w-full py-2 bg-accent2 hover:opacity-40 transition-colors duration-300 disabled:opacity-25"
                        :disabled="form.processing">
                        RESET PASSWORD
                    </button>
                </div>
            </form>

            <div v-if="$page.props.flash?.error" class="mt-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ $page.props.flash.error }}
            </div>
        </div>
    </GuestLayout>
</template>
