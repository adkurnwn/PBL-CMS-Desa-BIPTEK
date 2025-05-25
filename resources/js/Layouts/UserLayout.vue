<script setup>
import Feedback from "@/Components/Feedback.vue";
import Navbar from "../Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import { ref, onMounted, onBeforeUnmount, provide } from "vue";
import axios from "axios";

const layout = ref({});
const loading = ref(true);

const fetchProfile = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/api/profile/layout");
        layout.value = response.data.data;
        loading.value = false;
    } catch (error) {
        console.error("Error fetching layout data:", error);
        loading.value = false;
    }
};

onMounted(() => {
    fetchProfile();
});

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
