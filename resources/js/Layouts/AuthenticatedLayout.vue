<script setup>
import { ref, watch, computed, onMounted } from "vue";
import ApplicationLogo from "@/Components/DashboardComponent/ApplicationLogo.vue";
import NavLink from "@/Components/DashboardComponent/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Sidebar from "@/Components/DashboardComponent/Sidebar.vue";
import Header from "@/Components/DashboardComponent/Header.vue";
import Scrollbar from "@/Components/DashboardComponent/Scrollbar.vue";
import ToastNotification from "@/Components/DashboardComponent/ToastNotification.vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();
const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(true);
const sidebarCollapsed = ref(false);

// Toast notifications state
const toasts = ref([]);
const toastId = ref(0);

// Get sidebar collapsed state from localStorage on mount
onMounted(() => {
    const storedCollapsedState = localStorage.getItem('sidebarCollapsed');
    if (storedCollapsedState !== null) {
        sidebarCollapsed.value = storedCollapsedState === 'true';
    }
    
    checkForFlashMessages();
});

// Watch for changes to flash messages - important for SPA navigation
watch(() => page.props.flash, (newVal, oldVal) => {
    if (newVal && JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
        checkForFlashMessages();
    }
}, { deep: true });

// Check for flash messages and convert them to toasts
const checkForFlashMessages = () => {
    if (page.props.flash) {
        Object.entries(page.props.flash).forEach(([type, message]) => {
            if (message && ['success', 'error', 'warning', 'info'].includes(type)) {
                addToast(message, type);
            }
        });
    }
};

// Add a new toast notification
const addToast = (message, type = 'info', duration = 5000) => {
    const id = toastId.value++;
    toasts.value.push({ id, message, type, duration, show: true });
    
    // Auto-remove toast from array after it's closed (plus animation time)
    if (duration > 0) {
        setTimeout(() => {
            removeToast(id);
        }, duration + 500); // 500ms total for the animations
    }
};

// Remove a toast when closed
const removeToast = (id) => {
    // First set show to false to trigger the leave animation
    const toastIndex = toasts.value.findIndex(toast => toast.id === id);
    if (toastIndex !== -1) {
        toasts.value[toastIndex].show = false;
        
        // Then remove from array after animation completes
        setTimeout(() => {
            toasts.value = toasts.value.filter(toast => toast.id !== id);
        }, 300); // 300ms for the fade out animation
    }
};

// Calculate sidebar width based on collapsed state
const sidebarWidth = computed(() => {
    return sidebarCollapsed.value ? '80px' : '400px';
});

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

// This function will be called from the Sidebar component
const handleSidebarCollapse = (collapsed) => {
    sidebarCollapsed.value = collapsed;
};
</script>

