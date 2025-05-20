<template>
    <button
        class="inline-flex items-center justify-center h-[40px] rounded-[5px] border-selectedText border-[1px] bg-transparent px-[19px] py-[11px] text-[15px] font-medium text-selectedText transition duration-150 ease-in-out hover:bg-selectedBG hover:border-transparent active:bg-opacity-90"
    >
        <!-- Icon slot with proper sizing and alignment -->
        <span v-if="icon" class="w-5 h-5 flex items-center justify-center">
            <slot name="icon"></slot>
        </span>
        <!-- Button text -->
        <slot />
    </button>
</template>

<script setup>
defineProps({
    icon: {
        type: Boolean,
        default: false
    }
});
</script>
