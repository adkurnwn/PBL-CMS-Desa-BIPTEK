<template>
    <div class="photo-upload-container" :style="{ width: customWidth }">
        <div class="upload-section">
            <div class="field-name text-selectedText">Foto Profil</div>
            <div 
                class="upload-box bg-menuBG border border-selectedText hover:bg-selectedBG" 
                :class="{ 'drag-over': isDragging }"
                @click="triggerFileInput"
                @dragover.prevent="handleDragOver"
                @dragleave.prevent="handleDragLeave"
                @drop.prevent="handleDrop"
            >
                <label for="photo-upload" class="upload-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none"
                        class="upload-icon">
                        <mask id="mask0_1715_7263" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="21"
                            height="20">
                            <rect x="0.5" width="20" height="20" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1715_7263)">
                            <path d="M5.91683 16.6673C4.65294 16.6673 3.57308 16.2298 2.67725 15.3548C1.78141 14.4798 1.3335 13.4104 1.3335 12.1465C1.3335 11.0632 1.65989 10.0979 2.31266 9.25065C2.96544 8.40343 3.81961 7.86176 4.87516 7.62565C5.22239 6.34787 5.91683 5.31315 6.9585 4.52148C8.00016 3.72982 9.18072 3.33398 10.5002 3.33398C12.1252 3.33398 13.5036 3.89996 14.6356 5.0319C15.7675 6.16385 16.3335 7.54232 16.3335 9.16732C17.2918 9.27843 18.087 9.69162 18.7189 10.4069C19.3509 11.1222 19.6668 11.959 19.6668 12.9173C19.6668 13.959 19.3022 14.8444 18.5731 15.5736C17.8439 16.3027 16.9585 16.6673 15.9168 16.6673H11.3335C10.8752 16.6673 10.4828 16.5041 10.1564 16.1777C9.83002 15.8513 9.66683 15.459 9.66683 15.0007V10.709L8.3335 12.0007L7.16683 10.834L10.5002 7.50065L13.8335 10.834L12.6668 12.0007L11.3335 10.709V15.0007H15.9168C16.5002 15.0007 16.9932 14.7993 17.396 14.3965C17.7988 13.9937 18.0002 13.5007 18.0002 12.9173C18.0002 12.334 17.7988 11.8409 17.396 11.4382C16.9932 11.0354 16.5002 10.834 15.9168 10.834H14.6668V9.16732C14.6668 8.01454 14.2606 7.0319 13.4481 6.2194C12.6356 5.4069 11.6529 5.00065 10.5002 5.00065C9.34739 5.00065 8.36475 5.4069 7.55225 6.2194C6.73975 7.0319 6.3335 8.01454 6.3335 9.16732H5.91683C5.11127 9.16732 4.42377 9.45204 3.85433 10.0215C3.28489 10.5909 3.00016 11.2784 3.00016 12.084C3.00016 12.8895 3.28489 13.577 3.85433 14.1465C4.42377 14.7159 5.11127 15.0007 5.91683 15.0007H8.00016V16.6673H5.91683Z"
                                fill="currentColor" class="text-TableText" />
                        </g>
                    </svg>
                    <div class="upload-text text-TableText font-medium">Upload</div>
                </label>
                <input 
                    type="file" 
                    id="photo-upload" 
                    accept="image/png, image/jpeg, image/jpg"
                    @change="handleFileUpload"
                    class="hidden"
                    :required="required"
                    ref="fileInput"
                />
                <div class="instructions">
                    <div class="instructions-title text-menuText font-medium">Choose images or drag & drop it here</div>
                    <div class="instructions-subtitle text-searchIcon">JPG, JPEG, and PNG. Max 10 MB</div>
                </div>
            </div>
        </div>
        <!-- Only show preview container when there's an image to preview -->
        <div v-if="previewUrl && previewUrl.trim() !== ''" class="preview-container">
            <div class="image-wrapper">
                <img :src="previewUrl" alt="Preview" class="preview-image" />
                <button 
                    type="button" 
                    @click="deleteImage" 
                    class="delete-button bg-dangerText text-white"
                    aria-label="Delete image"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
        <div v-if="errorMessage" class="error-message text-dangerText">{{ errorMessage }}</div>
    </div>
