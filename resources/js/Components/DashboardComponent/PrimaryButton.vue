<template>
    <button
        class="inline-flex items-center justify-center 
               gap-[2px] sm:gap-[2.5px] md:gap-[3px] lg:gap-[3.5px] xl:gap-[4px] 2xl:gap-[4.5px] 3xl:gap-[5px] 4xl:gap-[6px]
               h-[20.7px] sm:h-[23.5px] md:h-[26.3px] lg:h-[28.7px] xl:h-[31px] 2xl:h-[35.5px] 3xl:h-[40.3px] 4xl:h-[46px] 
               rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] 
               bg-selectedText 
               px-[8.6px] sm:px-[9.7px] md:px-[10.9px] lg:px-[11.8px] xl:px-[12.7px] 2xl:px-[14.5px] 3xl:px-[16.5px] 4xl:px-[19px] 
               py-[5px] sm:py-[5.6px] md:py-[6.3px] lg:py-[6.9px] xl:py-[7.3px] 2xl:py-[8.4px] 3xl:py-[9.6px] 4xl:py-[11px] 
               text-[6.8px] sm:text-[7.7px] md:text-[8.6px] lg:text-[9.3px] xl:text-[10px] 2xl:text-[11.5px] 3xl:text-[13.1px] 4xl:text-[15px] 
               font-medium text-white transition duration-150 ease-in-out hover:bg-opacity-90 active:bg-opacity-80"
    >
        <!-- Icon slot with proper sizing and alignment -->
        <span v-if="$slots.icon" class="flex items-center justify-center flex-shrink-0 w-[10.2px] h-[10.2px] sm:w-[11.55px] sm:h-[11.55px] md:w-[12.9px] md:h-[12.9px] lg:w-[13.95px] lg:h-[13.95px] xl:w-[15px] xl:h-[15px] 2xl:w-[17.25px] 2xl:h-[17.25px] 3xl:w-[19.65px] 3xl:h-[19.65px] 4xl:w-[22.5px] 4xl:h-[22.5px]">
            <slot name="icon"></slot>
        </span>
        <!-- Button text -->
        <span class="uppercase  flex-shrink-0">
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
