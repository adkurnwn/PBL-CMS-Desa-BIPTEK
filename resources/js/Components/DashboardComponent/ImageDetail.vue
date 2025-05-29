<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-8" @click="closeModal">
        <div class="relative max-w-4xl max-h-full">
            <img :src="imageSrc" :alt="imageAlt" class="max-w-full max-h-full object-contain" @click.stop />
            <button @click="closeModal" class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl font-bold">
                ×
            </button>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
    imageSrc: {
        type: String,
        default: ''
    },
    imageAlt: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['close'])

const closeModal = () => {
    emit('close')
}
</script>
