<script setup>
import Dropdown from "@/Components/DashboardComponent/Dropdown.vue";
import DropdownLink from "@/Components/DashboardComponent/DropdownLink.vue";
import ApplicationLogo from "@/Components/DashboardComponent/ApplicationLogo.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

defineProps({
    sidebarOpen: {
        type: Boolean,
        default: true
    }
});

defineEmits(['toggle']);

const page = usePage();

// Get village name directly from Inertia shared data
const villageName = computed(() => {
    // Access the same way as done in Login.vue
    return page.props.profile?.name || 'Desa';
});

// Custom dropdown styles to match header
const dropdownStyles = {
    width: '322px',
    backgroundColor: 'white',
    borderRadius: '10px',
    overflow: 'hidden',
    boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)'
};
</script>

<template>
    <header class="bg-accent1 shadow h-[60px] sm:h-[65px] md:h-[70px] xl:h-[74px] 2xl:h-[82px] 3xl:h-[88px] 4xl:h-[110px] text-white flex items-center fixed top-0 left-0 right-0 z-20">
        <div class="flex justify-between items-center py-[12px] md:py-[15px] px-3 sm:px-4 md:px-[19px] w-full">
            <!-- Left Side -->
            <div class="flex items-center">

                <!-- Logo -->
                <div class="flex items-center">
                    <ApplicationLogo
                        :logo="page.props.profile?.logo"
                        class="h-[35px] sm:h-[40px] md:h-[45px] xl:h-[50px] 2xl:h-[55px] 3xl:h-[60px] 4xl:h-[76px] w-auto fill-current text-white"
                    />

                    <!-- Display CMS title and village name -->
                    <div class="ml-[6px] sm:ml-[8px] md:ml-[10px] flex flex-col w-[180px] sm:w-[220px] md:w-[260px] xl:w-[310px] h-auto justify-center">
                        <div class="text-[16px] sm:text-[18px] md:text-[20px] xl:text-[14.67px] 2xl:text-[16.5px] 3xl:text-[17.6px] 4xl:text-[22px] font-normal">
                            <span class="hidden max-md:inline">CMS</span>
                            <span class="inline max-md:hidden">Content Management System</span>
                        </div>
                        <div class="font-bold text-[18px] sm:text-[22px] md:text-[25px] xl:text-[20px] 2xl:text-[22px] 3xl:text-[24px] 4xl:text-[31px] leading-tight">{{ villageName }}</div>
                    </div>
                </div>
            </div>

            <!-- Right Side - User Profile Dropdown -->
            <div class="flex items-center">
                <Dropdown
                    align="right"
                    width="auto"
                    contentClasses="bg-white w-auto sm:w-[180px] md:w-[200px] xl:w-[214.67px] 2xl:w-[250px] 3xl:w-[280px] 4xl:w-[322px]"
                >
                    <template #trigger>
                        <button class="flex items-center font-medium text-white hover:text-gray-100 focus:outline-none transition duration-150 ease-in-out w-auto sm:w-[180px] md:w-[200px] xl:w-[214.67px] 2xl:w-[250px] 3xl:w-[280px] 4xl:w-[322px] pl-2 sm:pl-3 sm:px-3 justify-end">
                            <div class="flex items-center w-full">
                                <!-- User Avatar - Show profile picture if available, otherwise show initials -->
                                <div class="relative sm:mr-[6px] md:mr-[8px] xl:mr-[8px] 2xl:mr-[9px] 3xl:mr-[9px] 4xl:mr-[10px]">
                                    <div v-if="$page.props.auth.user.photo_profile"
                                        class="h-[25px] sm:h-[27px] md:h-[29px] xl:h-[30px] 2xl:h-[34px] 3xl:h-[38px] 4xl:h-[45px] w-[25px] sm:w-[27px] md:w-[29px] xl:w-[30px] 2xl:w-[34px] 3xl:w-[38px] 4xl:w-[45px] rounded-full overflow-hidden border-2 border-white">
                                        <img :src="'/storage/' + $page.props.auth.user.photo_profile"
                                            :alt="$page.props.auth.user.name"
                                            class="h-full w-full object-cover object-top" />
                                    </div>
                                    <div v-else
                                        class="h-[25px] sm:h-[27px] md:h-[29px] xl:h-[30px] 2xl:h-[34px] 3xl:h-[38px] 4xl:h-[45px] w-[25px] sm:w-[27px] md:w-[29px] xl:w-[30px] 2xl:w-[34px] 3xl:w-[38px] 4xl:w-[45px] rounded-full bg-white text-accent1 flex items-center justify-center border-2 border-white font-medium">
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                </div>

                                <!-- User info - Hidden on small screens -->
                                <div class="hidden sm:flex flex-col items-start flex-1 overflow-hidden">
                                    <div class="truncate max-w-[180px] md:max-w-[200px] xl:max-w-[220px] text-[14px] md:text-[16px] xl:text-[14px] 2xl:text-[16px] 3xl:text-[17px] 4xl:text-[18px]">{{ $page.props.auth.user.name }}</div>
                                    <div class="opacity-80 truncate max-w-[180px] md:max-w-[200px] xl:max-w-[220px] text-[12px] md:text-[13px] xl:text-[11px] 2xl:text-[13px] 3xl:text-[14px] 4xl:text-[15px]">
                                        <!-- Display role name if available -->
                                        <span v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.length > 0">
                                            {{ $page.props.auth.user.roles[0].name }}
                                        </span>
                                        <span v-else-if="$page.props.auth.user.position">
                                            {{ $page.props.auth.user.position.name }}
                                        </span>
                                        <span v-else>
                                            User
                                        </span>
                                    </div>
                                </div>

                                <!-- Dropdown indicator - Hidden on small screens -->
                                <svg class="hidden sm:block flex-shrink-0 fill-current h-3 md:h-3.5 xl:h-3 2xl:h-3.5 3xl:h-3.5 4xl:h-4 w-3 md:w-3.5 xl:w-3 2xl:w-3.5 3xl:w-3.5 4xl:w-4 ml-1 md:ml-1.5 xl:ml-1 2xl:ml-1.5 3xl:ml-1.5 4xl:ml-2"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </template>

                    <template #content>
                        <!-- User info for small screens -->
                        <div class="block sm:hidden px-4 py-3 border-b border-gray-200">
                            <div class="font-medium text-[14px] sm:text-[15px] md:text-[16px] text-gray-800">{{ $page.props.auth.user.name }}</div>
                            <div class="text-[12px] sm:text-[13px] md:text-[14px] text-gray-500">
                                <span v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.length > 0">
                                    {{ $page.props.auth.user.roles[0].name }}
                                </span>
                                <span v-else-if="$page.props.auth.user.position">
                                    {{ $page.props.auth.user.position.name }}
                                </span>
                                <span v-else>
                                    User
                                </span>
                            </div>
                        </div>

                        <!-- Profile Link -->
                        <DropdownLink
                            :href="route('profile.edit')"
                            :active="route().current('profile.edit')"
                        >
                            <div class="flex items-center">
                                <svg class="h-[18px] sm:h-[20px] md:h-[22px] xl:h-[20px] 2xl:h-[22px] 3xl:h-[23px] 4xl:h-[24px] w-[18px] sm:w-[20px] md:w-[22px] xl:w-[20px] 2xl:w-[22px] 3xl:w-[23px] 4xl:w-[24px] mr-[8px] xl:mr-[8px] 2xl:mr-[9px] 3xl:mr-[9px] 4xl:mr-[10px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-[14px] xl:text-[14px] 2xl:text-[15px] 3xl:text-[15px] 4xl:text-[16px]">Profile</span>
                            </div>
                        </DropdownLink>

                        <!-- Logout Link -->
                        <DropdownLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full text-left"
                            danger
                        >
                            <div class="flex items-center">
                                <svg class="h-[18px] sm:h-[20px] md:h-[22px] xl:h-[20px] 2xl:h-[22px] 3xl:h-[23px] 4xl:h-[24px] w-[18px] sm:w-[20px] md:w-[22px] xl:w-[20px] 2xl:w-[22px] 3xl:w-[23px] 4xl:w-[24px] mr-[8px] xl:mr-[8px] 2xl:mr-[9px] 3xl:mr-[9px] 4xl:mr-[10px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="text-[14px] xl:text-[14px] 2xl:text-[15px] 3xl:text-[15px] 4xl:text-[16px]">Log Out</span>
                            </div>
                        </DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </div>
    </header>
</template>

<style scoped>
</style>
