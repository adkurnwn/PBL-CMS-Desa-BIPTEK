<script setup>
import { ref, onUnmounted, onMounted, computed } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";

const props = defineProps({
    modelValue: {
        type: [File, null],
        default: null,
    },
    label: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: "",
    },
    currentImage: {
        type: String,
        default: null,
    },
    imageClass: {
        type: String,
        default: "",
    },
    imageHeight: {
        type: String,
        default: null, // If not provided, will use the default height from CSS
    },
    accept: {
        type: String,
        default: "image/png, image/jpeg, image/jpg",
    },
    customWidth: {
        type: String,
        default: '100%'
    },
    required: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(["update:modelValue"]);
const imagePreview = ref(null);
const isDragging = ref(false);
const fileInput = ref(null);
const currentImageDisplayed = ref(props.currentImage);

const handleImageChange = (e) => {
    const file = e.target.files[0];
    processFile(file);
};

const processFile = (file) => {
    if (!file) return;

    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!validTypes.includes(file.type)) {
        // Could handle error here
        return;
    }

    // Validate file size (max 10MB)
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        // Could handle error here
        return;
    }

    emit("update:modelValue", file);
    imagePreview.value = URL.createObjectURL(file);
};

const triggerFileInput = () => {
    fileInput.value.click();
};

