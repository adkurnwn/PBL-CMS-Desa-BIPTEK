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
        <div class="fixed top-[120px] right-4 z-50 w-[26rem] space-y-2">
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
        
        <div class="mt-[160px] flex flex-row h-[calc(100vh-110px)] overflow-hidden">
            <!-- Sidebar with collapse functionality -->
            <Sidebar @toggle="toggleSidebar" @collapse="handleSidebarCollapse">
                <template #logo>
                    <slot name="logo">
                        <ApplicationLogo class="h-12 w-12" />
                    </slot>
                </template>
            </Sidebar>

            <!-- Main Content with Custom Scrollbar -->
            <div 
                class="flex-1 flex flex-col overflow-hidden transition-all duration-300 relative pl-[29px] justify-start w-full"
                :style="{
                    marginLeft: sidebarCollapsed ? '80px' : '400px'
                }"
            >
                <!-- Page Heading - Fixed position relative to parent -->
                <div class="bg-white w-full" v-if="$slots.header">
                    <div class="max-w-full mx-auto pl-[10px]">
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

                <!-- Keep the Flash Messages section but hide it visually -->
                <!-- This maintains backward compatibility with pages that expect these elements -->
                <div class="w-full px-4 hidden">
                    <div
                        v-if="$page.props.flash?.success"
                        class="mb-4 p-4 bg-green-100 text-green-700 rounded-md"
                    >
                        {{ $page.props.flash.success }}
                    </div>
                    <div
                        v-if="$page.props.flash?.error"
                        class="mb-4 p-4 bg-red-100 text-red-700 rounded-md"
                    >
                        {{ $page.props.flash.error }}
                    </div>
                    <div
                        v-if="$page.props.flash?.warning"
                        class="mb-4 p-4 bg-yellow-100 text-yellow-700 rounded-md"
                    >
                        {{ $page.props.flash.warning }}
                    </div>
                    <div
                        v-if="$page.props.flash?.info"
                        class="mb-4 p-4 bg-blue-100 text-blue-700 rounded-md"
                    >
                        {{ $page.props.flash.info }}
                    </div>
                </div>

                <div v-if="$slots['tab-devider']" class="h-[48px] pt-[11px]">
                    <slot name="tab-devider"></slot>
                </div>

                <div v-if="$slots['subtitle-area']" class="px-[22px] pt-[25px] text-[22px] font-medium text-menuText">
                    <slot name="subtitle-area"></slot>
                </div>

                <!-- Replace blank space with a named slot for search functionality - Only display if content exists -->
                <div v-if="$slots['search-area']" class="h-[94px] pr-[23px] pl-[10px] flex items-center gap-[10px] justify-end">
                    <slot name="search-area"></slot>
                </div>

                <!-- Page Content with Custom Scrollbar - Only this part scrolls -->
                <Scrollbar 
                    class="flex-1 w-full pr-[23px] pl-[10px]" 
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

    <!-- Mobile Menu -->
    <div v-show="showingNavigationDropdown" class="fixed inset-0 z-40 lg:hidden">
        <div class="fixed inset-0 bg-black bg-opacity-25" @click="showingNavigationDropdown = false"></div>
        
        <div class="fixed inset-y-0 left-0 flex flex-col z-50 w-64 bg-gray-800 overflow-y-auto">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <ApplicationLogo class="h-8 w-auto fill-current text-white" />
                    </Link>
                    <button @click="showingNavigationDropdown = false" class="text-gray-300 hover:text-white">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                
                <div class="mt-5 space-y-1">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        Dashboard
                    </ResponsiveNavLink>

                    <div class="pt-4 pb-1 border-t border-gray-600">
                        <div class="px-4">
                            <div class="text-base font-medium text-white">{{ $page.props.auth.user.name }}</div>
                            <div class="text-sm font-medium text-gray-400">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
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
</style>
