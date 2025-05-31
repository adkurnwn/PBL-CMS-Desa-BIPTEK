<script setup>
import ApplicationLogo from "@/Components/DashboardComponent/ApplicationLogo.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    profile: {
        type: Object,
    },
});

const page = usePage();

// Get primary color from props or shared data
const primaryColor = computed(() => {
    return props.profile?.primary_color || page.props.profile?.primary_color || '#244031';
});

const secondaryColor = computed(() => {
    return props.profile?.secondary_color || page.props.profile?.secondary_color || '#3B614A';
});
</script>

<template>
    <div 
        class="min-h-screen bg-accent1"
        :style="{ '--accent1': primaryColor, '--accent2': secondaryColor }"
    >
        <main
            class="flex flex-col justify-center items-center min-h-screen">
            <slot></slot>
        </main>
    </div>
</template>
