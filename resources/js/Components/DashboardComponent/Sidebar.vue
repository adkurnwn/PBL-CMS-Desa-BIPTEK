<script setup>
import { ref, computed, provide, onMounted, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import Scrollbar from "@/Components/DashboardComponent/Scrollbar.vue";
import MenuSection from "@/Components/DashboardComponent/MenuSection.vue";
import MenuBox from "@/Components/DashboardComponent/MenuBox.vue";
import Collapse from "@/Components/DashboardComponent/Collapse.vue";

// Use localStorage to persist the collapsed state
// Initialize with the stored value or default to false
const sidebarCollapsed = ref(false);

// Define emits
const emit = defineEmits(["collapse"]);

// Load the sidebar state when the component is mounted
onMounted(() => {
    const storedState = localStorage.getItem("sidebarCollapsed");
    if (storedState !== null) {
        sidebarCollapsed.value = storedState === "true";
    }
    // Provide the initial value and emit it to parent
    provide("sidebarCollapsed", sidebarCollapsed.value);
    emit("collapse", sidebarCollapsed.value);
});

// Toggle sidebar collapsed state and persist it
const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    // Store the state in localStorage
    localStorage.setItem("sidebarCollapsed", sidebarCollapsed.value);
    // Update the provided value
    provide("sidebarCollapsed", sidebarCollapsed.value);
    // Emit the new state to parent
    emit("collapse", sidebarCollapsed.value);
};

// Watch for changes to collapsed state and emit them
watch(sidebarCollapsed, (newValue) => {
    emit("collapse", newValue);
});

// Provide the collapsed state to child components
provide("sidebarCollapsed", sidebarCollapsed.value);

// Get the current URL path from Inertia's page object
const page = usePage();
const currentUrl = computed(() => page.url);

console.log("Current URL:", currentUrl.value);

// Get village name directly from Inertia shared data
const villageName = computed(() => {
    // Access the same way as done in Login.vue
    return page.props.profile?.name || "Desa";
});

// Get user permissions from Inertia shared data
const auth = computed(() => page.props.auth);
const hasPermission = (permission) => {
    // Always show for admin users
    if (
        auth.value.user?.roles?.some(
            (role) => role.name.toLowerCase() === "admin"
        )
    ) {
        return true;
    }
    // Check if user has the specific permission
    return auth.value.user?.permissions?.includes(permission);
};

// Check if the current URL includes a specific path segment
const urlIncludes = (segment) => {
    return currentUrl.value.includes(segment);
};

// Menu icons (moved from sections to menu items)
const dashboardIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>`;
const userIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>`;
const roleIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>`;
const profileIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>`;
const demografiIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>`;
const newsIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>`;
const galleryIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>`;
const eventIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 01-2 2z" /></svg>`;
const produkDesaIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>`;
const produkHukumIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>`;
const danaDesaIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>`;
const commentIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>`;
const feedbackIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>`;
const recoveryIcon = `<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M480-400q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0 280q-139 0-241-91.5T122-440h82q14 104 92.5 172T480-200q117 0 198.5-81.5T760-480q0-117-81.5-198.5T480-760q-69 0-129 32t-101 88h110v80H120v-240h80v94q51-64 124.5-99T480-840q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-120Z"/></svg>`;
const settingsIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>`;

// Add specific icons for comment submenu items
const newsCommentIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>`;
const documentCommentIcon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z M8 10h.01M12 10h.01M16 10h.01" /></svg>`;

// Define active state for each menu item based on URL segments
const isDashboardActive = computed(() => {
    const path = currentUrl.value;
    return (
        path === "/" ||
        path === "/dashboard" ||
        path === "/dashboard/aktivitas-terbaru" ||
        path === "/dashboard/statistik" ||
        /^\/dashboard\/\d+(\?.*)?$/.test(path) ||
        /^\/dashboard\?.*$/.test(path)
    );
});

