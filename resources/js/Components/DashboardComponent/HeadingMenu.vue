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
            <div class="w-[40px] h-[40px] max-lg:w-[35px] max-lg:h-[35px] max-md:w-[30px] max-md:h-[30px] bg-transparent rounded flex items-center justify-center text-selectedText">
                <!-- Icon slot for SVG icons -->
                <slot name="icon"></slot>
            </div>
            <h2 class="ml-[10px] max-lg:ml-[8px] max-md:ml-[5px] text-[37px] max-lg:text-[30px] max-md:text-[24px] font-semibold leading-tight text-selectedText">
                {{ title }}
            </h2>
        </div>
        
        <!-- Optional slot for right-side content like buttons -->
        <div v-if="hasRightContent">
            <slot name="right-content"></slot>
        </div>
    </div>
</template>