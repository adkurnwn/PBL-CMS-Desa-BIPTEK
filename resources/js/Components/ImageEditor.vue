<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
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
    altText: {
        type: Boolean,
        default: true,
    },
    defaultAltText: {
        type: String,
        default: "",
    },
});

// Create a local copy of existing images to track alt text changes
const editableImages = ref(JSON.parse(JSON.stringify(props.existingImages)));

// Update local copy when props change
watch(
    () => props.existingImages,
    (newImages) => {
        editableImages.value = JSON.parse(JSON.stringify(newImages));
    },
    { deep: true }
);

const emit = defineEmits([
    "update:modelValue",
    "update:alts",
    "update:removedImages",
    "update:existingImages",
    "update:bulkAltText",
]);

// Bulk edit related
const bulkEditMode = ref(false);
const commonAltText = ref("");

// Toggle bulk edit mode
const toggleBulkEditMode = () => {
    bulkEditMode.value = !bulkEditMode.value;
    if (!bulkEditMode.value) {
        commonAltText.value = "";
    }
};

// Apply the common alt text to all visible images
const applyBulkAltText = () => {
    if (!commonAltText.value.trim()) return;

    const updatedImages = editableImages.value.map((img) => {
        if (!props.removedImages.includes(img.id)) {
            // Set the alt text, ensuring it's never empty
            return { ...img, alt: commonAltText.value || props.defaultAltText };
        }
        return img;
    });

    editableImages.value = updatedImages;
    emit("update:existingImages", updatedImages);
    bulkEditMode.value = false;
    commonAltText.value = "";
};

// Handle existing image removal
const removeExistingImage = (imageId) => {
    if (confirm("Are you sure you want to remove this image?")) {
        const newRemovedImages = [...props.removedImages, imageId];
        emit("update:removedImages", newRemovedImages);
    }
};

// Function to handle alt text changes for existing images
const updateExistingAlt = (imageId, newAlt) => {
    const updatedImages = editableImages.value.map((img) => {
        if (img.id === imageId) {
            // Allow alt to be empty in the UI, it will be replaced with title later
            return { ...img, alt: newAlt };
        }
        return img;
    });
    editableImages.value = updatedImages;
    emit("update:existingImages", updatedImages);
};

// Pass through to ImageUploader component
const updateImages = (images) => {
    emit("update:modelValue", images);
};

const updateAlts = (alts) => {
    // Replace empty alt texts with default
    const processedAlts = alts.map((alt) =>
        alt.trim() === "" ? props.defaultAltText : alt
    );
    emit("update:alts", processedAlts);
};

// Count visible images (not removed)
const visibleImagesCount = computed(() => {
    return editableImages.value.filter(
        (img) => !props.removedImages.includes(img.id)
    ).length;
});
</script>

<template>
    <div>
        <!-- Show existing images -->
        <div v-if="existingImages.length > 0">
            <div class="flex justify-between items-center mb-2">
                <InputLabel
                    :value="`Current ${label} (${visibleImagesCount})`"
                />

                <div v-if="altText && visibleImagesCount > 1">
                    <div v-if="!bulkEditMode">
                        <PrimaryButton
                            @click="toggleBulkEditMode"
                            type="button"
                            class="text-xs px-2 py-1"
                        >
                            Bulk Edit Alt Text
                        </PrimaryButton>
                    </div>
                    <div v-else class="flex space-x-2">
                        <TextInput
                            v-model="commonAltText"
                            type="text"
                            class="text-sm h-8"
                            :placeholder="
                                defaultAltText || 'Enter common alt text'
                            "
                        />
                        <PrimaryButton
                            @click="applyBulkAltText"
                            type="button"
                            class="text-xs px-2 py-1"
                        >
                            Apply to All
                        </PrimaryButton>
                        <DangerButton
                            @click="toggleBulkEditMode"
                            type="button"
                            class="text-xs px-2 py-1"
                        >
                            Cancel
                        </DangerButton>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 my-4">
                <div
                    v-for="image in editableImages"
                    :key="image.id"
                    class="relative border rounded-md p-3"
                    v-show="!removedImages.includes(image.id)"
                >
                    <img
                        :src="`/storage/${image.path}`"
                        :alt="image.alt || defaultAltText"
                        class="w-full h-32 object-cover rounded"
                    />
                    <div class="mt-2" v-if="altText">
                        <InputLabel
                            :for="`alt-existing-${image.id}`"
                            value="Alt Text"
                            class="text-sm"
                        />
                        <TextInput
                            :id="`alt-existing-${image.id}`"
                            v-model="image.alt"
                            @input="updateExistingAlt(image.id, image.alt)"
                            type="text"
                            class="mt-1 block w-full text-sm"
                            :placeholder="
                                defaultAltText || 'Describe this image'
                            "
                        />
                        <div
                            class="text-xs text-gray-500 mt-1"
                            v-if="defaultAltText"
                        >
                            Leave empty to use title as alt text
                        </div>
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
            :altText="altText"
            :defaultAltText="defaultAltText"
        />
    </div>
</template>
