<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import { ref, computed, watch, onMounted } from "vue";

const props = defineProps({
    modelValue: {
        type: File,
        default: null,
    },
    existingVideoUrl: {
        type: String,
        default: null,
    },
    label: {
        type: String,
        default: "Video",
    },
    error: {
        type: String,
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
    showUploadBox: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["update:modelValue", "remove-existing"]);

const localVideo = ref(null);
const fileInput = ref(null);
const isDragging = ref(false);
const videoPreviewUrl = ref(null);
const showExistingVideo = ref(false);

// Initialize local video and existing video
onMounted(() => {
    if (props.modelValue) {
        localVideo.value = props.modelValue;
        videoPreviewUrl.value = URL.createObjectURL(props.modelValue);
    } else if (props.existingVideoUrl) {
        showExistingVideo.value = true;
    }
});

// Watch for changes in existingVideoUrl prop
watch(() => props.existingVideoUrl, (newValue) => {
    if (newValue && !localVideo.value) {
        showExistingVideo.value = true;
    } else {
        showExistingVideo.value = false;
    }
});

// Watch for changes in modelValue prop
watch(() => props.modelValue, (newValue) => {
    console.log('ModelValue changed:', newValue);
    if (newValue) {
        localVideo.value = newValue;
        // Clean up old URL
        if (videoPreviewUrl.value) {
            URL.revokeObjectURL(videoPreviewUrl.value);
        }
        videoPreviewUrl.value = URL.createObjectURL(newValue);
    } else {
        localVideo.value = null;
        if (videoPreviewUrl.value) {
            URL.revokeObjectURL(videoPreviewUrl.value);
        }
        videoPreviewUrl.value = null;
    }
});

const handleVideoChange = (e) => {
    const file = e.target.files[0];
    console.log('File selected:', file);
    processFile(file);
};

const processFile = (file) => {
    if (!file) return;
    
    console.log('Processing file:', file.name, file.type, file.size);
    
    // Validate file type
    const validTypes = ['video/mp4', 'video/ogg', 'video/webm'];
    if (!validTypes.includes(file.type)) {
        alert('Format video tidak didukung. Gunakan MP4, OGG, atau WebM.');
        return;
    }
    
    // Validate file size (max 20MB)
    const maxSize = 20 * 1024 * 1024; // 20MB in bytes
    if (file.size > maxSize) {
        alert('Ukuran video terlalu besar. Maksimal 20MB.');
        return;
    }
    
    // Clean up old URL
    if (videoPreviewUrl.value) {
        URL.revokeObjectURL(videoPreviewUrl.value);
    }
    
    // Hide existing video when new video is selected
    showExistingVideo.value = false;
    
    localVideo.value = file;
    videoPreviewUrl.value = URL.createObjectURL(file);
    
    console.log('Video set:', localVideo.value, videoPreviewUrl.value);
    
    emit("update:modelValue", file);
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
    if (file && fileInput.value) {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.value.files = dataTransfer.files;
    }
};

const removeVideo = () => {
    console.log('Removing video');
    
    // Clean up URL
    if (videoPreviewUrl.value) {
        URL.revokeObjectURL(videoPreviewUrl.value);
    }
    
    localVideo.value = null;
    videoPreviewUrl.value = null;
    
    emit("update:modelValue", null);
    
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const removeExistingVideo = () => {
    showExistingVideo.value = false;
    emit("remove-existing");
};

const triggerFileInput = () => {
    fileInput.value.click();
};

// Expose method to parent
defineExpose({
    triggerFileInput
})

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const hasVideo = computed(() => {
    return localVideo.value || showExistingVideo.value;
});
</script>

<template>
    <div class="p-[5.3px] sm:p-[6px] md:p-[6.7px] lg:p-[7.3px] xl:p-[8px] 2xl:p-[9.3px] 3xl:p-[10.7px] 4xl:p-[12px] flex flex-col items-start justify-start gap-[5.3px] sm:gap-[6px] md:gap-[6.7px] lg:gap-[7.3px] xl:gap-[8px] 2xl:gap-[9.3px] 3xl:gap-[10.7px] 4xl:gap-[12px] text-left text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] border-none">
        <div class="self-stretch overflow-hidden flex flex-col items-start justify-start gap-0">
            <div class="self-stretch text-selectedText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] mb-[4.4px] sm:mb-[5px] md:mb-[5.6px] lg:mb-[6.1px] xl:mb-[6.67px] 2xl:mb-[7.7px] 3xl:mb-[8.9px] 4xl:mb-[10px]">{{ label }}</div>
            
            <input
                ref="fileInput"
                :id="label.toLowerCase()"
                type="file"
                @input="handleVideoChange"
                class="hidden"
                accept="video/mp4,video/ogg,video/webm"
                :required="required"
            />

            <!-- Upload box - only show when no video is selected -->
            <div
                v-if="showUploadBox && !hasVideo"
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 24 24" fill="none"
                        class="w-[8.9px] sm:w-[10.1px] md:w-[11.5px] lg:w-[12.5px] xl:w-[13.3px] 2xl:w-[15.4px] 3xl:w-[17.8px] 4xl:w-[20px] relative max-h-full">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" fill="currentColor" class="text-TableText" opacity="0.3"/>
                        <polyline points="14,2 14,8 20,8" fill="currentColor" class="text-TableText"/>
                        <path d="m10 12 5 3-5 3v-6z" fill="currentColor" class="text-TableText"/>
                    </svg>
                    <div class="relative text-TableText font-medium text-[6.7px] sm:text-[7.5px] md:text-[8.5px] lg:text-[9.2px] xl:text-[10px] 2xl:text-[11.5px] 3xl:text-[13.3px] 4xl:text-[15px]">Upload Video</div>
                </div>
                <div class="self-stretch flex flex-col items-center justify-start p-[2.7px] sm:p-[3px] md:p-[3.4px] lg:p-[3.7px] xl:p-[4px] 2xl:p-[4.6px] 3xl:p-[5.3px] 4xl:p-[6px] gap-[1.3px] sm:gap-[1.5px] md:gap-[1.7px] lg:gap-[1.8px] xl:gap-[2px] 2xl:gap-[2.3px] 3xl:gap-[2.7px] 4xl:gap-[3px] text-center text-[10.6px] sm:text-[12px] md:text-[13.6px] lg:text-[14.6px] xl:text-[16px] 2xl:text-[18.4px] 3xl:text-[21.4px] 4xl:text-[24px] mt-[4.4px] sm:mt-[5px] md:mt-[5.6px] lg:mt-[6.1px] xl:mt-[6.67px] 2xl:mt-[7.7px] 3xl:mt-[8.9px] 4xl:mt-[10px]">
                    <div class="self-stretch relative text-menuText font-medium text-[10.6px] sm:text-[12px] md:text-[13.6px] lg:text-[14.6px] xl:text-[16px] 2xl:text-[18.4px] 3xl:text-[21.4px] 4xl:text-[24px]">
                        Pilih video atau tarik dan lepaskan disini
                    </div>
                    <div class="self-stretch relative text-searchIcon text-[8.8px] sm:text-[10px] md:text-[11.2px] lg:text-[12.2px] xl:text-[13.34px] 2xl:text-[15.4px] 3xl:text-[17.8px] 4xl:text-[20px]">
                        MP4, OGG, dan WebM. Maksimal 20 MB.
                    </div>
                </div>
            </div>

            <!-- Existing Video Display -->
            <div v-if="showExistingVideo && existingVideoUrl" class="self-stretch bg-menuBG border border-gray-300 rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] p-[8px] sm:p-[9px] md:p-[10px] lg:p-[11px] xl:p-[12px] 2xl:p-[14px] 3xl:p-[16px] 4xl:p-[18px]">
                <div class="relative w-full">
                    <video
                        :src="existingVideoUrl"
                        class="w-full h-[200px] sm:h-[220px] md:h-[240px] lg:h-[260px] xl:h-[280px] 2xl:h-[300px] 3xl:h-[320px] 4xl:h-[340px] object-cover rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] shadow-sm"
                        controls
                        preload="metadata"
                    />
                    <button
                        type="button"
                        @click="removeExistingVideo"
                        class="bg-dangerText text-white absolute -top-[5.3px] sm:-top-[6px] md:-top-[6.7px] lg:-top-[7.3px] xl:-top-[8px] 2xl:-top-[9.3px] 3xl:-top-[10.7px] 4xl:-top-[12px] -right-[5.3px] sm:-right-[6px] md:-right-[6.7px] lg:-right-[7.3px] xl:-right-[8px] 2xl:-right-[9.3px] 3xl:-right-[10.7px] 4xl:-right-[12px] w-[10.7px] sm:w-[12px] md:w-[13.6px] lg:w-[14.7px] xl:w-[16px] 2xl:w-[18.5px] 3xl:w-[21.3px] 4xl:w-[24px] h-[10.7px] sm:h-[12px] md:h-[13.6px] lg:h-[14.7px] xl:h-[16px] 2xl:h-[18.5px] 3xl:h-[21.3px] 4xl:h-[24px] rounded-full flex items-center justify-center border border-white cursor-pointer p-0 shadow-sm transition-opacity duration-200 hover:opacity-90 focus:outline-none focus:ring-1 focus:ring-red-400"
                        aria-label="Delete existing video"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="mt-[8px] sm:mt-[9px] md:mt-[10px] lg:mt-[11px] xl:mt-[12px] 2xl:mt-[14px] 3xl:mt-[16px] 4xl:mt-[18px] text-selectedText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                    <div class="font-medium">Video Saat Ini</div>
                </div>
                
                <!-- Replace existing video button -->
                <button
                    type="button"
                    @click="triggerFileInput"
                    class="mt-[8px] sm:mt-[9px] md:mt-[10px] lg:mt-[11px] xl:mt-[12px] 2xl:mt-[14px] 3xl:mt-[16px] 4xl:mt-[18px] bg-selectedText text-white px-[8px] sm:px-[9px] md:px-[10px] lg:px-[11px] xl:px-[12px] 2xl:px-[14px] 3xl:px-[16px] 4xl:px-[18px] py-[4px] sm:py-[5px] md:py-[6px] lg:py-[7px] xl:py-[8px] 2xl:py-[9px] 3xl:py-[10px] 4xl:py-[12px] rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] text-[7px] sm:text-[8px] md:text-[9px] lg:text-[10px] xl:text-[11px] 2xl:text-[13px] 3xl:text-[15px] 4xl:text-[17px] hover:opacity-90 transition-opacity"
                >
                    Ganti Video
                </button>
            </div>

            <!-- New Video preview - show when new video is selected -->
            <div v-if="localVideo && videoPreviewUrl" class="self-stretch bg-menuBG border border-gray-300 rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] p-[8px] sm:p-[9px] md:p-[10px] lg:p-[11px] xl:p-[12px] 2xl:p-[14px] 3xl:p-[16px] 4xl:p-[18px]">
                <div class="relative w-full">
                    <video
                        :src="videoPreviewUrl"
                        class="w-full h-[200px] sm:h-[220px] md:h-[240px] lg:h-[260px] xl:h-[280px] 2xl:h-[300px] 3xl:h-[320px] 4xl:h-[340px] object-cover rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] shadow-sm"
                        controls
                        preload="metadata"
                        @error="console.error('Video error:', $event)"
                        @loadstart="console.log('Video loadstart')"
                        @loadeddata="console.log('Video loaded')"
                    />
                    <button
                        type="button"
                        @click="removeVideo"
                        class="bg-dangerText text-white absolute -top-[5.3px] sm:-top-[6px] md:-top-[6.7px] lg:-top-[7.3px] xl:-top-[8px] 2xl:-top-[9.3px] 3xl:-top-[10.7px] 4xl:-top-[12px] -right-[5.3px] sm:-right-[6px] md:-right-[6.7px] lg:-right-[7.3px] xl:-right-[8px] 2xl:-right-[9.3px] 3xl:-right-[10.7px] 4xl:-right-[12px] w-[10.7px] sm:w-[12px] md:w-[13.6px] lg:w-[14.7px] xl:w-[16px] 2xl:w-[18.5px] 3xl:w-[21.3px] 4xl:w-[24px] h-[10.7px] sm:h-[12px] md:h-[13.6px] lg:h-[14.7px] xl:h-[16px] 2xl:h-[18.5px] 3xl:h-[21.3px] 4xl:h-[24px] rounded-full flex items-center justify-center border border-white cursor-pointer p-0 shadow-sm transition-opacity duration-200 hover:opacity-90 focus:outline-none focus:ring-1 focus:ring-red-400"
                        aria-label="Delete video"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="mt-[8px] sm:mt-[9px] md:mt-[10px] lg:mt-[11px] xl:mt-[12px] 2xl:mt-[14px] 3xl:mt-[16px] 4xl:mt-[18px] text-selectedText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                    <div class="font-medium">{{ localVideo.name }}</div>
                    <div class="text-searchIcon text-[7px] sm:text-[8px] md:text-[9px] lg:text-[10px] xl:text-[11px] 2xl:text-[13px] 3xl:text-[15px] 4xl:text-[17px] mt-1">
                        {{ formatFileSize(localVideo.size) }}
                    </div>
                </div>
            </div>

            <InputError :message="error" class="text-dangerText text-[6.2px] sm:text-[7px] md:text-[8px] lg:text-[8.7px] xl:text-[9.33px] 2xl:text-[10.8px] 3xl:text-[12.4px] 4xl:text-[14px] mt-[1.8px] sm:mt-[2px] md:mt-[2.2px] lg:mt-[2.4px] xl:mt-[2.67px] 2xl:mt-[3.1px] 3xl:mt-[3.6px] 4xl:mt-[4px]" />
        </div>
    </div>
</template>
