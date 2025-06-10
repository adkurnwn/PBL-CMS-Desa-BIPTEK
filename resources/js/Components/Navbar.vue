<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useImageErrorHandler } from '@/Utility/useImageErrorHandler';

defineProps({
    layout: {
        type: Object,
        default: () => ({}),
    },
});

const { handleImageError, getImageSrc } = useImageErrorHandler();
const isScrolled = ref(false);
const isMenuOpen = ref(false);
const currentPath = ref(window.location.pathname);
const isHomePage = computed(() => currentPath.value === "/");
const menuItems = [
    { name: "Beranda", link: "/" },
    { name: "Profil Desa", link: "/profile" },
    {
        name: "Informasi & Berita",
        link: "/informasi",
        children: [
            { name: "Daftar Berita", link: "/berita" },
            { name: "Daftar Event", link: "/event" },
            { name: "Galeri Desa", link: "/galeri" },
        ]
    },
    { 
        name: "Layanan Publik",
        link: "/layanan",
        children: [
            { name: "Laporan Dana Desa", link: "/dana-desa" },
            { name: "Produk Hukum", link: "/produk-hukum" },
            { name: "Produk Unggulan", link: "/produk-unggulan" },
            { name: "Data Demografi", link: "/data-demografi" },
            { name: "Struktur Organisasi", link: "/struktur-organisasi" },
        ] 

    },
];

const handleScroll = () => {
    if (isHomePage) {
        isScrolled.value = window.scrollY > 50;
    }
};

const isActive = (link) => currentPath.value === link;
const isChildActive = (item) => {
  if (!item.children) return false;
  return item.children.some(child => isActive(child.link));
};

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};
const dropdownOpen = ref(null);
const toggleDropdown = (index) => {
    dropdownOpen.value = dropdownOpen.value === index ? null : index;
};
const isDesktop = ref(window.innerWidth > 1024);

function handleResize() {
  isDesktop.value = window.innerWidth >= 1024;
}

