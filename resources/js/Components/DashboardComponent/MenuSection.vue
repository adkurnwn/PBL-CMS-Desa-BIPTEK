<script setup>
import { ref, provide, computed } from "vue";

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    active: {
        type: Boolean,
        default: false
    },
    collapsed: {
        type: Boolean,
        default: false
    }
});

// When sidebar is collapsed, always show content (only icons)
// When sidebar is expanded, use the isExpanded state
const isExpanded = ref(true);

// Combined expansion state based on sidebar collapse state
const showContent = computed(() => props.collapsed || isExpanded.value);

// Provide expanded state to child components
provide('menuExpanded', showContent);

const toggleExpand = () => {
    if (!props.collapsed) {
        isExpanded.value = !isExpanded.value;
    }
};
</script>

<template>
    <div class="menu-section mb-[10px] sm:mb-[11px] md:mb-[12px] xl:mb-[13px] 2xl:mb-[15px] 3xl:mb-[18px] 4xl:mb-[20px]">
        <!-- Title button - hide in collapsed mode -->
        <button
            v-if="!collapsed"
            @click="toggleExpand"
            class="w-full flex items-center justify-between py-2.5 transition duration-200 text-selectedText text-[10px] sm:text-[10px] md:text-[10px] xl:text-[10px] 2xl:text-[12px] 3xl:text-[13px] 4xl:text-[15px]"
        >
            <div class="flex items-center">
                <span class="font-medium truncate max-w-[280px]">{{ title }}</span>
            </div>
            <svg
                class="h-5 w-5 transform transition-transform duration-200 flex-shrink-0"
                :class="{ 'rotate-180': isExpanded }"
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

        <!-- Content - always show in collapsed mode -->
        <div 
            class="menu-content overflow-hidden transition-all duration-300 ease-out" 
            :style="{ 
                maxHeight: showContent ? '1000px' : '0px',
                opacity: showContent ? '1' : '0'
            }"
        >
            <div 
                class="pt-[8px] sm:pt-[8px] md:pt-[9px] xl:pt-[10px] 2xl:pt-[12px] 3xl:pt-[14px] 4xl:pt-[15px] space-y-[8px] sm:space-y-[9px] md:space-y-[10px] xl:space-y-[10px] 2xl:space-y-[11px] 3xl:space-y-[13px] 4xl:space-y-[15px]"
                :class="collapsed ? '' : ''"
            >
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<style scoped>
.menu-content {
    will-change: max-height, opacity;
}

/* Return to original spacing */
.menu-content > div {
    margin-top: 0;
}

/* Fix transform issue causing extra spacing */
.menu-section {
    transform: none !important;
    margin-right: 0 !important;
    padding-right: 0 !important;
}

/* Ensure nested menu sections maintain proper spacing */
:deep(.menu-section) {
    width: 100%;
}

/* Add styles for collapsed state */
.menu-section:has(.menu-content div.flex.flex-col.items-center) {
    margin-bottom: 10px !important;
}

/* Prevent text wrapping in section titles */
.menu-section button span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
