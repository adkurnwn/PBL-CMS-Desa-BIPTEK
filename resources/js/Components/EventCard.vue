<script setup>
import { defineProps } from "vue";

defineProps({
    event: {
        type: Object,
        required: false,
        default: () => ({}),
    },
});
</script>
<template>
    <div class="relative w-full md:w-1/3 bg-accent2 rounded-lg shadow-2xl">
        <div class="px-7 md:px-9 py-7">
            <div class="flex flex-col">
                <h2 class="text-2xl md:text-3xl font-semibold text-secondary">
                    {{ event.date }}
                </h2>
                <h3
                    class="text-lg md:text-xl font-semibold text-white line-clamp-2 h-14 items-center justify-center mt-4 mb-2"
                >
                    {{ event.title }}
                </h3>
            </div>
            <div class="flex flex-col justify-between">
                <div class="flex flex-col text-lg space-y-1">
                    <div class="flex flex-row space-x-4">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="18px"
                            viewBox="0 -960 960 960"
                            width="18px"
                            fill="currentColor"
                            class="text-secondary md:h-[24px] md:w-[24px]"
                        >
                            <path
                                d="M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z"
                            />
                        </svg>
                        <h2
                            class="text-xs md:text-sm text-type1 font-extralight mt-0.5"
                        >
                            {{ event.location }}
                        </h2>
                    </div>
                    <div class="flex flex-row space-x-4">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="18px"
                            viewBox="0 -960 960 960"
                            width="18px"
                            fill="currentColor"
                            class="text-secondary md:h-[24px] md:w-[24px]"
                        >
                            <path
                                d="M480-80q-75 0-140.5-28.5t-114-77q-48.5-48.5-77-114T120-440q0-75 28.5-140.5t77-114q48.5-48.5 114-77T480-800q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-440q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-80Zm0-360Zm112 168 56-56-128-128v-184h-80v216l152 152ZM224-866l56 56-170 170-56-56 170-170Zm512 0 170 170-56 56-170-170 56-56ZM480-160q117 0 198.5-81.5T760-440q0-117-81.5-198.5T480-720q-117 0-198.5 81.5T200-440q0 117 81.5 198.5T480-160Z"
                            />
                        </svg>
                        <h2
                            class="text-xs md:text-sm text-type1 font-extralight mt-0.5"
                        >
                            {{ event.time }} - Selesai
                        </h2>
                    </div>
                </div>
            </div>
            <div
                class="lg:absolute md:bottom-0 md:right-0 flex flex-row space-x-4 md:p-3 px-4 hover:scale-105 transition-transform"
            >
                <button
                    class="bg-accent1 text-white text-sm mx-auto px-8 py-2 mt-4 rounded-lg"
                >
                    Atur Pengingat
                </button>
            </div>
        </div>
    </div>
</template>
