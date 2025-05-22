<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: false,
    },
    as: {
        type: String,
        default: 'a',
    },
    method: {
        type: String,
        default: 'get',
    },
    preserveScroll: {
        type: Boolean,
        default: false,
    },
    active: {
        type: Boolean,
        default: false,
    },
    danger: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div>
        <button
            v-if="as === 'button' && method === 'get'"
            type="button"
            class="w-full text-left flex items-center transition duration-300 text-menuText hover:bg-selectedBG hover:text-selectedText h-[24px] sm:h-[24px] md:h-[24px] xl:h-[32px] 2xl:h-[36px] 3xl:h-[42px] 4xl:h-[48px] px-[10px] sm:px-[10px] md:px-[11px] xl:px-[10px] 2xl:px-[11px] 3xl:px-[11px] 4xl:px-[12px]"
            :class="{
                'bg-selectedBG text-selectedText': active, 
                'hover:bg-dangerBG hover:text-dangerText': danger
            }"
            @click="$emit('click', $event)"
        >
            <template v-if="$slots.default">
                <svg v-if="$slots.default()[0]?.children && $slots.default()[0].children[0]?.type === 'svg'" 
                    :class="'h-[18px] w-[18px] sm:h-[19px] sm:w-[19px] md:h-[20px] md:w-[20px] xl:h-[20px] xl:w-[20px] 2xl:h-[21px] 2xl:w-[21px] 3xl:h-[22px] 3xl:w-[22px] 4xl:h-[24px] 4xl:w-[24px] mr-[8px] sm:mr-[8px] md:mr-[9px] xl:mr-[8px] 2xl:mr-[9px] 3xl:mr-[9px] 4xl:mr-[10px] flex-shrink-0'"
                    :viewBox="$slots.default()[0].children[0].props.viewBox"
                    :fill="$slots.default()[0].children[0].props.fill"
                    :stroke="$slots.default()[0].children[0].props.stroke"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path 
                        v-if="$slots.default()[0].children[0].children[0]?.props"
                        :stroke-linecap="$slots.default()[0].children[0].children[0].props['stroke-linecap']"
                        :stroke-linejoin="$slots.default()[0].children[0].children[0].props['stroke-linejoin']"
                        :stroke-width="$slots.default()[0].children[0].children[0].props['stroke-width']"
                        :d="$slots.default()[0].children[0].children[0].props.d"
                        :clip-rule="$slots.default()[0].children[0].children[0].props['clip-rule']"
                        :fill-rule="$slots.default()[0].children[0].children[0].props['fill-rule']"
                    />
                </svg>
                <span class="text-[14px] sm:text-[15px] md:text-[16px] xl:text-[14px] 2xl:text-[15px] 3xl:text-[16px] 4xl:text-[18px] whitespace-nowrap text-ellipsis overflow-hidden">
                    {{ $slots.default()[0]?.children && $slots.default()[0].children[1]?.children }}
                </span>
            </template>
            <slot v-else />
        </button>

        <!-- Special case for post/put/delete methods with button rendering -->
        <Link
            v-else-if="as === 'button' && method !== 'get'"
            :href="href"
            :method="method"
            :preserve-scroll="preserveScroll"
            as="button"
            type="button"
            class="w-full text-left flex items-center transition duration-300 text-menuText hover:bg-selectedBG hover:text-selectedText h-[24px] sm:h-[24px] md:h-[24px] xl:h-[32px] 2xl:h-[36px] 3xl:h-[42px] 4xl:h-[48px] px-[10px] sm:px-[10px] md:px-[11px] xl:px-[10px] 2xl:px-[11px] 3xl:px-[11px] 4xl:px-[12px]"
            :class="{
                'bg-selectedBG text-selectedText': active, 
                'hover:bg-dangerBG hover:text-dangerText': danger
            }"
        >
            <template v-if="$slots.default">
                <svg v-if="$slots.default()[0]?.children && $slots.default()[0].children[0]?.type === 'svg'" 
                    :class="['h-[18px] w-[18px] sm:h-[19px] sm:w-[19px] md:h-[20px] md:w-[20px] xl:h-[20px] xl:w-[20px] 2xl:h-[21px] 2xl:w-[21px] 3xl:h-[22px] 3xl:w-[22px] 4xl:h-[24px] 4xl:w-[24px] mr-[8px] sm:mr-[8px] md:mr-[9px] xl:mr-[8px] 2xl:mr-[9px] 3xl:mr-[9px] 4xl:mr-[10px] flex-shrink-0', { 'group-hover:text-dangerText': danger }]"
                    :viewBox="$slots.default()[0].children[0].props.viewBox"
                    :fill="$slots.default()[0].children[0].props.fill"
                    :stroke="$slots.default()[0].children[0].props.stroke"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path 
                        v-if="$slots.default()[0].children[0].children[0]?.props"
                        :stroke-linecap="$slots.default()[0].children[0].children[0].props['stroke-linecap']"
                        :stroke-linejoin="$slots.default()[0].children[0].children[0].props['stroke-linejoin']"
                        :stroke-width="$slots.default()[0].children[0].children[0].props['stroke-width']"
                        :d="$slots.default()[0].children[0].children[0].props.d"
                        :clip-rule="$slots.default()[0].children[0].children[0].props['clip-rule']"
                        :fill-rule="$slots.default()[0].children[0].children[0].props['fill-rule']"
                    />
                </svg>
                <span class="text-[14px] sm:text-[15px] md:text-[16px] xl:text-[14px] 2xl:text-[15px] 3xl:text-[16px] 4xl:text-[18px] whitespace-nowrap text-ellipsis overflow-hidden">
                    {{ $slots.default()[0]?.children && $slots.default()[0].children[1]?.children }}
                </span>
            </template>
            <slot v-else />
        </Link>

        <!-- Regular link with get method -->
        <Link
            v-else
            :href="href"
            :method="method"
            :as="as"
            :preserve-scroll="preserveScroll"
            class="block group flex items-center transition duration-300 text-menuText hover:bg-selectedBG hover:text-selectedText h-[24px] sm:h-[24px] md:h-[24px] xl:h-[32px] 2xl:h-[36px] 3xl:h-[42px] 4xl:h-[48px] px-[10px] sm:px-[10px] md:px-[11px] xl:px-[10px] 2xl:px-[11px] 3xl:px-[11px] 4xl:px-[12px]"
            :class="{
                'bg-selectedBG text-selectedText': active, 
                'hover:bg-dangerBG hover:text-dangerText': danger
            }"
        >
            <template v-if="$slots.default">
                <svg v-if="$slots.default()[0]?.children && $slots.default()[0].children[0]?.type === 'svg'" 
                    :class="['h-[18px] w-[18px] sm:h-[19px] sm:w-[19px] md:h-[20px] md:w-[20px] xl:h-[20px] xl:w-[20px] 2xl:h-[21px] 2xl:w-[21px] 3xl:h-[22px] 3xl:w-[22px] 4xl:h-[24px] 4xl:w-[24px] mr-[8px] sm:mr-[8px] md:mr-[9px] xl:mr-[8px] 2xl:mr-[9px] 3xl:mr-[9px] 4xl:mr-[10px] flex-shrink-0', { 'group-hover:text-dangerText': danger }]"
                    :viewBox="$slots.default()[0].children[0].props.viewBox"
                    :fill="$slots.default()[0].children[0].props.fill"
                    :stroke="$slots.default()[0].children[0].props.stroke"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path 
                        v-if="$slots.default()[0].children[0].children[0]?.props"
                        :stroke-linecap="$slots.default()[0].children[0].children[0].props['stroke-linecap']"
                        :stroke-linejoin="$slots.default()[0].children[0].children[0].props['stroke-linejoin']"
                        :stroke-width="$slots.default()[0].children[0].children[0].props['stroke-width']"
                        :d="$slots.default()[0].children[0].children[0].props.d"
                        :clip-rule="$slots.default()[0].children[0].children[0].props['clip-rule']"
                        :fill-rule="$slots.default()[0].children[0].children[0].props['fill-rule']"
                    />
                </svg>
                <span class="text-[14px] sm:text-[15px] md:text-[16px] xl:text-[14px] 2xl:text-[15px] 3xl:text-[16px] 4xl:text-[18px] whitespace-nowrap text-ellipsis overflow-hidden">
                    {{ $slots.default()[0]?.children && $slots.default()[0].children[1]?.children }}
                </span>
            </template>
            <slot v-else />
        </Link>
    </div>
</template>

<style scoped>
/* Individual dropdown link styling moved to Dropdown.vue component for consistency */
</style>
