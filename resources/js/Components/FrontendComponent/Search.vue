<template>
    <div class="relative flex items-center w-full">
        <div class="relative flex-grow">
            <input
                type="text"
                v-model="searchQuery"
                @input="handleInput"
                @keyup.enter="handleSearch"
                :placeholder="placeholder"
                class="w-full py-2 pl-4 pr-12 border-2 border-accent1 bg-type1 shadow-lg rounded-full italic font-light focus:outline-none focus:ring-2 focus:ring-accent1"
            />
            <button
                @click="handleSearch"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-accent1 hover:text-accent2 transition-colors"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    height="20px"
                    viewBox="0 -960 960 960"
                    width="20px"
                    fill="currentColor"
                >
                    <path
                        d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"
                    />
                </svg>
            </button>
        </div>
        <!-- Filter Button -->
        <button
            v-if="type === 'with-filter'"
            @click="handleFilter"
            class="ml-2 w-11 h-11 border-2 border-accent1 shadow-lg rounded-full text-accent1 hover:text-white transition"
        >
            <svg
                class="ml-2"
                xmlns="http://www.w3.org/2000/svg"
                height="24px"
                viewBox="http://www.w3.org/2000/svg"
                width="24px"
                fill="currentColor"
            >
                <path
                    d="M520-600v-80h120v-160h80v160h120v80H520Zm120 480v-400h80v400h-80Zm-400 0v-160H120v-80h320v80H320v160h-80Zm0-320v-400h80v400h-80Z"
                />
            </svg>
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'default', // 'default' or 'with-filter'
        validator: (value) => ['default', 'with-filter'].includes(value)
    },
    placeholder: {
        type: String,
        default: 'Cari Informasi atau Kegiatan...'
    }
});

const emit = defineEmits(['update:modelValue', 'search', 'filter']);

const searchQuery = ref(props.modelValue);

const handleInput = () => {
    emit('update:modelValue', searchQuery.value);
};

const handleSearch = () => {
    emit('search', searchQuery.value);
};

const handleFilter = () => {
    emit('filter');
};
</script>