</template>

<script>
export default {
    name: 'PhotoProfileUpload',
    props: {
        modelValue: {
            type: [File, null],
            default: null
        },
        previewImage: {
            type: String,
            default: ''
        },
        customWidth: {
            type: String,
            default: '100%'
        },
        required: {
            type: Boolean,
            default: false
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            previewUrl: this.previewImage || '',
            errorMessage: '',
            isDragging: false
        }
    },
    methods: {
        // Trigger file input click
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        
        // Handle file validation and processing
        processFile(file) {
            if (!file) return;
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                this.errorMessage = 'Please select a valid image file (JPG, JPEG, or PNG)';
                return;
            }
            
            // Validate file size (max 10MB)
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if (file.size > maxSize) {
                this.errorMessage = 'File size exceeds 10MB limit';
                return;
            }
            
            // Clear any previous errors
            this.errorMessage = '';
            
            // Create preview URL
            this.previewUrl = URL.createObjectURL(file);
            
            // Emit the file to parent component
            this.$emit('update:modelValue', file);
        },
        
        // Handle file upload from input
        handleFileUpload(event) {
            const file = event.target.files[0];
            this.processFile(file);
        },
        
        // Handle drag events
        handleDragOver(event) {
            this.isDragging = true;
        },
        
        handleDragLeave(event) {
            this.isDragging = false;
        },
        
        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            this.processFile(file);
            
            // Update the file input for form submission
            if (file) {
                // Create a DataTransfer object to set files on the input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                this.$refs.fileInput.files = dataTransfer.files;
            }
        },
        
        // Delete image method
        deleteImage() {
            // Clear the preview
            this.previewUrl = '';
            
            // Clear any object URLs to avoid memory leaks
            if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
                URL.revokeObjectURL(this.previewUrl);
            }
            
            // Emit null to clear the model value
            this.$emit('update:modelValue', null);
            
            // Reset the file input by creating a new FileList
            const fileInput = document.getElementById('photo-upload');
            if (fileInput) {
                fileInput.value = '';
            }
        }
    },
    watch: {
        previewImage(newValue) {
            if (newValue && !this.previewUrl) {
                this.previewUrl = newValue;
            }
        }
    },
    mounted() {
        // Validate preview image on mount to prevent showing empty/invalid image sources
        if (this.previewImage && this.previewImage.trim() !== '') {
            // Check if image is loadable
            const img = new Image();
            img.onload = () => {
                this.previewUrl = this.previewImage;
            };
            img.onerror = () => {
                console.warn('Unable to load preview image:', this.previewImage);
                this.previewUrl = '';
            };
            img.src = this.previewImage;
        }
    },
    beforeUnmount() {
        // Clean up any object URLs to avoid memory leaks
        if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
            URL.revokeObjectURL(this.previewUrl);
        }
    }
}
</script>

<style scoped>
.photo-upload-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 5.3px;
    text-align: left;
    font-size: 8px;
}

.upload-section {
    align-self: stretch;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 5.3px;
    gap: 0; /* Set gap to 0 for base size */
}

.field-name {
    align-self: stretch;
    font-weight: 500;
    font-size: 8px;
    margin-bottom: 4.4px;
}

.upload-box {
    align-self: stretch;
    border-radius: 2.2px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 10.7px;
    font-size: 6.7px;
    cursor: pointer;
    transition: background-color 0.2s, border-color 0.2s;
    height: 37.3px;
}

.drag-over {
    background-color: #e6f7ff;
    border: 2px dashed var(--selectedText);
    box-shadow: 0 0 10px rgba(76, 118, 96, 0.2);
}

