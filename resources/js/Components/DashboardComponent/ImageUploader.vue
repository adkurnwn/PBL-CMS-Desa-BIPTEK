<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import { ref, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    alts: {
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
    maxImages: {
        type: Number,
        default: 25,
    },
    existingImagesCount: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(["update:modelValue", "update:alts"]);

const URL = window.URL;
const localImages = ref([]);
const localAlts = ref([...props.alts]);
const fileInput = ref(null);
const isDragging = ref(false);

// New computed property to handle image-specific errors
const imageErrors = computed(() => {
    if (!props.error) return {};

    const errors = {};
    // Check for image-specific errors like images.0, images.1, etc.
    if (typeof props.error === "object") {
        Object.keys(props.error).forEach((key) => {
            if (key.startsWith("images.")) {
                const index = parseInt(key.split(".")[1]);
                if (!isNaN(index)) {
                    errors[index] = props.error[key];
                }
            }
        });
    }
    return errors;
});

const generalImageError = computed(() => {
    return typeof props.error === "string"
        ? props.error
        : props.error?.images || "";
});

const handleImagesChange = (e) => {
    const files = Array.from(e.target.files);
    processFiles(files);
};

const processFiles = (files) => {
    if (!files || files.length === 0) return;
    
    // Check image limit using prop
    const currentCount = localImages.value.length + props.existingImagesCount;
    const availableSlots = props.maxImages - currentCount;
    
    if (availableSlots <= 0) {
        alert(`Maksimal ${props.maxImages} gambar yang diizinkan`);
        return;
    }
    
    // Validate file types - match server validation exactly
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    const validFiles = files.filter(file => validTypes.includes(file.type));
    
    if (validFiles.length === 0) return;
    
    // Validate file sizes (max 10MB each)
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    const validSizedFiles = validFiles.filter(file => file.size <= maxSize);
    
    if (validSizedFiles.length === 0) return;
    
    // Limit files to available slots
    const filesToAdd = validSizedFiles.slice(0, availableSlots);
    
    if (filesToAdd.length < validSizedFiles.length) {
        alert(`Hanya ${filesToAdd.length} gambar yang dapat ditambahkan. Maksimal ${maxImages} gambar total.`);
    }
    
    // Append new images to existing ones
    const newImages = [...localImages.value, ...filesToAdd];
    localImages.value = newImages;

    // Initialize alt texts for new images
    const newAlts = [...localAlts.value];
    filesToAdd.forEach((_, index) => {
        newAlts.push("");
    });
    localAlts.value = newAlts;

    emit("update:modelValue", newImages);
    emit("update:alts", newAlts);
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
    const files = Array.from(e.dataTransfer.files);
    processFiles(files);
    
    // Update the file input for form submission - crucial for server validation
    if (files.length > 0 && fileInput.value) {
        const dataTransfer = new DataTransfer();
        // Only add valid files to the input
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        const validFiles = files.filter(file => validTypes.includes(file.type));
        validFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.value.files = dataTransfer.files;
    }
};

const updateAltText = (index, value) => {
    localAlts.value[index] = value;
    emit("update:alts", localAlts.value);
};

const removeImage = (index) => {
    const newImages = [...localImages.value];
    newImages.splice(index, 1);
    localImages.value = newImages;

    const newAlts = [...localAlts.value];
    newAlts.splice(index, 1);
    localAlts.value = newAlts;

    emit("update:modelValue", newImages);
    emit("update:alts", newAlts);
};

const triggerFileInput = () => {
    fileInput.value.click();
};
</script>

<template>
    <div class="flex flex-col items-start justify-start gap-[5.3px] sm:gap-[6px] md:gap-[6.7px] lg:gap-[7.3px] xl:gap-[8px] 2xl:gap-[9.3px] 3xl:gap-[10.7px] 4xl:gap-[12px] text-left text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] border-none">
        <div class="self-stretch overflow-hidden flex flex-col items-start justify-start gap-0">
            <div class="self-stretch text-selectedText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] mb-[4.4px] sm:mb-[5px] md:mb-[5.6px] lg:mb-[6.1px] xl:mb-[6.67px] 2xl:mb-[7.7px] 3xl:mb-[8.9px] 4xl:mb-[10px]">{{ label }}</div>
            <input
                ref="fileInput"
                :id="label.toLowerCase()"
                type="file"
                @input="handleImagesChange"
                multiple
                class="hidden"
                accept="image/png,image/jpeg,image/jpg,image/webp"
                :required="required"
            />

            <!-- Upload box - always visible -->
            <div
                class="self-stretch bg-menuBG border border-selectedText hover:bg-selectedBG rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] flex flex-col items-center justify-start p-[10.7px] sm:p-[12.1px] md:p-[13.6px] lg:p-[14.8px] xl:p-[16px] 2xl:p-[18.5px] 3xl:p-[21.3px] 4xl:p-[24px] text-[6.7px] sm:text-[7.5px] md:text-[8.5px] lg:text-[9.2px] xl:text-[10px] 2xl:text-[11.5px] 3xl:text-[13.3px] 4xl:text-[15px] cursor-pointer transition-all duration-200 min-h-[60px] sm:min-h-[70px] md:min-h-[80px] lg:min-h-[90px] xl:min-h-[100px] 2xl:min-h-[110px] 3xl:min-h-[120px] 4xl:min-h-[140px] w-full box-border"
                :class="{ 
                    'bg-blue-50 border-blue-400 border-2 border-dashed shadow-lg': isDragging,
                    'opacity-50 cursor-not-allowed': (modelValue.length + existingImagesCount) >= maxImages
                }"
                @click="(modelValue.length + existingImagesCount) < maxImages ? triggerFileInput() : null"
                @dragover.prevent="(modelValue.length + existingImagesCount) < maxImages ? handleDragOver($event) : null"
                @dragleave.prevent="handleDragLeave"
                @drop.prevent="(modelValue.length + existingImagesCount) < maxImages ? handleDrop($event) : null"
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
                        <span v-if="(modelValue.length + existingImagesCount) >= maxImages">Maksimal {{ maxImages }} gambar tercapai</span>
                        <span v-else-if="modelValue.length === 0">Pilih gambar atau tarik dan lepaskan disini</span>
                        <span v-else>Tambah gambar lagi</span>
                    </div>
                    <div class="self-stretch relative text-searchIcon text-[8.8px] sm:text-[10px] md:text-[11.2px] lg:text-[12.2px] xl:text-[13.34px] 2xl:text-[15.4px] 3xl:text-[17.8px] 4xl:text-[20px]">
                        <span v-if="(modelValue.length + existingImagesCount) >= maxImages">Hapus beberapa gambar untuk menambah yang baru</span>
                        <span v-else>JPG, JPEG, dan PNG. Maksimal 10 MB pergambar ({{ modelValue.length + existingImagesCount }}/{{ maxImages }})</span>
                    </div>
                </div>
            </div>

            <!-- General error for images field -->
            <InputError :message="generalImageError" class="text-dangerText text-[6.2px] sm:text-[7px] md:text-[8px] lg:text-[8.7px] xl:text-[9.33px] 2xl:text-[10.8px] 3xl:text-[12.4px] 4xl:text-[14px] mt-[1.8px] sm:mt-[2px] md:mt-[2.2px] lg:mt-[2.4px] xl:mt-[2.67px] 2xl:mt-[3.1px] 3xl:mt-[3.6px] 4xl:mt-[4px]" />

            <!-- Image previews with alt inputs -->
            <div v-if="modelValue.length > 0" class="self-stretch grid grid-cols-2 md:grid-cols-3 gap-[5.3px] sm:gap-[6px] md:gap-[6.7px] lg:gap-[7.3px] xl:gap-[8px] 2xl:gap-[9.3px] 3xl:gap-[10.7px] 4xl:gap-[12px] m-0 w-full mt-[4.4px] sm:mt-[5px] md:mt-[5.6px] lg:mt-[6.1px] xl:mt-[6.67px] 2xl:mt-[7.7px] 3xl:mt-[8.9px] 4xl:mt-[10px]">
                <div
                    v-for="(image, index) in modelValue"
                    :key="index"
                    class="relative rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] p-[4px] sm:p-[4.5px] md:p-[5px] lg:p-[5.5px] xl:p-[6px] 2xl:p-[7px] 3xl:p-[8px] 4xl:p-[9px] bg-menuBG border border-gray-300"
                    :class="{ 'border-red-500': imageErrors[index] }"
                >
                    <div class="relative w-full mb-[2.7px] sm:mb-[3px] md:mb-[3.4px] lg:mb-[3.7px] xl:mb-[4px] 2xl:mb-[4.6px] 3xl:mb-[5.3px] 4xl:mb-[6px] flex justify-center items-center">
                        <img
                            :src="URL.createObjectURL(image)"
                            class="w-full h-[80px] sm:h-[90px] md:h-[100px] lg:h-[110px] xl:h-[120px] 2xl:h-[130px] 3xl:h-[140px] 4xl:h-[150px] object-cover rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] shadow-sm"
                            @load="URL.revokeObjectURL(image)"
                        />
                        <button
                            type="button"
                            @click="removeImage(index)"
                            class="bg-dangerText text-white absolute -top-[5.3px] sm:-top-[6px] md:-top-[6.7px] lg:-top-[7.3px] xl:-top-[8px] 2xl:-top-[9.3px] 3xl:-top-[10.7px] 4xl:-top-[12px] -right-[5.3px] sm:-right-[6px] md:-right-[6.7px] lg:-right-[7.3px] xl:-right-[8px] 2xl:-right-[9.3px] 3xl:-right-[10.7px] 4xl:-right-[12px] w-[10.7px] sm:w-[12px] md:w-[13.6px] lg:w-[14.7px] xl:w-[16px] 2xl:w-[18.5px] 3xl:w-[21.3px] 4xl:w-[24px] h-[10.7px] sm:h-[12px] md:h-[13.6px] lg:h-[14.7px] xl:h-[16px] 2xl:h-[18.5px] 3xl:h-[21.3px] 4xl:h-[24px] rounded-full flex items-center justify-center border border-white cursor-pointer p-0 shadow-sm transition-opacity duration-200 hover:opacity-90 focus:outline-none focus:ring-1 focus:ring-red-400"
                            aria-label="Delete image"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-col mt-[2.7px] sm:mt-[3px] md:mt-[3.4px] lg:mt-[3.7px] xl:mt-[4px] 2xl:mt-[4.6px] 3xl:mt-[5.3px] 4xl:mt-[6px]">
                        <template v-if="altText">
                            <InputLabel
                                :for="'alt-' + index"
                                :value="`Alt Text ${index + 1}`"
                                class="text-selectedText font-light text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] mb-[4.4px] sm:mb-[5px] md:mb-[5.6px] lg:mb-[6.1px] xl:mb-[6.67px] 2xl:mb-[7.7px] 3xl:mb-[8.9px] 4xl:mb-[10px]"
                            />
                            <TextInput
                                :id="'alt-' + index"
                                v-model="localAlts[index]"
                                @input="(e) => updateAltText(index, e.target.value)"
                                type="text"
                                class="text-TableText bg-menuBG border border-selectedText border-solid focus:outline-none focus:border-2 focus:border-selectedText w-full h-[20.7px] sm:h-[23.5px] md:h-[26.3px] lg:h-[28.7px] xl:h-[31px] 2xl:h-[35.3px] 3xl:h-[40px] 4xl:h-[46px] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] p-[5.3px] sm:p-[6px] md:p-[6.7px] lg:p-[7.3px] xl:p-[8px] 2xl:p-[9.3px] 3xl:p-[10.7px] 4xl:p-[12px] rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] mt-[1.3px] sm:mt-[1.5px] md:mt-[1.7px] lg:mt-[1.8px] xl:mt-[2px] 2xl:mt-[2.3px] 3xl:mt-[2.7px] 4xl:mt-[3px] transition-all duration-200 box-border"
                                :placeholder="defaultAltText || 'Describe this image'"
                            />
                        </template>
                        <InputError :message="imageErrors[index]" class="text-dangerText text-[6.2px] sm:text-[7px] md:text-[8px] lg:text-[8.7px] xl:text-[9.33px] 2xl:text-[10.8px] 3xl:text-[12.4px] 4xl:text-[14px] mt-[1.8px] sm:mt-[2px] md:mt-[2.2px] lg:mt-[2.4px] xl:mt-[2.67px] 2xl:mt-[3.1px] 3xl:mt-[3.6px] 4xl:mt-[4px]" />
                    </div>
                </div>
            </div>

            <InputError v-if="altText" :message="altError" class="text-dangerText text-[6.2px] sm:text-[7px] md:text-[8px] lg:text-[8.7px] xl:text-[9.33px] 2xl:text-[10.8px] 3xl:text-[12.4px] 4xl:text-[14px] mt-[1.8px] sm:mt-[2px] md:mt-[2.2px] lg:mt-[2.4px] xl:mt-[2.67px] 2xl:mt-[3.1px] 3xl:mt-[3.6px] 4xl:mt-[4px]" />
        </div>
    </div>
</template>

<style scoped>
/* Remove all existing styles since we're using Tailwind classes */
</style>
