<script setup>
import Feedback from "@/Components/Feedback.vue";
import Navbar from "../Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import ToastNotification from "@/Components/DashboardComponent/ToastNotification.vue";
import { ref, onMounted, provide, computed } from "vue";
import { useApiCache } from "@/Utility/useApiCache";
import { usePage } from "@inertiajs/vue3";

const layout = ref({});
const page = usePage();

// Toast state and function
const toast = ref({
    show: false,
    message: "",
    type: "success",
});
const showToast = (message, type = "success") => {
    toast.value = { show: true, message, type };
};
provide("showToast", showToast);

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
    <div :style="{ '--accent1': primaryColor, '--accent2': secondaryColor }" id="app-accent-root">
        
        <div class="fixed top-20 sm:top-24 md:top-28 right-2 sm:right-4 z-50 flex flex-col items-end w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl space-y-2 px-2">
            <ToastNotification
            v-if="toast.show"
            :type="toast.type"
            :message="toast.message"
            :show="toast.show"
            @close="toast.show = false"
        />
        </div>
        <main class="bg-type1 min-h-screen">
            <!-- Navbar -->
            <div class="fixed top-0 left-0 w-full z-40">
                <Navbar :layout="layout" />
            </div>
            <!-- Page Content -->
            <slot :layout="layout"></slot>
            <Feedback />
            <Footer :layout="layout" />
        </main>
    </div>
</template>