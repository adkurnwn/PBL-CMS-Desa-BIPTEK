<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Search...'
    },
    modelValue: {
        type: String,
        default: ''
    },
    debounce: {
        type: Number,
        default: 300
    }
});

const emit = defineEmits(['update:modelValue', 'search']);

const searchQuery = ref(props.modelValue);
let debounceTimeout = null;

watch(searchQuery, (newValue) => {
    // Clear previous timeout
    if (debounceTimeout) clearTimeout(debounceTimeout);

    // Set new timeout
    debounceTimeout = setTimeout(() => {
        emit('update:modelValue', newValue);
        emit('search', newValue);
    }, props.debounce);
});

watch(() => props.modelValue, (newValue) => {
    searchQuery.value = newValue;
});
</script>

<template>
    <div class="search-container w-[290px] h-[27px] sm:w-[310px] sm:h-[30px] md:w-[330px] md:h-[35px] xl:w-[300px] xl:h-[29px] 2xl:w-[350px] 2xl:h-[35px] 3xl:w-[400px] 3xl:h-[40px] 4xl:w-[450px] 4xl:h-[44px]">
        <div class="relative flex items-center w-full h-full gap-[6px] sm:gap-[7px] md:gap-[8px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px]">
            <!-- Search icon -->
            <div class="absolute left-[2.8px] sm:left-[3px] md:left-[3.5px] xl:left-[6.67px] 2xl:left-[3.5px] 3xl:left-[3.8px] 4xl:left-4 text-searchIcon">
                <svg xmlns="http://www.w3.org/2000/svg" 
                    class="w-[14px] h-[14px] sm:w-[15px] sm:h-[15px] md:w-[16px] md:h-[16px] xl:w-[16px] xl:h-[16px] 2xl:w-[20px] 2xl:h-[20px] 3xl:w-[22px] 3xl:h-[22px] 4xl:w-[24px] 4xl:h-[24px]" 
                    viewBox="0 0 24 24" fill="none">
                    <path
                        d="M19.6 21L13.3 14.7C12.8 15.1 12.225 15.4167 11.575 15.65C10.925 15.8833 10.2333 16 9.5 16C7.68333 16 6.14583 15.3708 4.8875 14.1125C3.62917 12.8542 3 11.3167 3 9.5C3 7.68333 3.62917 6.14583 4.8875 4.8875C6.14583 3.62917 7.68333 3 9.5 3C11.3167 3 12.8542 3.62917 14.1125 4.8875C15.3708 6.14583 16 7.68333 16 9.5C16 10.2333 15.8833 10.925 15.65 11.575C15.4167 12.225 15.1 12.8 14.7 13.3L21 19.6L19.6 21ZM9.5 14C10.75 14 11.8125 13.5625 12.6875 12.6875C13.5625 11.8125 14 10.75 14 9.5C14 8.25 13.5625 7.1875 12.6875 6.3125C11.8125 5.4375 10.75 5 9.5 5C8.25 5 7.1875 5.4375 6.3125 6.3125C5.4375 7.1875 5 8.25 5 9.5C5 10.75 5.4375 11.8125 6.3125 12.6875C7.1875 13.5625 8.25 14 9.5 14Z"
                        fill="#A0AEC0" />
                </svg>
            </div>

            <!-- Search input with updated styling and proper padding for clear button -->
            <input 
                type="text" 
                v-model="searchQuery" 
                :placeholder="placeholder"
                class="w-full h-full pl-[25px] sm:pl-[27px] md:pl-[30px] xl:pl-[28px] 2xl:pl-[32px] 3xl:pl-[35px] 4xl:pl-12 
                       pr-[8px] sm:pr-[9px] md:pr-[9px] xl:pr-[8px] 2xl:pr-[9px] 3xl:pr-[9px] 4xl:pr-10 
                       py-[1.5px] sm:py-[1.7px] md:py-[1.8px] xl:py-[1.5px] 2xl:py-[1.7px] 3xl:py-[1.8px] 4xl:py-2 
                       rounded-[10px] sm:rounded-[11px] md:rounded-[12px] xl:rounded-[10px] 2xl:rounded-[12px] 3xl:rounded-[13px] 4xl:rounded-[15px] 
                       bg-transparent text-TableText border border-searchBorder focus:outline-none focus:ring-1 focus:ring-selectedText 
                       focus:border-selectedText transition-all duration-200 placeholder:text-searchIcon text-ellipsis
                       text-[10px] sm:text-[11px] md:text-[11.5px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]" 
            />

            <!-- Clear button with z-index to ensure it stays on top -->
            <div 
                v-if="searchQuery" 
                @click="searchQuery = ''"
                class="absolute right-[3px] sm:right-[3.5px] md:right-[3.7px] xl:right-[3px] 2xl:right-[3.5px] 3xl:right-[3.8px] 4xl:right-4 
                      cursor-pointer text-searchIcon hover:text-gray-600 z-10 bg-white bg-opacity-50"
            >
                <svg xmlns="http://www.w3.org/2000/svg" 
                    class="w-[12px] h-[12px] sm:w-[13px] sm:h-[13px] md:w-[14px] md:h-[14px] xl:w-[12px] xl:h-[12px] 2xl:w-[15px] 2xl:h-[15px] 3xl:w-[16px] 3xl:h-[16px] 4xl:w-[18px] 4xl:h-[18px]"
                    viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>
        </div>
    </div>
</template>


