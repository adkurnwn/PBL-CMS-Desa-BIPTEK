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
            'menu-box flex items-center rounded-[6px] sm:rounded-[6px] md:rounded-[6px] xl:rounded-[6px] 2xl:rounded-[7px] 3xl:rounded-[8px] 4xl:rounded-[10px]',
            collapsed ? 'w-[32px] sm:w-[32px] md:w-[32px] xl:w-[32px] 2xl:w-[40px] 3xl:w-[44px] 4xl:w-[48px] justify-center p-0' : 'w-full',
            'h-[32px] sm:h-[32px] md:h-[32px] xl:h-[32px] 2xl:h-[40px] 3xl:h-[44px] 4xl:h-[48px]',
            !collapsed ? 'pr-[10px] sm:pr-[10px] md:pr-[11px] xl:pr-[10px] 2xl:pr-[11px] 3xl:pr-[11px] 4xl:pr-[12px]' : '',
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
            class="icon-fixed flex-shrink-0 h-[16px] w-[16px] sm:h-[16px] sm:w-[16px] md:h-[16px] md:w-[16px] xl:h-[16px] xl:w-[16px] 2xl:h-[18px] 2xl:w-[18px] 3xl:h-[21px] 3xl:w-[21px] 4xl:h-[24px] 4xl:w-[24px]" 
            :class="collapsed ? '' : 'ml-[8px] sm:ml-[9px] md:ml-[10px] xl:ml-[8px] 2xl:ml-[10px] 3xl:ml-[11px] 4xl:ml-[12px] mr-[8px] sm:mr-[8px] md:mr-[9px] xl:mr-[8px] 2xl:mr-[9px] 3xl:mr-[9px] 4xl:mr-[10px]'"
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor" 
            v-html="icon"
        ></svg>
        
        <!-- Text that fades out but doesn't shift right -->
        <span 
            v-if="!collapsed" 
            class="truncate overflow-hidden max-w-[280px] menu-text text-[12px] sm:text-[12px] md:text-[12px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
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
