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
            v-if="as === 'button'"
            type="submit"
            class="w-full text-left dropdown-item"
            :class="{
                'active': active,
                'danger': danger
            }"
            @click="$emit('click', $event)"
        >
            <slot />
        </button>

        <Link
            v-else-if="as === 'a'"
            :href="href"
            class="dropdown-item"
            :class="{
                'active': active,
                'danger': danger
            }"
        >
            <slot />
        </Link>

        <Link
            v-else
            :href="href"
            :method="method"
            :as="as"
            :preserve-scroll="preserveScroll"
            class="dropdown-item"
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
