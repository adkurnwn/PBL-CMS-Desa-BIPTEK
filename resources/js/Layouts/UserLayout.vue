<script setup>
import Feedback from "@/Components/Feedback.vue";
import Navbar from "../Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import { ref, onMounted, provide } from "vue";
import { useApiCache } from "@/Composables/UseApiCache";

const layout = ref({});
const loading = ref(true);

const fetchData = async () => {
    const layoutApi = useApiCache("layoutData", "/api/profile/layout");
    try {
        const response = await layoutApi.fetchWithCache();
        layout.value = response;
    } catch (error) {
        console.error("Error fetching layout data:", error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchData);
provide("layout", layout);
</script>

<template>
    <div>
        <main class="bg-type1 min-h-screen">
            <!-- Navbar -->
            <div class="fixed top-0 left-0 w-full z-50">
                <Navbar :layout="layout" />
            </div>

            <!-- Page Content -->
            <slot />

            <Feedback />
            <Footer :layout="layout" />
        </main>
    </div>
</template>