const isUsersActive = computed(
    () => urlIncludes("/dashboard/users") || urlIncludes("/dashboard/positions")
);
const isRolesActive = computed(() => urlIncludes("/dashboard/roles"));
const isProfilesActive = computed(() => urlIncludes("/dashboard/profiles"));
const isDemografiActive = computed(() => urlIncludes("/dashboard/demografi"));
const isNewsActive = computed(() => urlIncludes("/dashboard/news"));
const isGalleriesActive = computed(() => urlIncludes("/dashboard/galleries"));
const isEventsActive = computed(() => urlIncludes("/dashboard/events"));
const isProdukDesaActive = computed(() =>
    urlIncludes("/dashboard/produk-desa")
);
const isProdukHukumActive = computed(() => {
    return (
        urlIncludes("/dashboard/produk-hukum") &&
        !urlIncludes("/dashboard/produk-hukum-comments")
    );
});
const isDanaDesaActive = computed(() => urlIncludes("/dashboard/dana-desa"));
const isCommentsActive = computed(() => urlIncludes("/dashboard/comments"));
const isProdukHukumCommentsActive = computed(() =>
    urlIncludes("/dashboard/produk-hukum-comments")
);
const isFeedbacksActive = computed(() => urlIncludes("/dashboard/feedbacks"));
const isRecoveryActive = computed(() => urlIncludes("/dashboard/recovery"));
const isSettingsActive = computed(() => urlIncludes("/dashboard/settings"));

// Compute active states for sections
const isUserSectionActive = computed(
    () => isUsersActive.value || isRolesActive.value
);

const isInfoSectionActive = computed(
    () => isProfilesActive.value || isDemografiActive.value
);

const isContentSectionActive = computed(
    () =>
        isNewsActive.value ||
        isGalleriesActive.value ||
        isEventsActive.value ||
        isProdukDesaActive.value
);

const isRegulationSectionActive = computed(
    () => isProdukHukumActive.value || isDanaDesaActive.value
);

const isInteractionSectionActive = computed(
    () =>
        isCommentsActive.value ||
        isProdukHukumCommentsActive.value ||
        isFeedbacksActive.value
);

const isConfigSectionActive = computed(() => isRecoveryActive.value);

// Import provide for passing index to menu boxes
// Menu item index counter for staggered animations
let menuItemIndex = 0;

// Helper to reset and provide index for menu items
const resetMenuIndex = () => {
    menuItemIndex = 0;
    return null;
};

const getNextMenuIndex = () => {
    provide("menuBoxIndex", menuItemIndex);
    menuItemIndex++;
    return null;
};

// Default to expanded for the comment moderation submenu
const commentModeratorExpanded = ref(true);

// Toggle function for comment moderation submenu
const toggleCommentModerator = () => {
    commentModeratorExpanded.value = !commentModeratorExpanded.value;
};
</script>

