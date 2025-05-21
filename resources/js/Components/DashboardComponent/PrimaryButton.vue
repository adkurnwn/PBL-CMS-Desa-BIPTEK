<template>
    <button
        class="inline-flex items-center justify-center h-[46px] rounded-[5px] bg-selectedText px-[19px] py-[11px] text-[15px] font-medium text-white transition duration-150 ease-in-out hover:bg-opacity-90 active:bg-opacity-80"
    >
        <!-- Icon slot with proper sizing and alignment -->
        <span v-if="icon" class="w-5 h-5 flex items-center justify-center">
            <slot name="icon"></slot>
        </span>
        <!-- Button text -->
        <span class="uppercase">
            <slot />
        </span>
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