.upload-button {
    border-radius: 2.2px;
    overflow: hidden;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    padding: 2.7px 5.3px;
    gap: 2.2px;
    cursor: pointer;
}

.upload-icon {
    width: 8.9px;
    position: relative;
    max-height: 100%;
}

.upload-text {
    position: relative;
    font-size: 6.7px;
}

.hidden {
    display: none;
}

.instructions {
    align-self: stretch;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 2.7px 5.3px;
    gap: 1.3px;
    text-align: center;
    font-size: 5.3px;
    margin-top: 4.4px;
}

.instructions-title {
    align-self: stretch;
    position: relative;
    font-size: 5.3px;
}

.instructions-subtitle {
    align-self: stretch;
    position: relative;
    font-size: 4.4px;
}

.preview-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 4.4px;
}

.image-wrapper {
    position: relative;
    display: inline-block;
}

.preview-image {
    width: 70.2px;
    height: 94.2px;
    object-fit: cover;
    border-radius: 7.1px;
    box-shadow: 0px 1.3px 1.3px 0px rgba(0, 0, 0, 0.2);
}

.delete-button {
    position: absolute;
    top: -5.3px;
    right: -5.3px;
    width: 10.7px;
    height: 10.7px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid white;
    cursor: pointer;
    padding: 0;
    box-shadow: 0 0.9px 1.8px rgba(0, 0, 0, 0.2);
    transition: background-color 0.2s;
}

.delete-button:hover {
    opacity: 0.9;
}

.delete-button:focus {
    outline: none;
    box-shadow: 0 0 0 1px rgba(220, 20, 60, 0.5);
}

.error-message {
    font-size: 6.2px;
    margin-top: 1.8px;
}

/* Responsive styles using media queries */
@media (min-width: 640px) { /* sm */
    .photo-upload-container {
        gap: 6px;
        font-size: 9px;
    }
    
    .upload-section {
        padding: 6px;
        gap: 0; /* Set gap to 0 for sm */
    }
    
    .field-name {
        font-size: 9px;
        margin-bottom: 5px;
    }
    
    .upload-box {
        border-radius: 2.5px;
        padding: 12.1px;
        font-size: 7.5px;
        height: 42.3px;
    }
    
    .upload-button {
        border-radius: 2.5px;
        padding: 3px 6px;
        gap: 2.5px;
    }
    
    .upload-icon {
        width: 10.1px;
    }
    
    .upload-text {
        font-size: 7.5px;
    }
    
    .instructions {
        padding: 3px 6px;
        gap: 1.5px;
        font-size: 6px;
        margin-top: 5px;
    }
    
    .instructions-title {
        font-size: 6px;
    }
    
    .instructions-subtitle {
        font-size: 5px;
    }
    
    .preview-container {
        margin-top: 5px;
    }
    
    .preview-image {
        width: 79.5px;
        height: 106.5px;
        border-radius: 8px;
    }
    
    .delete-button {
        top: -6px;
        right: -6px;
        width: 12px;
        height: 12px;
    }
    
    .error-message {
        font-size: 7px;
        margin-top: 2px;
    }
}

@media (min-width: 768px) { /* md */
    .photo-upload-container {
        gap: 6.7px;
        font-size: 10px;
    }
    
    .upload-section {
        padding: 6.7px;
        gap: 0; /* Set gap to 0 for md */
    }
    
    .field-name {
        font-size: 10px;
        margin-bottom: 5.6px;
    }
    
    .upload-box {
        border-radius: 2.9px;
        padding: 13.6px;
        font-size: 8.5px;
        height: 48px;
    }
    
    .upload-button {
        border-radius: 2.9px;
        padding: 3.4px 6.7px;
        gap: 2.9px;
    }
    
    .upload-icon {
        width: 11.5px;
    }
    
    .upload-text {
        font-size: 8.5px;
    }
    
    .instructions {
        padding: 3.4px 6.7px;
        gap: 1.7px;
        font-size: 6.8px;
        margin-top: 5.6px;
    }
    
    .instructions-title {
        font-size: 6.8px;
    }
    
    .instructions-subtitle {
        font-size: 5.6px;
    }
    
    .preview-container {
        margin-top: 5.6px;
    }
    
    .preview-image {
        width: 90.3px;
        height: 121px;
        border-radius: 9.1px;
    }
    
    .delete-button {
        top: -6.7px;
        right: -6.7px;
        width: 13.6px;
        height: 13.6px;
    }
    
    .error-message {
        font-size: 8px;
        margin-top: 2.2px;
    }
}

