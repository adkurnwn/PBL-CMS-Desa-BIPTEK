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
    gap: 12px;
    text-align: left;
    font-size: 18px;
}

.upload-section {
    align-self: stretch;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 12px;
    gap: 10px;
}

.field-name {
    align-self: stretch;
    font-weight: 500;
}

.upload-box {
    align-self: stretch;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 24px;
    font-size: 15px;
    cursor: pointer;
    transition: background-color 0.2s, border-color 0.2s;
}

.drag-over {
    background-color: #e6f7ff;
    border: 2px dashed var(--selectedText);
    box-shadow: 0 0 10px rgba(76, 118, 96, 0.2);
}

.upload-button {
    border-radius: 5px;
    overflow: hidden;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    padding: 6px 12px;
    gap: 5px;
    cursor: pointer;
}

.upload-icon {
    width: 20px;
    position: relative;
    max-height: 100%;
}

.upload-text {
    position: relative;
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
    padding: 6px 12px;
    gap: 3px;
    text-align: center;
    font-size: 12px;
    margin-top: 10px;
}

.instructions-title {
    align-self: stretch;
    position: relative;
}

.instructions-subtitle {
    align-self: stretch;
    position: relative;
}

.preview-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 10px;
}

.image-wrapper {
    position: relative;
    display: inline-block;
}

.preview-image {
    width: 157px;
    height: 211px;
    object-fit: cover;
    border-radius: 16px; /* Add rounded corners */
    box-shadow: 0px 3px 3px 0px rgba(0, 0, 0, 0.2); /* Add box shadow */
}

.delete-button {
    position: absolute;
    top: -12px;
    right: -12px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    cursor: pointer;
    padding: 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: background-color 0.2s;
}

.delete-button:hover {
    opacity: 0.9;
}

.delete-button:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(220, 20, 60, 0.5);
}

.error-message {
    font-size: 14px;
    margin-top: 4px;
}
</style>