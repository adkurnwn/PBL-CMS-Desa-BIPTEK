<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    tabs: {
        type: Array,
        required: true,
        // Each tab should have: { name: 'Tab Name', href: '/route', active: boolean }
    }
});

// You can use this if you want to control tabs internally without props
// const activeTab = ref(0);
// const switchTab = (index) => {
//     activeTab.value = index;
// };
</script>

<template>
    <div class="tab-devider">
        <div class="flex pl-[10px]">
            <div 
                v-for="(tab, index) in tabs" 
                :key="index"
                class="relative"
            >
                <Link
                    :href="tab.href"
                    class="px-[12px] py-[10px] inline-block text-[22px] font-medium transition-colors duration-200"
                    :class="tab.active 
                        ? 'text-accent1' 
                        : 'text-menuText hover:text-accent1/80'"
                >
                    {{ tab.name }}
                    <div 
                        v-if="tab.active" 
                        class="absolute bottom-0 left-0 w-full h-[2px] bg-accent1"
                    ></div>
                </Link>
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
