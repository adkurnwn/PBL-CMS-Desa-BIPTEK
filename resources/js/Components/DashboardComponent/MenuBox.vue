<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed, inject, ref, onMounted } from "vue";

const props = defineProps({
    href: {
        type: String,
        required: true
    },
    active: {
        type: Boolean,
        default: false
    },
    icon: {
        type: String,
        default: null
    },
    collapsed: {
        type: Boolean,
        default: false
    }
});

// Get current URL for direct comparison
const currentUrl = computed(() => usePage().url);

// Self-checking active state to combine with the provided prop
const isActive = computed(() => {
    return props.active || currentUrl.value === props.href;
});

// For animation purposes - simplify for better performance
const isVisible = ref(true);
const index = inject('menuBoxIndex', 0);

onMounted(() => {
    // Only apply stagger if it's not initial render
    if (index > 0) {
        isVisible.value = false;
        setTimeout(() => {
            isVisible.value = true;
        }, index * 30);
    }
});
</script>

<template>
    <Link
        :href="href"
        :class="[
            'menu-box flex items-center rounded-[10px]',
            collapsed ? 'w-[48px] h-[48px] justify-center p-0' : 'w-full h-[48px] pl-[12px] pr-[12px]',
            isActive
                ? 'bg-selectedBG text-selectedText border border-selectedText'
                : 'bg-menuBG text-menuText hover:bg-selectedBG hover:text-selectedText',
            isVisible ? 'opacity-100' : 'opacity-0'
        ]"
        :title="collapsed ? $slots.default()[0].children : ''"
    >
        <!-- Keep icon at left position -->
        <svg 
            v-if="icon" 
            class="icon-fixed h-[24px] w-[24px] flex-shrink-0" 
            :class="collapsed ? '' : 'ml-[12px] mr-[10px]'"
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor" 
            v-html="icon"
        ></svg>
        
        <!-- Text that fades out but doesn't shift right -->
        <span 
            v-if="!collapsed" 
            class="text-[18px] truncate overflow-hidden max-w-[280px] menu-text"
        >
            <slot></slot>
        </span>
    </Link>
</template>

<style scoped>
.menu-box {
    margin-bottom: 0 !important;
    transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

/* Keep icons fixed at left */
.icon-fixed {
    position: relative;
    transition: margin 300ms ease;
}

/* Text transitions only in opacity */
.menu-text {
    white-space: nowrap;
    text-overflow: ellipsis;
    transition: opacity 200ms ease;
}
</style>
