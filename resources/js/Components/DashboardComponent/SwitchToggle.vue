<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: [Boolean, String],
        required: true,
        validator: (value) => {
            if (typeof value === 'boolean') return true;
            return ['true', 'false', 'aktif', 'nonaktif'].includes(value.toLowerCase());
        }
    },
    text: {
        type: String,
        required: true
    }
});

// Determine if the state is active based on type (now supports boolean and string)
const isActive = computed(() => {
    if (typeof props.type === 'boolean') return props.type;
    return props.type.toLowerCase() === 'true' || props.type.toLowerCase() === 'aktif';
});

// Determine the appropriate class based on active state
const labelClass = computed(() => {
    return isActive.value 
        ? 'bg-[#EBF2EB] text-[#0E8400]' 
        : 'bg-[#F5EDEE] text-[#C23D39]';
});

// Get the text color from the labelClass to use for the icon
const iconColor = computed(() => {
    const textColorMatch = labelClass.value.match(/text-\[(.*?)\]/);
    if (textColorMatch && textColorMatch[1]) {
        return textColorMatch[1].replace(/"/g, '');
    }
    return '#000000';
});
</script>

<template>
    <span 
        class="h-[8.4px] sm:h-[9.6px] md:h-[10.8px] lg:h-[11.7px] xl:h-[12.6px] 2xl:h-[14.4px] 3xl:h-[16.2px] 4xl:h-[19px] 
               px-[4px] sm:px-[4.5px] md:px-[5px] lg:px-[5.5px] xl:px-[6px] 2xl:px-[7px] 3xl:px-[8px] 4xl:px-[9px] 
               py-[1.3px] sm:py-[1.5px] md:py-[1.7px] lg:py-[1.8px] xl:py-[2px] 2xl:py-[2.3px] 3xl:py-[2.6px] 4xl:py-[3px] 
               text-[5.3px] sm:text-[6px] md:text-[6.7px] lg:text-[7.3px] xl:text-[8px] 2xl:text-[9.3px] 3xl:text-[10.7px] 4xl:text-[12px] 
               rounded-[0.9px] sm:rounded-[1px] md:rounded-[1.1px] lg:rounded-[1.2px] xl:rounded-[1.33px] 2xl:rounded-[1.6px] 3xl:rounded-[1.8px] 4xl:rounded-[2px] 
               font-bold inline-flex items-center justify-center gap-[0.7px] sm:gap-[0.8px] md:gap-[0.85px] lg:gap-[0.9px] xl:gap-[1px] 2xl:gap-[1.1px] 3xl:gap-[1.2px] 4xl:gap-[1.3px]" 
        :class="labelClass"
    >
        <span>{{ text.toUpperCase() }}</span>
        <svg 
            xmlns="http://www.w3.org/2000/svg" 
            width="11" 
            height="11" 
            viewBox="0 0 24 25" 
            fill="none"
            class="transform transition-transform duration-200 w-[5.3px] h-[5.3px] sm:w-[6px] sm:h-[6px] md:w-[7px] md:h-[7px] lg:w-[8px] lg:h-[8px] xl:w-[10.6px] xl:h-[10.6px] 2xl:w-[12px] 2xl:h-[12px] 3xl:w-[14px] 3xl:h-[14px] 4xl:w-[16px] 4xl:h-[16px]"
            :class="{ 'rotate-180': isActive }"
        >
            <mask id="mask0_switch_toggle" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="25">
                <rect y="0.5" width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_switch_toggle)">
                <path d="M7 14.5L12 9.5L17 14.5H7Z" :fill="iconColor"/>
            </g>
        </svg>
    </span>
</template>