<template>
    <div class="flex flex-col h-screen bg-white overflow-hidden">
        <!-- Header -->
        <Header :sidebarOpen="sidebarOpen" @toggle="toggleSidebar" />
        
        <!-- Toast Notifications Container - Positioned for SPA -->
        <div class="fixed top-[120px] inset-x-0 sm:top-[120px] sm:right-4 sm:inset-x-auto z-50 flex flex-col items-center sm:items-end max-w-full sm:w-[26rem] space-y-2 px-0">
            <ToastNotification
                v-for="toast in toasts"
                :key="toast.id"
                :type="toast.type"
                :message="toast.message"
                :duration="toast.duration"
                :show="toast.show"
                @close="removeToast(toast.id)"
            />
        </div>
        
        <div class="mt-[70px] sm:mt-[80px] md:mt-[90px] xl:mt-[107px] 2xl:mt-[120px] 3xl:mt-[140px] 4xl:mt-[160px] flex flex-row h-[calc(100vh-70px)] sm:h-[calc(100vh-80px)] md:h-[calc(100vh-90px)] xl:h-[calc(100vh-107px)] 2xl:h-[calc(100vh-120px)] 3xl:h-[calc(100vh-140px)] 4xl:h-[calc(100vh-160px)] overflow-hidden">
            <!-- Sidebar with collapse functionality -->
            <Sidebar @toggle="toggleSidebar" @collapse="handleSidebarCollapse">
            </Sidebar>

            <!-- Main Content with Custom Scrollbar -->
            <div 
                class="flex-1 flex flex-col overflow-hidden transition-all duration-300 relative pl-[22px] 4xl:pl-[29px] justify-start w-full"
                :class="
                    sidebarCollapsed 
                    ? 'ml-[64px] xl:ml-[64px] 2xl:ml-[72px] 3xl:ml-[76px] 4xl:ml-[80px] w-[calc(100%-64px)] xl:w-[calc(100%-64px)] 2xl:w-[calc(100%-72px)] 3xl:w-[calc(100%-76px)] 4xl:w-[calc(100%-80px)]' 
                    : 'ml-[230px] sm:ml-[250px] md:ml-[266px] xl:ml-[266px] 2xl:ml-[300px] 3xl:ml-[350px] 4xl:ml-[400px] w-[calc(100%-230px)] sm:w-[calc(100%-250px)] md:w-[calc(100%-266px)] xl:w-[calc(100%-266px)] 2xl:w-[calc(100%-300px)] 3xl:w-[calc(100%-350px)] 4xl:w-[calc(100%-400px)]'
                "
            >
                <!-- Page Heading - Fixed position relative to parent -->
                <div class="bg-white w-full" v-if="$slots.header">
                    <div class="max-w-full mx-auto pl-[4px] sm:pl-[5px] md:pl-[6px] xl:pl-[6px] 2xl:pl-[7px] 3xl:pl-[8px] 4xl:pl-[10px]">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <slot name="page-icon"></slot>
                                <slot name="header"></slot>
                            </div>
                            <div v-if="$slots['header-actions']">
                                <slot name="header-actions"></slot>
                            </div>
                        </div>
                    </div>
                </div>

                

                <div v-if="$slots['tab-devider']" class=" pt-[7px] sm:pt-[8px] md:pt-[9px] xl:pt-[7px] 2xl:pt-[8px] 3xl:pt-[10px] 4xl:pt-[11px]">
                    <slot name="tab-devider"></slot>
                </div>

                <div v-if="$slots['subtitle-area']" class="px-[12px] sm:px-[15px] md:px-[18px] xl:px-[14px] 2xl:px-[18px] 3xl:px-[20px] 4xl:px-[22px] pt-[6.7px] sm:pt-[7.5px] md:pt-[8.3px] lg:pt-[9.2px] xl:pt-[10px] 2xl:pt-[11.7px] 3xl:pt-[13.3px] 4xl:pt-[15px] text-[9.3px] sm:text-[10.6px] md:text-[12px] lg:text-[13.3px] xl:text-[14.6px] 2xl:text-[16.7px] 3xl:text-[19.4px] 4xl:text-[22px] font-medium text-menuText">
                    <slot name="subtitle-area"></slot>
                </div>

                <div v-if="$slots['search-area']" class=" pt-[2px] pb-[13px] border-MenuBG shadow-sm border-b sm:pb-[14px] md:pb-[15px] lg:pb-[16px] xl:pb-[16.67px] 2xl:pb-[19px] 3xl:pb-[22px] 4xl:pb-[25px] pr-[15px] sm:pr-[18px] md:pr-[20px] xl:pr-[15px] 2xl:pr-[18px] 3xl:pr-[20px] 4xl:pr-[23px] pl-[4px] sm:pl-[5px] md:pl-[6px] xl:pl-[6px] 2xl:pl-[7px] 3xl:pl-[8px] 4xl:pl-[10px] flex items-center gap-[6px] sm:gap-[7px] md:gap-[8px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px] justify-end">
                    <slot name="search-area"></slot>
                </div>

                <div v-if="$slots['table-content']" class="flex-1 w-full pr-[15px] sm:pr-[18px] md:pr-[20px] xl:pr-[15px] 2xl:pr-[18px] 3xl:pr-[20px] 4xl:pr-[23px] pl-[4px] sm:pl-[5px] md:pl-[6px] xl:pl-[6px] 2xl:pl-[7px] 3xl:pl-[8px] 4xl:pl-[10px] table-container-slot">
                    <slot name="table-content"></slot>
                </div>
                 
                <Scrollbar 
                    v-if="!$slots['table-content']"
                    class="flex-1 w-full pr-[15px] sm:pr-[18px] md:pr-[20px] xl:pr-[15px] 2xl:pr-[18px] 3xl:pr-[20px] 4xl:pr-[23px] pl-[4px] sm:pl-[5px] md:pl-[6px] xl:pl-[6px] 2xl:pl-[7px] 3xl:pl-[8px] 4xl:pl-[10px]" 
                    :hideScrollbar="false"
                    :autoHide="false"
                >
                    <main class="w-full">
                        <slot />
                    </main>
                </Scrollbar>
            </div>
        </div>
    </div>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.3s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Hide browser scrollbar without affecting layout */
:deep(.app-container) {
    overflow: hidden;
}

/* Target specifically the html/body scrollbars without affecting sidebar */
:deep(html::-webkit-scrollbar),
:deep(body::-webkit-scrollbar) {
    width: 0;
    height: 0;
    display: none;
}

:deep(html),
:deep(body) {
    overflow: hidden;
    scrollbar-width: none;  /* Firefox */
    -ms-overflow-style: none;  /* IE and Edge */
}

/* Don't apply these styles to the sidebar */
:deep(*:not(.sidebar)::-webkit-scrollbar) {
    display: none;
}

/* Add special handling for dashboard page */
:deep(.dashboard-page) {
    overflow: visible !important;
}

/* Special container for table content to ensure proper positioning */
.table-container-slot {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 254px); /* Adjust for header height and search area */
    min-height: 400px;
    overflow: hidden;
}

/* Responsive adjustments for different screen sizes */
@media (max-height: 800px) {
    .table-container-slot {
        height: calc(100vh - 220px);
    }
}

@media (max-height: 600px) {
    .table-container-slot {
        height: calc(100vh - 190px);
    }
}
</style>