@media (min-width: 1024px) { /* lg */
    .photo-upload-container {
        gap: 7.3px;
        font-size: 11px;
    }
    
    .upload-section {
        padding: 7.3px;
        gap: 0; /* Set gap to 0 for lg */
    }
    
    .field-name {
        font-size: 11px;
        margin-bottom: 6.1px;
    }
    
    .upload-box {
        border-radius: 3.1px;
        padding: 14.8px;
        font-size: 9.2px;
        height: 52px;
    }
    
    .upload-button {
        border-radius: 3.1px;
        padding: 3.7px 7.3px;
        gap: 3.1px;
    }
    
    .upload-icon {
        width: 12.5px;
    }
    
    .upload-text {
        font-size: 9.2px;
    }
    
    .instructions {
        padding: 3.7px 7.3px;
        gap: 1.8px;
        font-size: 7.3px;
        margin-top: 6.1px;
    }
    
    .instructions-title {
        font-size: 7.3px;
    }
    
    .instructions-subtitle {
        font-size: 6.1px;
    }
    
    .preview-container {
        margin-top: 6.1px;
    }
    
    .preview-image {
        width: 98px;
        height: 131.6px;
        border-radius: 10px;
    }
    
    .delete-button {
        top: -7.3px;
        right: -7.3px;
        width: 14.7px;
        height: 14.7px;
    }
    
    .error-message {
        font-size: 8.7px;
        margin-top: 2.4px;
    }
}

@media (min-width: 1280px) { /* xl */
    .photo-upload-container {
        gap: 8px;
        font-size: 12px;
    }
    
    .upload-section {
        padding: 8px;
        gap: 0; /* Removed the gap that was showing as purple in inspector */
    }
    
    .field-name {
        font-size: 12px;
        margin-bottom: 6.67px;
    }
    
    .upload-box {
        border-radius: 3.33px;
        padding: 16px;
        font-size: 10px;
        height: 83px; /* Exactly 83px at xl breakpoint as specified */
    }
    
    .upload-button {
        border-radius: 3.33px;
        padding: 4px 8px;
        gap: 3.33px;
    }
    
    .upload-icon {
        width: 13.3px;
    }
    
    .upload-text {
        font-size: 10px;
    }
    
    .instructions {
        padding: 4px 8px;
        gap: 2px;
        font-size: 8px;
        margin-top: 6.67px;
    }
    
    .instructions-title {
        font-size: 8px;
    }
    
    .instructions-subtitle {
        font-size: 6.67px;
    }
    
    .preview-container {
        margin-top: 6.67px;
    }
    
    .preview-image {
        width: 104.7px;
        height: 140.7px;
        border-radius: 10.7px;
    }
    
    .delete-button {
        top: -8px;
        right: -8px;
        width: 16px;
        height: 16px;
    }
    
    .error-message {
        font-size: 9.33px;
        margin-top: 2.67px;
    }
}

