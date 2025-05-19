<script setup>
import ApplicationLogo from "@/Components/DashboardComponent/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    profile: {
        type: Object,
    },
});
</script>

<template>
    <div class="min-h-screen bg-accent1">
        <main
            class="flex flex-col justify-center items-center px-20 py-60 min-h-screen max-md:px-5 max-md:py-24">
            <slot></slot>
        </main>
    </div>
</template>
