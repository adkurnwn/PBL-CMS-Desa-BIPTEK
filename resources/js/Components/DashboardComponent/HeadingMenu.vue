<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    // Optional right content for buttons or other elements
    hasRightContent: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="w-[40px] h-[40px] bg-transparent rounded flex items-center justify-center text-selectedText">
                <!-- Icon slot for SVG icons -->
                <slot name="icon"></slot>
            </div>
            <h2 class="ml-[10px] text-[37px] font-semibold leading-tight text-selectedText">
                {{ title }}
            </h2>
        </div>
        
        <!-- Optional slot for right-side content like buttons -->
        <div v-if="hasRightContent">
            <slot name="right-content"></slot>
        </div>
    </div>
</template>