<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: [String, Number],
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'bg-white',
    },
    customStyles: {
        type: Object,
        default: () => ({}),
    },
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    // If width is 'auto', don't apply a width class - let contentClasses handle it
    if (props.width === 'auto') {
        return '';
    }
    
    return {
        '48': 'w-48',
    }[props.width.toString()] || '';
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'origin-top-left left-0';
    } else if (props.align === 'right') {
        return 'origin-top-right right-0';
    } else {
        return 'origin-top';
    }
});

const open = ref(false);
</script>

<template>
    <div class="dropdown relative">
        <div @click="open = ! open">
            <slot name="trigger"></slot>
        </div>

        <div
            v-show="open"
            class="fixed inset-0 z-40"
            @click="open = false"
        ></div>

        <transition
            enter-active-class="transition-all ease-out duration-300"
            enter-from-class="transform opacity-0 scale-y-0 -translate-y-2"
            enter-to-class="transform opacity-100 scale-y-100 translate-y-0"
            leave-active-class="transition-all ease-in duration-200"
            leave-from-class="transform opacity-100 scale-y-100 translate-y-0"
            leave-to-class="transform opacity-0 scale-y-0 -translate-y-2"
        >
            <div
                v-show="open"
                class="dropdown-content absolute z-50 mt-1 sm:mt-1.5 xl:mt-1.5 2xl:mt-1.5 3xl:mt-1.5 4xl:mt-2 shadow-lg rounded-lg sm:rounded-[9px] xl:rounded-[8px] 2xl:rounded-[9px] 3xl:rounded-[9px] 4xl:rounded-[10px] overflow-hidden shadow-[0px_3px_8px_rgba(0,0,0,0.08)] sm:shadow-[0px_3px_9px_rgba(0,0,0,0.08)] xl:shadow-[0px_3px_8px_rgba(0,0,0,0.09)] 2xl:shadow-[0px_3px_10px_rgba(0,0,0,0.09)] 3xl:shadow-[0px_4px_10px_rgba(0,0,0,0.09)] 4xl:shadow-[0px_4px_12px_rgba(0,0,0,0.1)] bg-menuBG"
                :class="[widthClass, alignmentClasses, contentClasses]"
                :style="[
                    typeof width === 'number' ? { width: width + 'px' } : {}, 
                    { transformOrigin: 'top' },
                    customStyles
                ]"
                @click="open = false"
            >
                <slot name="content"></slot>
            </div>
        </transition>
    </div>
</template>
