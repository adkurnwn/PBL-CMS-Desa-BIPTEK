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
            class="w-full text-left block px-4 py-2 text-sm leading-5 transition duration-150 ease-in-out"
            :class="{
                'active': active, 
                'danger': danger
            }"
            @click="$emit('click', $event)"
        >
            <slot />
        </button>

        <!-- Special case for post/put/delete methods with button rendering -->
        <Link
            v-else-if="as === 'button' && method !== 'get'"
            :href="href"
            :method="method"
            :preserve-scroll="preserveScroll"
            as="button"
            type="button"
            class="w-full text-left block px-4 py-2 text-sm leading-5 transition duration-150 ease-in-out dropdown-item"
            :class="{
                'active': active, 
                'danger': danger
            }"
        >
            <slot />
        </Link>

        <!-- Regular link with get method -->
        <Link
            v-else
            :href="href"
            :method="method"
            :as="as"
            :preserve-scroll="preserveScroll"
            class="block px-4 py-2 text-sm leading-5 transition duration-150 ease-in-out dropdown-item"
            :class="{
                'active': active, 
                'danger': danger
            }"
        >
            <slot />
        </Link>
    </div>
</template>

<style scoped>
/* Individual dropdown link styling moved to Dropdown.vue component for consistency */
</style>