onMounted(() => {
    if (isHomePage) {
        window.addEventListener("scroll", handleScroll);
    }
    window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
    if (isHomePage) {
        window.removeEventListener("scroll", handleScroll);
    }
    window.removeEventListener("resize", handleResize);
});
</script>
<template>
    <nav
        :class="[
            'sticky z-50 mt-4 mx-2 lg:mx-6 flex rounded-lg items-center justify-between px-4 md:px-8 py-4 transition-colors duration-300 font-merriweather',
            isScrolled || !isHomePage ? 'bg-accent1' : 'bg-transparent',
        ]"
    >
        <div class="flex items-center space-x-4 xl:space-x-6 4xl:space-x-8">
            <img
                :src="getImageSrc(`/storage/${layout.logo}`)"
                :alt="'Logo ' + layout.name"
                class="w-10 h-12 2xl:w-12 2xl:h-16  4xl:w-20 4xl:h-24 " 
                @error="handleImageError(`/storage/${layout.logo}`)"
            />
            <div class="flex flex-col">
                <h1
                    class="text-white font-bold text-base sm:text-2xl xl:md:text-xl 4xl:text-3xl"
                >
                    {{ layout.name }}
                </h1>
                <p class="text-white text-[12px] sm:text-base md:text-xs 3xl:text-sm 4xl:text-lg 4xl:mt-1">
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
            v-show="isDesktop || !isMenuOpen"
            :class="[
                'md:flex text-sm sm:text-xl md:text-sm 3xl:text-base 4xl:text-xl lg:space-x-8 text-white font-semibold transition-all duration-300 sm:mt-3 md:mt-6 lg:mt-2.5 lg:mr-6',
                isMenuOpen
                    ? 'block'
                    : 'max-h-0 opacity-0 scale-y-0 sm:max-h-full sm:opacity-100 lg:scale-y-100',
                'absolute lg:static w-1/2 top-20 my-1 rounded-lg right-0 lg:w-auto bg-accent1 lg:bg-transparent lg:flex flex-col lg:flex-row space-y-4 sm:space-y-6 py-5 sm:py-8 lg:py-0 lg:space-y-0 text-center xl:text-left p-4 lg:p-0',
            ]"
        >
        <li v-for="(item, index) in menuItems" :key="index" class="relative group">
            <template v-if="item.children">
                <a
                :href="item.link"
                class="relative inline-block pb-1 after:content-[''] after:absolute after:left-0 after:bottom-0 after:-mt-1 after:w-0 after:h-[2px] after:bg-white after:transition-all after:duration-300 after:ease-in-out hover:after:w-full hover:after:left-0 font-merriweather"
                    :class="{
                        'after:w-full after:left-0': isActive(item.link),
                    }"
                >
                {{ item.name }}
                <svg class="inline ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
                </a>
                <ul
                    class="absolute left-0 -ml-4 py-1 w-56 bg-accent1 rounded-lg shadow-lg z-50 text-left
                            opacity-0 group-hover:opacity-100 hover:opacity-100
                            pointer-events-none group-hover:pointer-events-auto hover:pointer-events-auto
                            transition-all duration-300 transform scale-95 group-hover:scale-100 hover:scale-100
                            translate-y-2 group-hover:translate-y-0 hover:translate-y-0"
                    >
                    <li
                        v-for="(child, cidx) in item.children"
                        :key="cidx"
                        class="transition-all duration-200"
                    >
                        <a
                        :href="child.link"
                        class="hidden lg:flex items-center gap-2 text-sm px-4 py-2 rounded-md hover:bg-accent2 hover:text-white hover:pl-6 border-l-4 border-transparent hover:border-accent2 transition-all duration-200"
                        >
                        <span>{{ child.name }}</span>
                        <svg
                            class="w-4 h-4 ml-auto opacity-0 group-hover:opacity-60 transition-opacity duration-200"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                        </a>
                    </li>
                    </ul>
            </template>
            <template v-else>
                <a
                    :href="item.link"
                    class="relative inline-block pb-1  after:content-[''] after:absolute after:left-0 after:bottom-0 after:-mt-1 after:w-0 after:h-[2px] after:bg-white after:transition-all after:duration-300 after:ease-in-out hover:after:w-full hover:after:left-0 font-merriweather"
                    :class="{
                        'after:w-full after:left-0': isActive(item.link),
                    }"
                >
                    {{ item.name }}
                </a>
            </template>
        </li>
        </ul>

        <transition name="fade">
            <div
                v-if="isMenuOpen && !isDesktop"
                class="fixed inset-0 bg-black bg-opacity-50 z-40"
                @click="toggleMenu"
            ></div>
        </transition>
        <!-- MOBILE ASIDE MENU -->
        <transition name="slide-aside">
            <aside
                v-if="isMenuOpen && !isDesktop"
                class="fixed top-0 right-0 z-50 h-full w-2/3 bg-accent1 p-6 shadow-lg transition-transform duration-300 transform translate-x-0 lg:hidden"
            >
            <button
                @click="toggleMenu"
                class="absolute top-4 right-4 text-white text-2xl"
            >
                ✕
            </button>
            <ul class="space-y-4 mt-12">
                <li v-for="(item, index) in menuItems" :key="index">
                    <div>
                    <div
                        v-if="item.children"
                        class="flex items-center justify-between w-full text-white font-semibold rounded-md px-2 py-2 transition-all duration-200"
                        :class="{
                            'bg-accent2/40 border-l-4 border-accent2': isActive(item.link)
                        }"
                    >
                        <a
                            :href="item.link"
                            class="flex-1"
                            style="min-width:0"
                        >
                            {{ item.name }}
                        </a>
                        <!-- Dropdown arrow button -->
                        <button
                            @click.stop="toggleDropdown(index)"
                            class="ml-2 flex-shrink-0 p-1 rounded hover:bg-accent2/30 transition bg-accent2"
                            aria-label="Tampilkan submenu"
                            type="button"
                        >
                            <svg
                            :class="{ 'rotate-180': dropdownOpen === index }"
                            class="w-5 h-5 transform transition-transform duration-300"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            >
                            <path d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                    <a
                        v-else
                        :href="item.link"
                        class="flex items-center justify-between w-full text-white font-semibold rounded-md px-2 py-2 transition-all duration-200"
                        :class="{
                        'bg-accent2/40 border-l-4 ml-1 border-accent2': isActive(item.link) || isChildActive(item.link) || dropdownOpen === index
                        }"
                    >
                        {{ item.name }}
                    </a>
                    <!-- Dropdown Children -->
                    <transition name="fade-slide">
                        <ul
                        v-show="dropdownOpen === index"
                        class="mt-2 mb-4 ml-4 space-y-2"
                        >
                        <li
                            v-for="(child, cidx) in item.children"
                            :key="cidx"
                        >
                            <a
                            :href="child.link"
                            class="block text-white text-sm py-2 px-4 rounded-md transition-all duration-200 border-l-4 border-transparent"
                            :class="{
                                'bg-accent2 border-accent2 opacity-95 font-bold ': isChildActive(item)
                            }"
                            >
                            {{ child.name }}
                            </a>
                        </li>
                        </ul>
                    </transition>
                    </div>
                </li>
                </ul>
            </aside>
            
        </transition>


    </nav>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
  transition: all 0.3s ease;
}

/* Slide-in/out from right */
.slide-aside-enter-active,
.slide-aside-leave-active {
  transition: transform 0.5s ease, opacity 0.5s ease;
}

.slide-aside-enter-from,
.slide-aside-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

.slide-aside-enter-to,
.slide-aside-leave-from {
  transform: translateX(0%);
  opacity: 1;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.fade-enter-to, .fade-leave-from {
  opacity: 1;
}


</style>