<template>
    <!-- Sidebar container with relative positioning -->
    <div class="flex">
        <!-- Main sidebar with dynamic width based on collapsed state -->
        <div
            class="h-full bg-white text-white fixed left-0 top-[60px] sm:top-[65px] md:top-[70px] xl:top-[73px] 2xl:top-[82px] 3xl:top-[88px] 4xl:top-[110px] overflow-hidden sidebar"
            :class="[
                sidebarCollapsed
                    ? 'w-[80px]'
                    : 'w-[230px] sm:w-[250px] md:w-[266px] xl:w-[266px] 2xl:w-[300px] 3xl:w-[350px] 4xl:w-[400px]',
            ]"
            :style="{
                transition: 'width 300ms ease-in-out',
            }"
        >
            <!-- Fixed Dashboard Menu at the top with 50px padding -->
            <div
                class="fixed bg-white pt-[30px] sm:pt-[35px] md:pt-[40px] xl:pt-[35px] 2xl:pt-[40px] 3xl:pt-[45px] 4xl:pt-[50px] pb-[10px] sm:pb-[12px] md:pb-[13px] xl:pb-[10px] 2xl:pb-[12px] 3xl:pb-[13px] 4xl:pb-[15px] z-10"
                :class="[
                    sidebarCollapsed
                        ? 'w-[60px] pl-[20px] sm:pl-[20px] xl:pl-[20px] 2xl:pl-[20px] 3xl:pl-[20px] 4xl:pl-[20px]'
                        : 'w-[230px] sm:w-[250px] md:w-[266px] xl:w-[266px] 2xl:w-[300px] 3xl:w-[350px] 4xl:w-[400px] pl-[12px] sm:pl-[20px] pr-[12px]',
                ]"
                :style="{
                    transition: 'width 300ms ease-in-out',
                }"
            >
                <!-- Dashboard menu stays fixed at left -->
                <div
                    class="transition-all duration-300"
                    :class="sidebarCollapsed ? '' : ''"
                    v-if="hasPermission('manage_home')"
                >
                    <MenuBox
                        :href="route('dashboard')"
                        :active="isDashboardActive"
                        :icon="dashboardIcon"
                        :collapsed="sidebarCollapsed"
                    >
                        Dashboard
                    </MenuBox>
                    {{ resetMenuIndex() }}
                </div>
            </div>

            <!-- Scrollable content with padding to account for the fixed Dashboard -->
            <Scrollbar
                :hideScrollbar="true"
                :autoHide="true"
                class="pt-[75px] sm:pt-[85px] md:pt-[95px] xl:pt-[85px] 2xl:pt-[95px] 3xl:pt-[105px] 4xl:pt-[113px] sidebar-scrollbar"
            >
                <div
                    class="pb-[100px] pl-[20px] sm:pl-[12px] xl:pl-[20px] 2xl:pl-[20px] 3xl:pl-[20px] 4xl:pl-[20px] sm:pt-[0px]"
                    :class="sidebarCollapsed ? 'pr-3 sm:pr-5' : 'pr-0'"
                >
                    <div
                        class="space-y-[10px] sm:space-y-[11px] md:space-y-[12px] xl:space-y-[13px] 2xl:space-y-[15px] 3xl:space-y-[18px] 4xl:space-y-[20px]"
                        :class="sidebarCollapsed ? '' : 'pr-[12px]'"
                    >
                        <!-- User Management Section - Only show if user has user or role permissions -->
                        <MenuSection
                            v-if="
                                hasPermission('manage_users') ||
                                hasPermission('manage_roles')
                            "
                            title="Manajemen Pengguna"
                            :active="isUserSectionActive"
                            :collapsed="sidebarCollapsed"
                        >
                            <MenuBox
                                v-if="hasPermission('manage_users')"
                                :href="route('dashboard.users.index')"
                                :active="isUsersActive"
                                :icon="userIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Pengguna
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                            <MenuBox
                                v-if="hasPermission('manage_roles')"
                                :href="route('dashboard.roles.index')"
                                :active="isRolesActive"
                                :icon="roleIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Hak Akses
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                        </MenuSection>

                        <!-- Information Section - Only show if user has profile or demografi permissions -->
                        <MenuSection
                            v-if="
                                hasPermission('manage_profiles') ||
                                hasPermission('manage_demografi')
                            "
                            title="Informasi Desa"
                            :active="isInfoSectionActive"
                            :collapsed="sidebarCollapsed"
                        >
                            <MenuBox
                                v-if="hasPermission('manage_profiles')"
                                :href="
                                    route(
                                        'dashboard.profiles.edit',
                                        villageName
                                    )
                                "
                                :active="isProfilesActive"
                                :icon="profileIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Profil Desa
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                            <MenuBox
                                v-if="hasPermission('manage_demografi')"
                                :href="route('dashboard.demografi.index')"
                                :active="isDemografiActive"
                                :icon="demografiIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Demografi
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                        </MenuSection>

                        <!-- Content Section - Only show if user has relevant permissions -->
                        <MenuSection
                            v-if="
                                hasPermission('manage_news') ||
                                hasPermission('manage_galleries') ||
                                hasPermission('manage_events') ||
                                hasPermission('manage_produk-desa')
                            "
                            title="Konten & Publikasi"
                            :active="isContentSectionActive"
                            :collapsed="sidebarCollapsed"
                        >
                            <MenuBox
                                v-if="hasPermission('manage_news')"
                                :href="route('dashboard.news.index')"
                                :active="isNewsActive"
                                :icon="newsIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Berita
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                            <MenuBox
                                v-if="hasPermission('manage_galleries')"
                                :href="route('dashboard.galleries.index')"
                                :active="isGalleriesActive"
                                :icon="galleryIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Galeri
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                            <MenuBox
                                v-if="hasPermission('manage_events')"
                                :href="route('dashboard.events.index')"
                                :active="isEventsActive"
                                :icon="eventIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Agenda
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                            <MenuBox
                                v-if="hasPermission('manage_produk-desa')"
                                :href="route('dashboard.produk-desa.index')"
                                :active="isProdukDesaActive"
                                :icon="produkDesaIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                UMKM/Produk Unggulan
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                        </MenuSection>

                        <!-- Regulation Section - Only show if user has relevant permissions -->
                        <MenuSection
                            v-if="
                                hasPermission('manage_produk-hukum') ||
                                hasPermission('manage_dana-desa')
                            "
                            title="Regulasi Desa"
                            :active="isRegulationSectionActive"
                            :collapsed="sidebarCollapsed"
                        >
                            <MenuBox
                                v-if="hasPermission('manage_produk-hukum')"
                                :href="route('dashboard.produk-hukum.index')"
                                :active="isProdukHukumActive"
                                :icon="produkHukumIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Produk Hukum
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                            <MenuBox
                                v-if="hasPermission('manage_dana-desa')"
                                :href="route('dashboard.dana-desa.index')"
                                :active="isDanaDesaActive"
                                :icon="danaDesaIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Laporan Dana Desa
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                        </MenuSection>

                        <!-- Interaction Section - Only show if user has comments or feedback permissions -->
                        <MenuSection
                            v-if="
                                hasPermission('manage_comments') ||
                                hasPermission('manage_feedbacks')
                            "
                            title="Interaksi Pengguna"
                            :active="isInteractionSectionActive"
                            :collapsed="sidebarCollapsed"
                        >
                            <!-- Comments section with permission check -->
                            <div v-if="hasPermission('manage_comments')">
                                <!-- In non-collapsed state, show the dropdown menu -->
                                <div
                                    v-if="!sidebarCollapsed"
                                    class="moderation-container"
                                >
                                    <!-- Custom comment moderation box -->
                                    <div class="moderation-container">
                                        <div
                                            class="w-full mb-[15px] rounded-[6px] sm:rounded-[6px] md:rounded-[6px] xl:rounded-[6px] 2xl:rounded-[7px] 3xl:rounded-[8px] 4xl:rounded-[10px] overflow-hidden bg-menuBG"
                                        >
                                            <!-- Header - highlight if any of the submenu items are active -->
                                            <button
                                                @click="toggleCommentModerator"
                                                class="w-full flex items-center justify-between h-[32px] sm:h-[32px] md:h-[32px] xl:h-[32px] 2xl:h-[40px] 3xl:h-[44px] 4xl:h-[48px] transition-colors duration-200"
                                                :class="{
                                                    'text-selectedText font-medium':
                                                        isCommentsActive ||
                                                        isProdukHukumCommentsActive,
                                                    'text-menuText hover:text-selectedText':
                                                        !isCommentsActive &&
                                                        !isProdukHukumCommentsActive,
                                                }"
                                            >
                                                <div class="flex items-center">
                                                    <svg
                                                        class="h-[12px] w-[12px] sm:h-[13px] sm:w-[13px] md:h-[14px] md:w-[14px] xl:h-[16px] xl:w-[16px] 2xl:h-[18px] 2xl:w-[18px] 3xl:h-[21px] 3xl:w-[21px] 4xl:h-[24px] 4xl:w-[24px] ml-[8px] sm:ml-[9px] md:ml-[10px] xl:ml-[8px] 2xl:ml-[10px] 3xl:ml-[11px] 4xl:ml-[12px] mr-[8px] sm:mr-[8px] md:mr-[9px] xl:mr-[8px] 2xl:mr-[9px] 3xl:mr-[9px] 4xl:mr-[10px]'"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        v-html="commentIcon"
                                                    ></svg>
                                                    <span
                                                        class="text-[12px] sm:text-[12px] md:text-[12px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                                                        >Moderasi Komentar</span
                                                    >
                                                </div>
                                                <svg
                                                    class="h-[12px] w-[12px] sm:h-[13px] sm:w-[13px] md:h-[14px] md:w-[14px] xl:h-[16px] xl:w-[16px] 2xl:h-[18px] 2xl:w-[18px] 3xl:h-[21px] 3xl:w-[21px] 4xl:h-[24px] 4xl:w-[24px] mr-[8px] sm:mr-[9px] md:mr-[10px] xl:mr-[8px] 2xl:mr-[10px] 3xl:mr-[11px] 4xl:mr-[12px] transform transition-transform duration-200"
                                                    :class="{
                                                        'rotate-180':
                                                            commentModeratorExpanded,
                                                    }"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>

                                            <!-- Submenu content with transition -->
                                            <div
                                                v-show="
                                                    commentModeratorExpanded
                                                "
                                                class="transition-all duration-300 ease-out overflow-hidden"
                                            >
                                                <div
                                                    class="submenu-wrapper rounded-[6px] sm:rounded-[6px] md:rounded-[6px] xl:rounded-[6px] 2xl:rounded-[7px] 3xl:rounded-[8px] 4xl:rounded-[10px] overflow-hidden"
                                                >
                                                    <!-- Berita submenu with proper MenuBox styling -->
                                                    <Link
                                                        :href="
                                                            route(
                                                                'dashboard.comments'
                                                            )
                                                        "
                                                        class="submenu-box w-full flex items-center rounded-[4px] sm:rounded-[5px] md:rounded-[6px] xl:rounded-[6px] 2xl:rounded-[7px] 3xl:rounded-[8px] 4xl:rounded-[10px] h-[32px] sm:h-[32px] md:h-[32px] xl:h-[32px] 2xl:h-[40px] 3xl:h-[44px] 4xl:h-[48px] transition-all duration-200"
                                                        :class="{
                                                            'bg-selectedBG text-selectedText border border-selectedText':
                                                                isCommentsActive,
                                                            'bg-menuBG text-menuText hover:bg-selectedBG hover:text-selectedText':
                                                                !isCommentsActive,
                                                        }"
                                                    >
                                                        <svg
                                                            class="h-[12px] w-[12px] sm:h-[13px] sm:w-[13px] md:h-[14px] md:w-[14px] xl:h-[16px] xl:w-[16px] 2xl:h-[18px] 2xl:w-[18px] 3xl:h-[21px] 3xl:w-[21px] 4xl:h-[24px] 4xl:w-[24px] ml-[8px] sm:ml-[9px] md:ml-[10px] xl:ml-[24px] 2xl:ml-[26px] 3xl:ml-[28px] 4xl:ml-[30px] mr-[8px] sm:mr-[8px] md:mr-[9px] xl:mr-[8px] 2xl:mr-[9px] 3xl:mr-[9px] 4xl:mr-[10px]"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor"
                                                            v-html="
                                                                newsCommentIcon
                                                            "
                                                        ></svg>
                                                        <span
                                                            class="truncate overflow-hidden max-w-[280px] text-[12px] sm:text-[12px] md:text-[12px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                                                            >Berita</span
                                                        >
                                                    </Link>

                                                    <!-- Produk Hukum submenu - Make sure this gets highlighted correctly -->
                                                    <Link
                                                        :href="
                                                            route(
                                                                'dashboard.produk-hukum.index-comments'
                                                            )
                                                        "
                                                        class="submenu-box w-full flex items-center rounded-[4px] sm:rounded-[5px] md:rounded-[6px] xl:rounded-[6px] 2xl:rounded-[7px] 3xl:rounded-[8px] 4xl:rounded-[10px] h-[32px] sm:h-[32px] md:h-[32px] xl:h-[32px] 2xl:h-[40px] 3xl:h-[44px] 4xl:h-[48px] transition-all duration-200"
                                                        :class="{
                                                            'bg-selectedBG text-selectedText border border-selectedText':
                                                                isProdukHukumCommentsActive,
                                                            'bg-menuBG text-menuText hover:bg-selectedBG hover:text-selectedText':
                                                                !isProdukHukumCommentsActive,
                                                        }"
                                                    >
                                                        <svg
                                                            class="h-[12px] w-[12px] sm:h-[13px] sm:w-[13px] md:h-[14px] md:w-[14px] xl:h-[16px] xl:w-[16px] 2xl:h-[18px] 2xl:w-[18px] 3xl:h-[21px] 3xl:w-[21px] 4xl:h-[24px] 4xl:w-[24px] ml-[8px] sm:ml-[9px] md:ml-[10px] xl:ml-[24px] 2xl:ml-[26px] 3xl:ml-[28px] 4xl:ml-[30px] mr-[8px] sm:mr-[8px] md:mr-[9px] xl:mr-[8px] 2xl:mr-[9px] 3xl:mr-[9px] 4xl:mr-[10px]"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor"
                                                            v-html="
                                                                documentCommentIcon
                                                            "
                                                        ></svg>
                                                        <span
                                                            class="truncate overflow-hidden max-w-[280px] text-[12px] sm:text-[12px] md:text-[12px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                                                            >Produk Hukum</span
                                                        >
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- In collapsed state, show individual comment menu items -->
                                <template v-if="sidebarCollapsed">
                                    <MenuBox
                                        :href="route('dashboard.comments')"
                                        :active="isCommentsActive"
                                        :icon="newsCommentIcon"
                                        :collapsed="sidebarCollapsed"
                                    >
                                        Berita
                                    </MenuBox>
                                    {{ getNextMenuIndex() }}
                                    <MenuBox
                                        :href="
                                            route(
                                                'dashboard.produk-hukum.index-comments'
                                            )
                                        "
                                        :active="isProdukHukumCommentsActive"
                                        :icon="documentCommentIcon"
                                        :collapsed="sidebarCollapsed"
                                    >
                                        Produk Hukum
                                    </MenuBox>
                                    {{ getNextMenuIndex() }}
                                </template>
                            </div>

                            <!-- Normal MenuBox for Feedback with permission check -->
                            <MenuBox
                                v-if="hasPermission('manage_feedbacks')"
                                :href="route('dashboard.feedbacks.index')"
                                :active="isFeedbacksActive"
                                :icon="feedbackIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Saran & Keluhan
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                        </MenuSection>

                        <!-- System & Configuration Section -->
                        <MenuSection
                            v-if="hasPermission('manage_recovery')"
                            title="Sistem & Konfigurasi"
                            :active="isConfigSectionActive"
                            :collapsed="sidebarCollapsed"
                        >
                            <MenuBox
                                v-if="hasPermission('manage_recovery')"
                                :href="route('dashboard.recovery.index')"
                                :active="isRecoveryActive"
                                :icon="recoveryIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Pemulihan Data
                            </MenuBox>
                            {{ getNextMenuIndex() }}

                            <MenuBox
                                v-if="hasPermission('manage_settings')"
                                :href="route('dashboard.settings.edit')"
                                :active="isSettingsActive"
                                :icon="settingsIcon"
                                :collapsed="sidebarCollapsed"
                            >
                                Pengaturan
                            </MenuBox>
                            {{ getNextMenuIndex() }}
                        </MenuSection>
                    </div>
                </div>
            </Scrollbar>
        </div>

        <!-- Collapse button positioned outside the sidebar -->
        <div
            class="fixed z-20 top-[90px] sm:top-[100px] md:top-[110px] xl:top-[108px] 2xl:top-[122px] 3xl:top-[133px] 4xl:top-[160px]"
            :class="[
                sidebarCollapsed
                    ? 'left-[64px] xl:left-[64px] 2xl:left-[72px] 3xl:left-[76px] 4xl:left-[80px]'
                    : 'left-[230px] sm:left-[250px] md:left-[266px] xl:left-[266px] 2xl:left-[300px] 3xl:left-[350px] 4xl:left-[400px]',
            ]"
            :style="{
                transition: 'left 300ms ease-in-out',
            }"
        >
            <Collapse :collapsed="sidebarCollapsed" @click="toggleSidebar" />
        </div>
    </div>
