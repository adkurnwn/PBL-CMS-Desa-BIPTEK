<script setup>
import Feedback from "@/Components/Feedback.vue";
import Navbar from "../Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import { ref, onMounted, provide, computed } from "vue";
import { useApiCache } from "@/Utility/useApiCache";
import { usePage } from "@inertiajs/vue3";

const layout = ref({});
const page = usePage();

// Get primary color from shared data or layout data
const primaryColor = computed(() => {
    return page.props.profile?.primary_color || layout.value?.primary_color || '#244031';
});

const secondaryColor = computed(() => {
    return page.props.profile?.secondary_color || layout.value?.secondary_color || '#3B614A';
});

const fetchData = async () => {
    const layoutApi = useApiCache("layoutData", "/api/profile/layout");
    await layoutApi.fetchWithCache();
    layout.value = layoutApi.data.value || {};
};

onMounted(fetchData);
provide("layout", layout);
</script>

<template>
    <div :style="{ '--accent1': primaryColor, '--accent2': secondaryColor }">
        <main class="bg-type1 min-h-screen">
            <!-- Navbar -->
            <div class="fixed top-0 left-0 w-full z-50">
                <Navbar :layout="layout" />
            </div>

            <!-- Page Content -->
            <slot :layout="layout"></slot>

            <Feedback />
            <Footer :layout="layout" />
        </main>
    </div>
</template>
