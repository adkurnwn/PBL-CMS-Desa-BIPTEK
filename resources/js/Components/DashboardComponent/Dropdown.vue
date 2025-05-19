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
    return {
        '48': 'w-48',
    }[props.width.toString()];
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
                class="dropdown-content absolute z-50 mt-2 shadow-lg"
                :class="[widthClass, alignmentClasses, contentClasses]"
                :style="[
                    typeof width === 'number' ? { width: width + 'px' } : {}, 
                    { transformOrigin: 'top', borderRadius: '10px', overflow: 'hidden' },
                    customStyles
                ]"
                @click="open = false"
            >
                <slot name="content"></slot>
            </div>
        </transition>
    </div>
</template>

<style scoped>
/* Match MenuBox styling */
.dropdown-content {
    background-color: theme('colors.menuBG');
    border-radius: 10px !important;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
}

/* Style dropdown items similar to menu items */
:deep(.dropdown-item) {
    display: flex;
    align-items: center;
    height: 48px;
    padding: 0 12px;
    transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
    color: theme('colors.menuText');
}

:deep(.dropdown-item:hover) {
    background-color: theme('colors.selectedBG');
    color: theme('colors.selectedText');
}

:deep(.dropdown-item.active) {
    background-color: theme('colors.selectedBG');
    color: theme('colors.selectedText');
}

/* Add danger styling for logout button */
:deep(.dropdown-item.danger:hover) {
    background-color: theme('colors.dangerBG');
    color: theme('colors.dangerText');
}

:deep(.dropdown-item.danger:hover svg) {
    color: theme('colors.dangerText');
}

/* Match text styling */
:deep(.dropdown-item span) {
    font-size: 18px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}

/* Match icon styling */
:deep(.dropdown-item svg) {
    height: 24px;
    width: 24px;
    margin-right: 10px;
    flex-shrink: 0;
    color: currentColor;
}

/* Style for dropdown content */
:deep(.dropdown-item svg:hover) {
    color: inherit;
}
</style>