const handleDragOver = (e) => {
    e.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = (e) => {
    e.preventDefault();
    isDragging.value = false;
};

const handleDrop = (e) => {
    e.preventDefault();
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    processFile(file);

    // Update the file input for form submission
    if (file) {
        // Create a DataTransfer object to set files on the input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.value.files = dataTransfer.files;
    }
};

const deleteImage = () => {
    // If there was a blob URL, revoke it to prevent memory leaks
    if (imagePreview.value && imagePreview.value.startsWith('blob:')) {
        URL.revokeObjectURL(imagePreview.value);
    }

    // Clear the preview
    imagePreview.value = null;

    // Also clear the current image display
    currentImageDisplayed.value = null;

    // Emit null to clear the model value in the parent component
    emit("update:modelValue", null);

    // Reset the file input value
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

onMounted(() => {
    // Initialize preview from current image if available
    if (props.currentImage && !imagePreview.value) {
        currentImageDisplayed.value = props.currentImage;
        const img = new Image();
        img.onload = () => {
            // Only set if image loaded successfully
        };
        img.onerror = () => {
            console.warn('Unable to load preview image:', props.currentImage);
            currentImageDisplayed.value = null;
        };
        img.src = `/storage/${props.currentImage}`;
    }
});

// Cleanup preview URL when component unmounts
onUnmounted(() => {
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
    }
});

// Calculate image style to apply custom height if provided
const imageStyle = computed(() => {
    if (props.imageHeight) {
        return {
            height: props.imageHeight,
            width: 'auto', // Set width to auto to maintain aspect ratio
            maxWidth: '100%' // Ensure image doesn't overflow container
        };
    }
    return {};
});
</script>

<template>
    <div class="flex flex-col items-start justify-start gap-1.5 text-left text-xs" :style="{ width: customWidth }">
        <div class="self-stretch overflow-hidden flex flex-col items-start justify-start p-1.5 gap-0">
            <InputLabel :for="label" :value="label"
                class="self-stretch font-medium text-xs text-selectedText font-light mb-1" />

            <!-- Show image preview when available -->
            <div v-if="imagePreview || currentImageDisplayed" class="self-stretch flex justify-start items-center">
                <div class="flex justify-start items-center w-full">
                    <div class="relative inline-block">
                        <img :src="imagePreview || (currentImageDisplayed ? `/storage/${currentImageDisplayed}` : '')"
                            :class="imageClass + ' block object-contain rounded-lg shadow-sm'" :style="imageStyle"
                            alt="Preview" ref="previewImage" />
                        <button type="button" @click="deleteImage"
                            class="absolute -top-1.5 -right-1.5 w-3 h-3 rounded-full flex items-center justify-center border border-white cursor-pointer p-0 shadow-sm transition-opacity hover:opacity-90 focus:outline-none focus:ring-1 focus:ring-red-400 bg-dangerText text-white"
                            aria-label="Delete image">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Upload box - only visible when no image -->
            <div v-else class="w-full">
                <input
                    ref="fileInput"
                    :id="label"
                    type="file"
                    :accept="accept"
                    @change="handleImageChange"
                    class="hidden"
                    :required="required"
                />

                <div
                    class="self-stretch bg-menuBG border border-selectedText hover:bg-selectedBG rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] flex flex-col items-center justify-start p-[10.7px] sm:p-[12.1px] md:p-[13.6px] lg:p-[14.8px] xl:p-[16px] 2xl:p-[18.5px] 3xl:p-[21.3px] 4xl:p-[24px] text-[6.7px] sm:text-[7.5px] md:text-[8.5px] lg:text-[9.2px] xl:text-[10px] 2xl:text-[11.5px] 3xl:text-[13.3px] 4xl:text-[15px] cursor-pointer transition-all duration-200 min-h-[60px] sm:min-h-[70px] md:min-h-[80px] lg:min-h-[90px] xl:min-h-[100px] 2xl:min-h-[110px] 3xl:min-h-[120px] 4xl:min-h-[140px] w-full box-border"
                    :class="{ 
                        'bg-blue-50 border-blue-400 border-2 border-dashed shadow-lg': isDragging
                    }"
                    @click="triggerFileInput"
                    @dragover.prevent="handleDragOver"
                    @dragleave.prevent="handleDragLeave"
                    @drop.prevent="handleDrop"
                >
                    <div class="rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] overflow-hidden flex flex-row items-center justify-center p-[2.7px] sm:p-[3px] md:p-[3.4px] lg:p-[3.7px] xl:p-[4px] 2xl:p-[4.6px] 3xl:p-[5.3px] 4xl:p-[6px] gap-[2.2px] sm:gap-[2.5px] md:gap-[2.9px] lg:gap-[3.1px] xl:gap-[3.33px] 2xl:gap-[3.8px] 3xl:gap-[4.4px] 4xl:gap-[5px] cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none"
                            class="w-[8.9px] sm:w-[10.1px] md:w-[11.5px] lg:w-[12.5px] xl:w-[13.3px] 2xl:w-[15.4px] 3xl:w-[17.8px] 4xl:w-[20px] relative max-h-full">
                            <mask id="mask0_1715_7263" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="21"
                                height="20">
                                <rect x="0.5" width="20" height="20" fill="#D9D9D9" />
                            </mask>
                            <g mask="url(#mask0_1715_7263)">
                                <path d="M5.91683 16.6673C4.65294 16.6673 3.57308 16.2298 2.67725 15.3548C1.78141 14.4798 1.3335 13.4104 1.3335 12.1465C1.3335 11.0632 1.65989 10.0979 2.31266 9.25065C2.96544 8.40343 3.81961 7.86176 4.87516 7.62565C5.22239 6.34787 5.91683 5.31315 6.9585 4.52148C8.00016 3.72982 9.18072 3.33398 10.5002 3.33398C12.1252 3.33398 13.5036 3.89996 14.6356 5.0319C15.7675 6.16385 16.3335 7.54232 16.3335 9.16732C17.2918 9.27843 18.087 9.69162 18.7189 10.4069C19.3509 11.1222 19.6668 11.959 19.6668 12.9173C19.6668 13.959 19.3022 14.8444 18.5731 15.5736C17.8439 16.3027 16.9585 16.6673 15.9168 16.6673H11.3335C10.8752 16.6673 10.4828 16.5041 10.1564 16.1777C9.83002 15.8513 9.66683 15.459 9.66683 15.0007V10.709L8.3335 12.0007L7.16683 10.834L10.5002 7.50065L13.8335 10.834L12.6668 12.0007L11.3335 10.709V15.0007H15.9168C16.5002 15.0007 16.9932 14.7993 17.396 14.3965C17.7988 13.9937 18.0002 13.5007 18.0002 12.9173C18.0002 12.334 17.7988 11.8409 17.396 11.4382C16.9932 11.0354 16.5002 10.834 15.9168 10.834H14.6668V9.16732C14.6668 8.01454 14.2606 7.0319 13.4481 6.2194C12.6356 5.4069 11.6529 5.00065 10.5002 5.00065C9.34739 5.00065 8.36475 5.4069 7.55225 6.2194C6.73975 7.0319 6.3335 8.01454 6.3335 9.16732H5.91683C5.11127 9.16732 4.42377 9.45204 3.85433 10.0215C3.28489 10.5909 3.00016 11.2784 3.00016 12.084C3.00016 12.8895 3.28489 13.577 3.85433 14.1465C4.42377 14.7159 5.11127 15.0007 5.91683 15.0007H8.00016V16.6673H5.91683Z"
                                    fill="currentColor" class="text-TableText" />
                            </g>
                        </svg>
                        <div class="relative text-TableText font-medium text-[6.7px] sm:text-[7.5px] md:text-[8.5px] lg:text-[9.2px] xl:text-[10px] 2xl:text-[11.5px] 3xl:text-[13.3px] 4xl:text-[15px]">Upload</div>
                    </div>
                    <div class="self-stretch flex flex-col items-center justify-start p-[2.7px] sm:p-[3px] md:p-[3.4px] lg:p-[3.7px] xl:p-[4px] 2xl:p-[4.6px] 3xl:p-[5.3px] 4xl:p-[6px] gap-[1.3px] sm:gap-[1.5px] md:gap-[1.7px] lg:gap-[1.8px] xl:gap-[2px] 2xl:gap-[2.3px] 3xl:gap-[2.7px] 4xl:gap-[3px] text-center text-[10.6px] sm:text-[12px] md:text-[13.6px] lg:text-[14.6px] xl:text-[16px] 2xl:text-[18.4px] 3xl:text-[21.4px] 4xl:text-[24px] mt-[4.4px] sm:mt-[5px] md:mt-[5.6px] lg:mt-[6.1px] xl:mt-[6.67px] 2xl:mt-[7.7px] 3xl:mt-[8.9px] 4xl:mt-[10px]">
                        <div class="self-stretch relative text-menuText font-medium text-[10.6px] sm:text-[12px] md:text-[13.6px] lg:text-[14.6px] xl:text-[16px] 2xl:text-[18.4px] 3xl:text-[21.4px] 4xl:text-[24px]">
                            Choose images or drag & drop it here
                        </div>
                        <div class="self-stretch relative text-searchIcon text-[8.8px] sm:text-[10px] md:text-[11.2px] lg:text-[12.2px] xl:text-[13.34px] 2xl:text-[15.4px] 3xl:text-[17.8px] 4xl:text-[20px]">
                            JPG, JPEG, and PNG. Max 10 MB
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <InputError :message="error" class="text-xs mt-0.5 text-dangerText" />
    </div>
</template>
