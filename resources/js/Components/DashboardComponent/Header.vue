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
    <header class="bg-accent1 shadow h-[110px] text-white flex items-center fixed top-0 left-0 right-0 z-20">
        <div class="flex justify-between items-center py-4 px-6 w-full">
            <!-- Left Side -->
            <div class="flex items-center">
                <!-- Sidebar Toggle Button -->
                <button @click="$emit('toggle')" class="text-white focus:outline-none lg:hidden">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                
                <!-- Logo -->
                <div class="ml-4 flex items-center">
                    <ApplicationLogo 
                        :logo="page.props.profile?.logo" 
                        class="h-[76px] w-[76px] fill-current text-white"
                    />
                    
                    <!-- Display CMS title and village name -->
                    <div class="ml-[10px] flex flex-col w-[310px] h-[65px] justify-center">
                        <div class="text-[22px] font-normal">Content Management System</div>
                        <div class="font-bold text-[31px] leading-tight">{{ villageName }}</div>
                    </div>
                </div>
            </div>

            <!-- Right Side - User Profile Dropdown -->
            <div class="flex items-center">
                <Dropdown 
                    align="right" 
                    :width="322"
                    contentClasses="bg-white"
                >
                    <template #trigger>
                        <button class="flex items-center font-medium text-white hover:text-gray-100 focus:outline-none transition duration-150 ease-in-out w-[322px] px-3 justify-end">
                            <div class="flex items-center w-full">
                                <!-- User Avatar - Show profile picture if available, otherwise show initials -->
                                <div class="relative mr-[10px]">
                                    <div v-if="$page.props.auth.user.photo_profile" 
                                        class="h-[45px] w-[45px] rounded-full overflow-hidden border-2 border-white">
                                        <img :src="'/storage/' + $page.props.auth.user.photo_profile"
                                            :alt="$page.props.auth.user.name" 
                                            class="h-full w-full object-cover" />
                                    </div>
                                    <div v-else 
                                        class="h-[45px] w-[45px] rounded-full bg-white text-accent1 flex items-center justify-center border-2 border-white font-medium">
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                </div>
                                
                                <div class="flex flex-col items-start flex-1 overflow-hidden">
                                    <div class="truncate max-w-[220px] text-[18px]">{{ $page.props.auth.user.name }}</div>
                                    <div class="opacity-80 truncate max-w-[220px] text-[15px]">
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
                                
                                <!-- Dropdown indicator -->
                                <svg class="flex-shrink-0 fill-current h-4 w-4 ml-2" 
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
                        <!-- Profile Link -->
                        <DropdownLink 
                            :href="route('profile.edit')" 
                            :active="route().current('profile.edit')"
                        >
                            <div class="flex items-center">
                                <svg class="h-[24px] w-[24px] mr-[10px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Profile</span>
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
                                <svg class="h-[24px] w-[24px] mr-[10px]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Log Out</span>
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
