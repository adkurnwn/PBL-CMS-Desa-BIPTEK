<template>
    <button
        :type="type"
        :disabled="disabled"
        class="inline-flex items-center justify-center 
               h-[20.7px] sm:h-[23.5px] md:h-[26.3px] lg:h-[28.7px] xl:h-[31px] 2xl:h-[35.5px] 3xl:h-[40.3px] 4xl:h-[46px] 
               rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] 
               border-selectedText border-[0.45px] sm:border-[0.5px] md:border-[0.55px] lg:border-[0.65px] xl:border-[0.67px] 2xl:border-[0.75px] 3xl:border-[0.85px] 4xl:border-[1px]
               bg-transparent 
               px-[8.6px] sm:px-[9.7px] md:px-[10.9px] lg:px-[11.8px] xl:px-[12.7px] 2xl:px-[14.5px] 3xl:px-[16.5px] 4xl:px-[19px] 
               py-[5px] sm:py-[5.6px] md:py-[6.3px] lg:py-[6.9px] xl:py-[7.3px] 2xl:py-[8.4px] 3xl:py-[9.6px] 4xl:py-[11px] 
               text-[6.8px] sm:text-[7.7px] md:text-[8.6px] lg:text-[9.3px] xl:text-[10px] 2xl:text-[11.5px] 3xl:text-[13.1px] 4xl:text-[15px] 
               font-medium text-selectedText transition duration-150 ease-in-out hover:bg-selectedBG hover:border-transparent active:bg-opacity-90 gap-2"
        :class="additionalClasses"
    >
        <!-- Icon slot - only show if icon slot has content -->
        <span v-if="$slots.icon" class="flex items-center justify-center flex-shrink-0 w-[10.2px] h-[10.2px] sm:w-[11.55px] sm:h-[11.55px] md:w-[12.9px] md:h-[12.9px] lg:w-[13.95px] lg:h-[13.95px] xl:w-[15px] xl:h-[15px] 2xl:w-[17.25px] 2xl:h-[17.25px] 3xl:w-[19.65px] 3xl:h-[19.65px] 4xl:w-[22.5px] 4xl:h-[22.5px]">
            <slot name="icon"></slot>
        </span>
        
        <!-- Button text -->
        <span class="uppercase flex-shrink-0">
            <slot />
        </span>
    </button>
</template>

<script setup>
defineProps({
    type: {
        type: String,
        default: 'button'
    },
    disabled: {
        type: Boolean,
        default: false
    },
    additionalClasses: {
        type: String,
        default: ''
    }
});
</script>
