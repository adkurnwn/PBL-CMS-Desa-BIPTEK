<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";

defineProps({
    layout: {
        type: Object,
        default: () => ({}),
    },
});

const isScrolled = ref(false);
const isMenuOpen = ref(false);
const currentPath = ref(window.location.pathname);
const isHomePage = computed(() => currentPath.value === "/");
const menuItems = [
    { name: "Beranda", link: "/" },
    { name: "Profil Desa", link: "/profile" },
    { name: "Informasi & Berita", link: "/informasi" },
    { name: "Layanan Publik", link: "/layanan" },
];

const handleScroll = () => {
    if (isHomePage) {
        isScrolled.value = window.scrollY > 50;
    }
};
// Fungsi untuk menentukan apakah menu aktif
const isActive = (link) => currentPath.value === link;

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

onMounted(() => {
    if (isHomePage) {
        window.addEventListener("scroll", handleScroll);
    }
});

onUnmounted(() => {
    if (isHomePage) {
        window.removeEventListener("scroll", handleScroll);
    }
});
</script>
<template>
    <nav
        :class="[
            'sticky z-50 mt-4 mx-2 lg:mx-6 flex rounded-lg items-center justify-between px-4 md:px-8 py-4 transition-colors duration-300 font-merriweather',
            isScrolled || !isHomePage ? 'bg-accent1' : 'bg-transparent',
        ]"
    >
        <div class="flex items-center space-x-4">
            <img
                :src="`/storage/${layout.logo}`"
                alt="Logo"
                class="w-10 h-12 md:w-12 md:h-16"
            />
            <div class="flex flex-col">
                <h1
                    class="text-white font-bold text-base sm:text-2xl lg:text-xl"
                >
                    {{ layout.name }}
                </h1>
                <p class="text-white text-[12px] sm:text-base lg:text-sm">
                    {{ layout.kabupaten }}
                </p>
            </div>
        </div>

        <!-- Mobile Menu Button -->
        <button
            @click="toggleMenu"
            class="lg:hidden text-white text-xl transition-colors duration-300"
        >
            ☰
        </button>

        <!-- Navigation Links -->
        <ul
            :class="[
                'md:flex text-sm sm:text-xl lg:text-base lg:space-x-8 text-white font-semibold transition-all duration-300 sm:mt-3 md:mt-6 lg:mt-2.5 lg:mr-6',
                isMenuOpen
                    ? 'block'
                    : 'max-h-0 opacity-0 scale-y-0 sm:max-h-full sm:opacity-100 lg:scale-y-100',
                'absolute lg:static w-1/2 top-20 my-1 rounded-lg right-0 lg:w-auto bg-accent1 lg:bg-transparent lg:flex flex-col lg:flex-row space-y-4 sm:space-y-6 py-5 sm:py-8 lg:py-0 lg:space-y-0 text-center xl:text-left p-4 lg:p-0',
            ]"
        >
            <li v-for="(item, index) in menuItems" :key="index">
                <a
                    :href="item.link"
                    class="relative inline-block pb-1 after:content-[''] after:absolute after:left-0 after:bottom-0 after:-mt-1 after:w-0 after:h-[2px] after:bg-white after:transition-all after:duration-300 after:ease-in-out hover:after:w-full hover:after:left-0 font-merriweather"
                    :class="{
                        'after:w-full after:left-0': isActive(item.link),
                    }"
                >
                    {{ item.name }}
                </a>
            </li>
        </ul>
    </nav>
</template>

<style scoped></style>