</template>

<style>
/* Hide default scrollbars but keep functionality */
:global(.sidebar-scrollbar) {
    scrollbar-width: none !important; /* Firefox */
    -ms-overflow-style: none !important; /* IE and Edge */
}

:global(.sidebar-scrollbar::-webkit-scrollbar),
:global(.sidebar-scrollbar .custom-scrollbar::-webkit-scrollbar) {
    width: 0 !important;
    height: 0 !important;
    display: none !important;
}

/* Ensure consistent padding for all menu sections */
:global(.menu-section) {
    padding-right: 0 !important;
}

/* Ensure consistent spacing in nested sections */
.nested-section .menu-content > div {
    padding-left: 24px !important;
}

/* Fix padding and margin for better alignment */
.sidebar > div > div {
    padding-right: 0 !important;
}

/* Ensure proper width calculations for consistent right padding */
.sidebar .space-y-\[20px\] {
    width: 100%;
    padding-right: 12px;
}

/* Custom styles for the moderation container */
.moderation-container {
    margin-bottom: 15px;
}

/* Improved styles for Moderation Komentar submenu */
.moderation-container .submenu-wrapper {
    background-color: transparent;
}

/* Add styles for fixed dashboard */
.sidebar .pt-\[113px\] {
    padding-top: 113px !important;
}

/* Remove transform origin and custom transition delays that were causing issues */
.sidebar,
.sidebar > div {
    will-change: width, left;
}

/* Fix for MenuBox icon positioning */
.menu-box svg {
    flex-shrink: 0;
}

/* Animation styles for text */
.menu-box > span {
    transition: opacity 200ms ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Simple transition for sidebar width */
.sidebar {
    transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* Reset any interfering styles */
.left-align-transition {
    transition-delay: 0ms !important;
}

/* Fix the icon positioning to keep them at the left side during collapse */
.menu-box {
    transform-origin: left center;
    position: relative;
}

/* Ensure text doesn't wrap */
.menu-box > span {
    white-space: nowrap;
}

/* Fix icon positioning to stay at left */
.menu-box svg {
    flex-shrink: 0;
    position: relative;
    left: 0;
}
</style>
