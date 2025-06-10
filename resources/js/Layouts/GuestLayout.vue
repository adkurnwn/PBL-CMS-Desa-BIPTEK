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
    <div class="min-h-screen bg-accent1" :style="{ '--accent1': primaryColor, '--accent2': secondaryColor }">
        <main class="flex flex-col items-center justify-center min-h-screen">
            <slot></slot>
            <div class="bg-accent1 absolute bottom-2">
                <p
                    class=" text-white font-thin text-xs md:text-sm text-center">
                    © {{ new Date().getFullYear() }} Powered by PT BIPTEK Tokodata Indonesia
                </p>
            </div>
        </main>

    </div>
</template>