@media (min-width: 1536px) { /* 2xl */
    .photo-upload-container {
        gap: 9.3px;
        font-size: 14px;
    }
    
    .upload-section {
        padding: 9.3px;
        gap: 0; /* Set gap to 0 for 2xl */
    }
    
    .field-name {
        font-size: 14px;
        margin-bottom: 7.7px;
    }
    
    .upload-box {
        border-radius: 3.8px;
        padding: 18.5px;
        font-size: 11.5px;
        height: 96px;
    }
    
    .upload-button {
        border-radius: 3.8px;
        padding: 4.6px 9.3px;
        gap: 3.8px;
    }
    
    .upload-icon {
        width: 15.4px;
    }
    
    .upload-text {
        font-size: 11.5px;
    }
    
    .instructions {
        padding: 4.6px 9.3px;
        gap: 2.3px;
        font-size: 9.2px;
        margin-top: 7.7px;
    }
    
    .instructions-title {
        font-size: 9.2px;
    }
    
    .instructions-subtitle {
        font-size: 7.7px;
    }
    
    .preview-container {
        margin-top: 7.7px;
    }
    
    .preview-image {
        width: 121px;
        height: 162.5px;
        border-radius: 12.3px;
    }
    
    .delete-button {
        top: -9.3px;
        right: -9.3px;
        width: 18.5px;
        height: 18.5px;
    }
    
    .error-message {
        font-size: 10.8px;
        margin-top: 3.1px;
    }
}

@media (min-width: 1920px) { /* 3xl */
    .photo-upload-container {
        gap: 10.7px;
        font-size: 16px;
    }
    
    .upload-section {
        padding: 10.7px;
        gap: 0; /* Set gap to 0 for 3xl */
    }
    
    .field-name {
        font-size: 16px;
        margin-bottom: 8.9px;
    }
    
    .upload-box {
        border-radius: 4.4px;
        padding: 21.3px;
        font-size: 13.3px;
        height: 111px;
    }
    
    .upload-button {
        border-radius: 4.4px;
        padding: 5.3px 10.7px;
        gap: 4.4px;
    }
    
    .upload-icon {
        width: 17.8px;
    }
    
    .upload-text {
        font-size: 13.3px;
    }
    
    .instructions {
        padding: 5.3px 10.7px;
        gap: 2.7px;
        font-size: 10.7px;
        margin-top: 8.9px;
    }
    
    .instructions-title {
        font-size: 10.7px;
    }
    
    .instructions-subtitle {
        font-size: 8.9px;
    }
    
    .preview-container {
        margin-top: 8.9px;
    }
    
    .preview_image {
        width: 138.2px;
        height: 185.7px;
        border-radius: 14px;
    }
    
    .delete-button {
        top: -10.7px;
        right: -10.7px;
        width: 21.3px;
        height: 21.3px;
    }
    
    .error-message {
        font-size: 12.4px;
        margin-top: 3.6px;
    }
}

@media (min-width: 2560px) { /* 4xl */
    .photo-upload-container {
        gap: 12px;
        font-size: 18px;
    }
    
    .upload-section {
        padding: 12px;
        gap: 0; /* Set gap to 0 for 4xl */
    }
    
    .field-name {
        font-size: 18px;
        margin-bottom: 10px;
    }
    
    .upload-box {
        border-radius: 5px;
        padding: 24px;
        font-size: 15px;
        height: 126px;
    }
    
    .upload-button {
        border-radius: 5px;
        padding: 6px 12px;
        gap: 5px;
    }
    
    .upload-icon {
        width: 20px;
    }
    
    .upload-text {
        font-size: 15px;
    }
    
    .instructions {
        padding: 6px 12px;
        gap: 3px;
        font-size: 12px;
        margin-top: 10px;
    }
    
    .instructions-title {
        font-size: 12px;
    }
    
    .instructions-subtitle {
        font-size: 10px;
    }
    
    .preview-container {
        margin-top: 10px;
    }
    
    .preview-image {
        width: 157px;
        height: 211px;
        border-radius: 16px;
    }
    
    .delete-button {
        top: -12px;
        right: -12px;
        width: 24px;
        height: 24px;
    }
    
    .error-message {
        font-size: 14px;
        margin-top: 4px;
    }
}
</style>