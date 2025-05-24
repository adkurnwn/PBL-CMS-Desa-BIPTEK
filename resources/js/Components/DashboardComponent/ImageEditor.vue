<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import ImageUploader from "@/Components/DashboardComponent/ImageUploader.vue";
import { ref, watch, computed } from "vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";

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
    <div class="photo-upload-container p-[5.3px] sm:p-[6px] md:p-[6.7px] lg:p-[7.3px] xl:p-[8px] 2xl:p-[9.3px] 3xl:p-[10.7px] 4xl:p-[12px] flex flex-col items-stretch justify-start gap-[5.3px] sm:gap-[6px] md:gap-[6.7px] lg:gap-[7.3px] xl:gap-[8px] 2xl:gap-[9.3px] 3xl:gap-[10.7px] 4xl:gap-[12px] text-left text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] w-full">
        <!-- Upload new images -->
        <div class="new-images-section mt-0 w-full">
            <ImageUploader
                :modelValue="modelValue"
                :alts="alts"
                :label="`Tambah ${label} Baru`"
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
        <!-- Show existing images -->
        <div v-if="existingImages.length > 0" class="existing-images-section w-full">
            <div class="field-name text-selectedText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] mb-[4.4px] sm:mb-[5px] md:mb-[5.6px] lg:mb-[6.1px] xl:mb-[6.67px] 2xl:mb-[7.7px] 3xl:mb-[8.9px] 4xl:mb-[10px]">
                {{ label }} ({{ visibleImagesCount }})
            </div>
            <div class="flex justify-right items-center mb-2">
                <div v-if="altText && visibleImagesCount > 1" class="bulk-edit-controls flex items-center gap-[2.2px] sm:gap-[2.5px] md:gap-[2.9px] lg:gap-[3.1px] xl:gap-[3.33px] 2xl:gap-[3.8px] 3xl:gap-[4.4px] 4xl:gap-[5px]">
                    <div v-if="!bulkEditMode" class="flex items-center gap-[2.2px] sm:gap-[2.5px] md:gap-[2.9px] lg:gap-[3.1px] xl:gap-[3.33px] 2xl:gap-[3.8px] 3xl:gap-[4.4px] 4xl:gap-[5px]">
                        <PrimaryButton
                            @click="toggleBulkEditMode"
                            type="button"
                        >
                            Bulk Edit Alt Text
                        </PrimaryButton>
                    </div>
                    <div v-else class="bulk-edit-active flex items-center gap-[2.2px] sm:gap-[2.5px] md:gap-[2.9px] lg:gap-[3.1px] xl:gap-[3.33px] 2xl:gap-[3.8px] 3xl:gap-[4.4px] 4xl:gap-[5px]">
                        <input
                            v-model="commonAltText"
                            type="text"
                            class="bulk-alt-input text-TableText bg-menuBG border border-selectedText border-solid focus:outline-none focus:border-2 focus:border-selectedText h-[20.7px] sm:h-[23.5px] md:h-[26.3px] lg:h-[28.7px] xl:h-[31px] 2xl:h-[35.3px] 3xl:h-[40px] 4xl:h-[46px] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] p-[5.3px] sm:p-[6px] md:p-[6.7px] lg:p-[7.3px] xl:p-[8px] 2xl:p-[9.3px] 3xl:p-[10.7px] 4xl:p-[12px] min-w-[53.3px] sm:min-w-[60px] md:min-w-[67px] lg:min-w-[73px] xl:min-w-[80px] 2xl:min-w-[93px] 3xl:min-w-[107px] 4xl:min-w-[120px] rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] transition-all duration-200 box-border"
                            :placeholder="defaultAltText || 'Enter common alt text'"
                        />
                        <PrimaryButton
                            @click="applyBulkAltText"
                            type="button"
                        >
                            Terapkan
                        </PrimaryButton>
                        <SecondaryButton
                            @click="toggleBulkEditMode"
                            type="button"
                        >
                            Batal
                        </SecondaryButton>
                    </div>
                </div>
            </div>

            <div class="images-grid grid grid-cols-2 md:grid-cols-3 gap-[5.3px] sm:gap-[6px] md:gap-[6.7px] lg:gap-[7.3px] xl:gap-[8px] 2xl:gap-[9.3px] 3xl:gap-[10.7px] 4xl:gap-[12px] m-0 w-full">
                <div
                    v-for="image in editableImages"
                    :key="image.id"
                    class="image-container relative rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] p-[4px] sm:p-[4.5px] md:p-[5px] lg:p-[5.5px] xl:p-[6px] 2xl:p-[7px] 3xl:p-[8px] 4xl:p-[9px] bg-menuBG"
                    v-show="!removedImages.includes(image.id)"
                >
                    <div class="image-wrapper relative w-full mb-[2.7px] sm:mb-[3px] md:mb-[3.4px] lg:mb-[3.7px] xl:mb-[4px] 2xl:mb-[4.6px] 3xl:mb-[5.3px] 4xl:mb-[6px]">
                        <img
                            :src="`/storage/${image.path}`"
                            :alt="image.alt || defaultAltText"
                            class="preview-image w-full h-[80px] sm:h-[90px] md:h-[100px] lg:h-[110px] xl:h-[120px] 2xl:h-[130px] 3xl:h-[140px] 4xl:h-[150px] object-cover rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] shadow-sm"
                        />
                        <button
                            type="button"
                            @click="removeExistingImage(image.id)"
                            class="delete-button bg-dangerText text-white absolute -top-[5.3px] sm:-top-[6px] md:-top-[6.7px] lg:-top-[7.3px] xl:-top-[8px] 2xl:-top-[9.3px] 3xl:-top-[10.7px] 4xl:-top-[12px] -right-[5.3px] sm:-right-[6px] md:-right-[6.7px] lg:-right-[7.3px] xl:-right-[8px] 2xl:-right-[9.3px] 3xl:-right-[10.7px] 4xl:-right-[12px] w-[10.7px] sm:w-[12px] md:w-[13.6px] lg:w-[14.7px] xl:w-[16px] 2xl:w-[18.5px] 3xl:w-[21.3px] 4xl:w-[24px] h-[10.7px] sm:h-[12px] md:h-[13.6px] lg:h-[14.7px] xl:h-[16px] 2xl:h-[18.5px] 3xl:h-[21.3px] 4xl:h-[24px] rounded-full flex items-center justify-center border border-white cursor-pointer p-0 shadow-sm transition-opacity duration-200 hover:opacity-90 focus:outline-none focus:ring-1 focus:ring-red-400"
                            aria-label="Delete image"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="alt-text-section mt-[2.7px] sm:mt-[3px] md:mt-[3.4px] lg:mt-[3.7px] xl:mt-[4px] 2xl:mt-[4.6px] 3xl:mt-[5.3px] 4xl:mt-[6px]" v-if="altText">
                        <div class="field-name text-selectedText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] mb-[4.4px] sm:mb-[5px] md:mb-[5.6px] lg:mb-[6.1px] xl:mb-[6.67px] 2xl:mb-[7.7px] 3xl:mb-[8.9px] 4xl:mb-[10px]">
                            Alt Text
                        </div>
                        <input
                            :id="`alt-existing-${image.id}`"
                            v-model="image.alt"
                            @input="updateExistingAlt(image.id, image.alt)"
                            type="text"
                            class="alt-input text-TableText bg-menuBG border border-selectedText border-solid focus:outline-none focus:border-2 focus:border-selectedText w-full h-[20.7px] sm:h-[23.5px] md:h-[26.3px] lg:h-[28.7px] xl:h-[31px] 2xl:h-[35.3px] 3xl:h-[40px] 4xl:h-[46px] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] p-[5.3px] sm:p-[6px] md:p-[6.7px] lg:p-[7.3px] xl:p-[8px] 2xl:p-[9.3px] 3xl:p-[10.7px] 4xl:p-[12px] rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] mt-[1.3px] sm:mt-[1.5px] md:mt-[1.7px] lg:mt-[1.8px] xl:mt-[2px] 2xl:mt-[2.3px] 3xl:mt-[2.7px] 4xl:mt-[3px] transition-all duration-200 box-border"
                            :placeholder="defaultAltText || 'Describe this image'"
                        />
                        <div
                            class="instructions-subtitle text-searchIcon text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] mt-[1.3px] sm:mt-[1.5px] md:mt-[1.7px] lg:mt-[1.8px] xl:mt-[2px] 2xl:mt-[2.3px] 3xl:mt-[2.7px] 4xl:mt-[3px]"
                            v-if="defaultAltText"
                        >
                            *Kosongkan untuk menggunakan judul sebagai alt text
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>