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
            class="flex flex-col justify-center items-center min-h-screen">
            <slot></slot>
        </main>
    </div>
</template>
