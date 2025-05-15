<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
const showContentManagementDropdown = ref(false);
const showAdminDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>

                                <!-- Content Management Dropdown -->
                                <div class="relative">
                                    <button
                                        @click="
                                            showContentManagementDropdown =
                                                !showContentManagementDropdown
                                        "
                                        class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out mt-4"
                                        :class="[
                                            route().current(
                                                'dashboard.news.*'
                                            ) ||
                                            route().current(
                                                'dashboard.galleries.*'
                                            ) ||
                                            route().current(
                                                'dashboard.events.*'
                                            ) ||
                                            route().current(
                                                'dashboard.produk-hukum.*'
                                            ) ||
                                            route().current(
                                                'dashboard.dana-desa.*'
                                            ) ||
                                            route().current(
                                                'dashboard.produk-desa.*'
                                            ) ||
                                            route().current(
                                                'dashboard.comments'
                                            ) ||
                                            route().current(
                                                'dashboard.demografi.*'
                                            ) ||
                                            route().current(
                                                'dashboard.produk-hukum.index-comments'
                                            )
                                                ? 'border-indigo-400 text-gray-900'
                                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        ]"
                                    >
                                        Content Management
                                        <svg
                                            class="ml-2 -mr-0.5 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>

                                    <div
                                        v-show="showContentManagementDropdown"
                                        class="absolute z-50 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5"
                                        @click.outside="
                                            showContentManagementDropdown = false
                                        "
                                    >
                                        <Link
                                            :href="
                                                route('dashboard.news.index')
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            News Management
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.galleries.index'
                                                )
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Gallery Management
                                        </Link>
                                        <Link
                                            :href="
                                                route('dashboard.events.index')
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Event Management
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.produk-hukum.index'
                                                )
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Produk Hukum
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.dana-desa.index'
                                                )
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Dana Desa
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.produk-desa.index'
                                                )
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Produk Desa
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.demografi.index'
                                                )
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Demografi
                                        </Link>
                                    </div>
                                </div>

                                <!-- Admin Dropdown -->
                                <div class="relative">
                                    <button
                                        @click="
                                            showAdminDropdown =
                                                !showAdminDropdown
                                        "
                                        class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out mt-4"
                                        :class="[
                                            route().current(
                                                'dashboard.users.*'
                                            ) ||
                                            route().current(
                                                'dashboard.roles.*'
                                            ) ||
                                            route().current(
                                                'dashboard.profiles.*'
                                            ) ||
                                            route().current(
                                                'dashboard.comments'
                                            ) ||
                                            route().current(
                                                'dashboard.produk-hukum.index-comments'
                                            ) ||
                                            route().current(
                                                'dashboard.feedbacks.*'
                                            ) ||
                                            route().current(
                                                'dashboard.recovery.*'
                                            )
                                                ? 'border-indigo-400 text-gray-900'
                                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        ]"
                                    >
                                        Administration
                                        <svg
                                            class="ml-2 -mr-0.5 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>

                                    <div
                                        v-show="showAdminDropdown"
                                        class="absolute z-50 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5"
                                        @click.outside="
                                            showAdminDropdown = false
                                        "
                                    >
                                        <Link
                                            :href="
                                                route('dashboard.users.index')
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            User Management
                                        </Link>
                                        <Link
                                            :href="
                                                route('dashboard.roles.index')
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Role Management
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.profiles.index'
                                                )
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Profile Desa Management
                                        </Link>
                                        <Link
                                            :href="route('dashboard.comments')"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Comment Moderation
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.produk-hukum.index-comments'
                                                )
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Comment Produk Hukum
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.feedbacks.index'
                                                )
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Feedback
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.recovery.index'
                                                )
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Recovery
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="mx-auto max-w-7xl px-4 py-2 sm:px-6 lg:px-8">
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

            <!-- Page Content -->
            <main>
                <slot />
            </main>
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
</style>
