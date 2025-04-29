<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    profile: {
        type: Object,
    },
});
</script>

<template>
    <div
        class="flex h-screen flex-col items-center bg-type1 pt-6 sm:justify-center sm:pt-0"
    >
        <!-- <div class="absolute top-0 left-0 right-0 flex h-screen w-full items-center justify-center bg-accent1 z-10 ">
            <img src="../../images/profile.jpg" class="h-full w-full object-cover z-10" />
        </div>
        <div class="absolute top-0 left-0 right-0 flex h-screen w-full items-center justify-center bg-black opacity-50 z-20">

        </div> -->
        <div class="z-20">
            <Link href="/">
                <ApplicationLogo
                    :logo="profile.logo"
                    class="h-24 w-20 fill-current text-gray-500"
                />
            </Link>
        </div>
        <div class="z-20 text-center my-6">
            <h1 class="ca font-bold text-2xl sm:text-3xl lg:text-4xl">
                Sistem Informasi {{ profile.name }}
            </h1>
        </div>
        <div
            class="mt-6 w-full overflow-hidden bg-slate-100 px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg z-50"
        >
            <slot />
        </div>
    </div>
</template>
