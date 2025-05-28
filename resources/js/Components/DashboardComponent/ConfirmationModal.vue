<template>
    <div v-if="isVisible" class="fixed inset-0 z-[9999] flex items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="onCancel"></div>
        
        <!-- Modal -->
        <div class="relative bg-white rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <!-- Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                
                <!-- Title -->
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ title }}</h3>
                
                <!-- Message -->
                <p class="text-sm text-gray-500 mb-6">{{ message }}</p>
                
                <!-- Buttons -->
                <div class="flex gap-3 justify-center">
                    <button
                        type="button"
                        @click="onCancel"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        {{ cancelText }}
                    </button>
                    <button
                        type="button"
                        @click="onConfirm"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        {{ confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    isVisible: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: 'Konfirmasi'
    },
    message: {
        type: String,
        default: 'Apakah Anda yakin?'
    },
    confirmText: {
        type: String,
        default: 'Ya, Hapus'
    },
    cancelText: {
        type: String,
        default: 'Batal'
    }
});

const emit = defineEmits(['confirm', 'cancel']);

const onConfirm = () => {
    emit('confirm');
};

const onCancel = () => {
    emit('cancel');
};
</script>
