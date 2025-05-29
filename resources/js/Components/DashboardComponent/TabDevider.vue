<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    tabs: {
        type: Array,
        required: true,
        // Expected format: [{ 
        //   name: 'Tab Name',
        //   href: '/path', 
        //   active: true,
        //   onClick: () => {} // Optional click handler for filtering
        // }]
    }
});

const handleTabClick = (tab, event) => {
    if (tab.onClick) {
        event.preventDefault();
        tab.onClick();
    }
};
</script>

<template>
    <div class="tab-devider">
        <div class="flex pl-[6px] sm:pl-[7px] md:pl-[8px] xl:pl-[8px] 2xl:pl-[9px] 3xl:pl-[9px] 4xl:pl-[10px]">
            <div 
                v-for="(tab, index) in tabs" 
                :key="index"
                class="relative"
            >
                <Link
                    v-if="!tab.onClick"
                    :href="tab.href"
                    class="px-[8px] sm:px-[9px] md:px-[10px] xl:px-[8px] 2xl:px-[10px] 3xl:px-[11px] 4xl:px-[12px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[6.5px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] inline-block text-[14.5px] sm:text-[16px] md:text-[18px] xl:text-[14.5px] 2xl:text-[17px] 3xl:text-[20px] 4xl:text-[22px] font-medium transition-colors duration-200"
                    :class="tab.active 
                        ? 'text-accent1' 
                        : 'text-menuText hover:text-accent1/80'"
                >
                    {{ tab.name }}
                    <span 
                        v-if="tab.badge && tab.badge > 0" 
                        class="ml-2 inline-flex items-center justify-center min-w-[20px] h-[20px] px-1 text-[10px] font-medium bg-menuText text-white rounded-full"
                    >
                        {{ tab.badge }}
                    </span>
                    <div 
                        v-if="tab.active" 
                        class="absolute bottom-0 left-0 w-full h-[1px] sm:h-[1px] md:h-[1.5px] xl:h-[1.5px] 2xl:h-[1.5px] 3xl:h-[2px] 4xl:h-[2px] bg-accent1"
                    ></div>
                </Link>
                <button
                    v-else
                    @click="handleTabClick(tab, $event)"
                    class="px-[8px] sm:px-[9px] md:px-[10px] xl:px-[8px] 2xl:px-[10px] 3xl:px-[11px] 4xl:px-[12px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[6.5px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] inline-block text-[14.5px] sm:text-[16px] md:text-[18px] xl:text-[14.5px] 2xl:text-[17px] 3xl:text-[20px] 4xl:text-[22px] font-medium transition-colors duration-200 cursor-pointer bg-transparent border-none"
                    :class="tab.active 
                        ? 'text-accent1' 
                        : 'text-menuText hover:text-accent1/80'"
                >
                    {{ tab.name }}
                    <span 
                        v-if="tab.badge && tab.badge > 0" 
                        class="ml-2 inline-flex items-center justify-center min-w-[20px] h-[20px] px-1 text-[10px] font-medium bg-menuText text-white rounded-full"
                    >
                        {{ tab.badge }}
                    </span>
                    <div 
                        v-if="tab.active" 
                        class="absolute bottom-0 left-0 w-full h-[1px] sm:h-[1px] md:h-[1.5px] xl:h-[1.5px] 2xl:h-[1.5px] 3xl:h-[2px] 4xl:h-[2px] bg-accent1"
                    ></div>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.tab-devider {
    width: 100%;
    overflow-x: auto;
}
</style>
