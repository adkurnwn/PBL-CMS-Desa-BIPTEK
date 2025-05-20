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
        class="h-[19px] px-[6px] py-[2px] text-[12px] font-bold rounded-[2px] inline-flex items-center justify-center gap-1" 
        :class="labelClass"
    >
        <span>{{ text.toUpperCase() }}</span>
        <svg 
            xmlns="http://www.w3.org/2000/svg" 
            width="16" 
            height="16" 
            viewBox="0 0 24 25" 
            fill="none"
            class="transform transition-transform duration-200"
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
