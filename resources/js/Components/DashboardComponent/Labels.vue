<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => [
            'create', 'update', 'delete', 'other', 'mendatang', 'edit', 
            'berlangsung', 'berakhir', 'logout', 'login', 'kepala desa', 
            'sekretaris desa', 'bendahara desa', 'menunggu', 'ditanggapi',
            'saran', 'keluhan'
        ].includes(value)
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
        case 'mendatang':
            return 'bg-greenLabel text-greenLabelText';
        case 'update':
            return 'bg-greenLabel text-greenLabelText';
        case 'edit':
            return 'bg-yellowLabel text-yellowLabelText';
        case 'berlangsung':
            return 'bg-yellowLabel text-yellowLabelText';
        case 'delete':
            return 'bg-redLabel text-redLabelText';
        case 'berakhir':
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
        case 'menunggu':
             return 'bg-yellowLabel text-yellowLabelText';
        case 'ditanggapi':
            return 'bg-greenLabel text-greenLabelText';
        case 'saran':
            return 'bg-blueLabel text-blueLabelText';
        case 'keluhan':
            return 'bg-greenLabel text-greenLabelText';
        case 'Berita':
            return 'bg-blueLabel text-blueLabelText';
        case 'Produk Hukum':
            return 'bg-redLabel text-redLabelText';
        case 'Dana Desa':
            return 'bg-greenLabel text-greenLabelText';
        default:
            return 'bg-gray-100 text-gray-800';
    }
});
</script>

<template>
    <span class="h-[8.4px] sm:h-[9.6px] md:h-[10.8px] lg:h-[11.7px] xl:h-[12.6px] 2xl:h-[14.4px] 3xl:h-[16.2px] 4xl:h-[19px] 
               px-[4px] sm:px-[4.5px] md:px-[5px] lg:px-[5.5px] xl:px-[6px] 2xl:px-[7px] 3xl:px-[8px] 4xl:px-[9px] 
               py-[1.3px] sm:py-[1.5px] md:py-[1.7px] lg:py-[1.8px] xl:py-[2px] 2xl:py-[2.3px] 3xl:py-[2.6px] 4xl:py-[3px] 
               text-[5.3px] sm:text-[6px] md:text-[6.7px] lg:text-[7.3px] xl:text-[8px] 2xl:text-[9.3px] 3xl:text-[10.7px] 4xl:text-[12px] 
               rounded-[0.9px] sm:rounded-[1px] md:rounded-[1.1px] lg:rounded-[1.2px] xl:rounded-[1.33px] 2xl:rounded-[1.6px] 3xl:rounded-[1.8px] 4xl:rounded-[2px] 
               font-bold inline-flex items-center justify-center" :class="labelClass">
        {{ text.toUpperCase() }}
    </span>
</template>
