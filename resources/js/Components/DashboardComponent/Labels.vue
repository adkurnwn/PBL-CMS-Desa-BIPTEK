<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['create', 'update', 'delete', 'other'].includes(value)
    },
    text: {
        type: String,
        required: true
    }
});

// Determine the appropriate class based on action type
const labelClass = computed(() => {
    switch (props.type) {
        case 'create':
            return 'bg-greenLabel text-greenLabelText';
        case 'update':
            return 'bg-greenLabel text-greenLabelText';
        case 'edit':
            return 'bg-yellowLabel text-yellowLabelText';
        case 'delete':
            return 'bg-redLabel text-redLabelText';
        case 'logout':
            return 'bg-redLabel text-redLabelText';
        case 'login':
            return 'bg-blueLabel text-blueLabelText';
        case 'kepala desa':
            return 'bg-[#C7AE2B]/25 text-[#C7AE2B]';
        case 'sekretaris desa':
            return 'bg-[#00BFFF]/25 text-[#00BFFF]';
        case 'bendahara desa':
            return 'bg-[#32CD32]/25 text-[#32CD32]';
        default:
            return 'bg-gray-100 text-gray-800';
    }
});
</script>

<template>
    <span 
        class="h-[19px] px-[6px] py-[2px] text-[12px] font-bold rounded-[2px] inline-flex items-center justify-center" 
        :class="labelClass"
    >
        {{ text.toUpperCase() }}
    </span>
</template>
