<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import DangerButton from "@/Components/DangerButton.vue";
import ImageUploader from "@/Components/ImageUploader.vue";
import { ref, watch, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    alts: {
        type: Array,
        default: () => [],
    },
    existingImages: {
        type: Array,
        default: () => [],
    },
    removedImages: {
        type: Array,
        default: () => [],
    },
    label: {
        type: String,
        default: "Images",
    },
    error: {
        type: [String, Object],
        default: "",
    },
    altError: {
        type: String,
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    "update:modelValue",
    "update:alts",
    "update:removedImages",
]);

// Handle existing image removal
const removeExistingImage = (imageId) => {
    if (confirm("Are you sure you want to remove this image?")) {
        const newRemovedImages = [...props.removedImages, imageId];
        emit("update:removedImages", newRemovedImages);
    }
};

// Pass through to ImageUploader component
const updateImages = (images) => {
    emit("update:modelValue", images);
};

const updateAlts = (alts) => {
    emit("update:alts", alts);
};
</script>

<template>
    <div>
        <!-- Show existing images -->
        <div v-if="existingImages.length > 0">
            <InputLabel :value="`Current ${label}`" />
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 my-4">
                <div
                    v-for="image in existingImages"
                    :key="image.id"
                    class="relative border rounded-md p-3"
                    v-show="!removedImages.includes(image.id)"
                >
                    <img
                        :src="`/storage/${image.path}`"
                        :alt="image.alt"
                        class="w-full h-32 object-cover rounded"
                    />
                    <div class="mt-2">
                        <p class="text-sm text-gray-600">
                            Alt text: {{ image.alt }}
                        </p>
                    </div>
                    <DangerButton
                        type="button"
                        @click="removeExistingImage(image.id)"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                    >
                        ×
                    </DangerButton>
                </div>
            </div>
        </div>

        <!-- Upload new images -->
        <ImageUploader
            :modelValue="modelValue"
            :alts="alts"
            :label="`Add New ${label}`"
            :error="error"
            :alt-error="altError"
            :required="
                required &&
                existingImages.length === 0 &&
                removedImages.length === existingImages.length
            "
            @update:modelValue="updateImages"
            @update:alts="updateAlts"
        />
    </div>
</template>
