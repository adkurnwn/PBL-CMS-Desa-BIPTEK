<template>
    <div
        class="flex flex-col w-full text-left text-[#111827] font-sans gap-[7px] sm:gap-[7px] md:gap-[7px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px]">
        <!-- Avatar and Name Row -->
        <div
            class="flex flex-row items-center justify-start gap-[7px] sm:gap-[7px] md:gap-[7px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px]">
            <div
                class="w-[30px] h-[30px] sm:w-[32px] sm:h-[32px] md:w-[35px] md:h-[35px] xl:w-[26px] xl:h-[26px] 2xl:w-[30px] 2xl:h-[30px] 3xl:w-[35px] 3xl:h-[35px] 4xl:w-[40px] 4xl:h-[40px]  rounded-full overflow-hidden flex-shrink-0">
                <img :src="avatarUrl" :alt="name" class="w-full h-full object-cover" @error="handleImageError" />
            </div>
            <div
                class="flex-1 relative font-medium text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                {{ name }}</div>
        </div>

        <div
            class="flex w-full gap-[7px] sm:gap-[7px] md:gap-[7px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px]">
            <div
                class="w-[30px] h-[30px] sm:w-[32px] sm:h-[32px] md:w-[35px] md:h-[35px] xl:w-[26px] xl:h-[26px] 2xl:w-[30px] 2xl:h-[30px] 3xl:w-[35px] 3xl:h-[35px] 4xl:w-[40px] 4xl:h-[40px]  rounded-full overflow-hidden flex-shrink-0">
                
            </div>
            <div
                class="rounded-r-sm w-full sm:rounded-r-sm md:rounded-r lg:rounded-r-md xl:rounded-r-lg 2xl:rounded-r-xl 3xl:rounded-r-2xl 4xl:rounded-r-md bg-gray-200 bg-opacity-50 border-l border-[#6f7684] overflow-hidden flex flex-col items-start justify-center p-[2.6px] sm:p-[3px] md:p-[3.4px] lg:p-[3.6px] xl:p-[4px] 2xl:p-[4.6px] 3xl:p-[5.4px] 4xl:p-[6px] text-[6px] sm:text-[7px] md:text-[8px] lg:text-[9px] xl:text-[10px] 2xl:text-[11px] 3xl:text-[13px] 4xl:text-[15px]">
                <div class="self-stretch relative font-medium">{{ title }}</div>
                <i class="self-stretch relative font-light">{{ description }}</i>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
    name: {
        type: String,
        default: ''
    },
    title: {
        type: String,
        default: ''
    },
    description: {
        type: String,
        default: ''
    }
})

const getAvatar = (name) => {
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(
        name
    )}&background=random`;
};

const avatarUrl = computed(() => {
    return getAvatar(props.name)
})
</script>