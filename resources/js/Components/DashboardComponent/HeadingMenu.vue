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
            <div class="w-[30px] h-[30px] sm:w-[32px] sm:h-[32px] md:w-[35px] md:h-[35px] xl:w-[26px] xl:h-[26px] 2xl:w-[30px] 2xl:h-[30px] 3xl:w-[35px] 3xl:h-[35px] 4xl:w-[40px] 4xl:h-[40px] bg-transparent rounded flex items-center justify-center text-selectedText">
                <!-- Icon slot for SVG icons - will override any width/height/color on the SVG -->
                <div class="w-full h-full flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:text-current [&>svg_path]:fill-current">
                    <slot name="icon"></slot>
                </div>
            </div>
            <h2 class="ml-[5px] sm:ml-[6px] md:ml-[8px] xl:ml-[6px] 2xl:ml-[8px] 3xl:ml-[9px] 4xl:ml-[10px] text-[18px] sm:text-[18px] md:text-[18px] xl:text-[20px] 2xl:text-[23px] 3xl:text-[27px] 4xl:text-[31px] font-semibold leading-tight text-selectedText">
                {{ title }}
            </h2>
        </div>
        
        <!-- Optional slot for right-side content like buttons -->
        <div v-if="hasRightContent">
            <slot name="right-content"></slot>
        </div>
    </div>
</template>