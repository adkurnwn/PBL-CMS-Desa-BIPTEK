<template>
    <div class="file-field-container" :style="{ width: customWidth }">
        <div class="field-name text-selectedText">{{ fieldName }}</div>

        <!-- Show current file if exists -->
        <div v-if="currentFile" class="current-file-info">
            <div class="current-file-label">Current file:</div>
            <div class="current-file-link">
                <div
                    v-html="getFileIconSvg(currentFile)"
                    class="file-icon"
                ></div>
                <div class="file-actions">
                    <button
                        @click="previewFile"
                        v-if="isPdfFile(currentFile)"
                        type="button"
                        class="file-preview-btn bg-white border hover:border-transparent border-selectedText text-greenLabelText hover:bg-selectedBG hover:text-selectedText ease-in-out duration-200"
                    >
                        Lihat
                    </button>
                    <a
                        :href="currentFileUrl"
                        target="_blank"
                        class="file-link text-selectedText"
                    >
                        {{ getFileName(currentFile) }}
                    </a>
                </div>
            </div>
        </div>

        <div class="file-input-container">
            <div class="file-input-wrapper">
                <input
                    :id="id"
                    type="file"
                    @change="handleFileChange"
                    :accept="accept"
                    :disabled="disabled"
                    :required="required"
                    class="file-input"
                    :class="{ 'opacity-75 cursor-not-allowed': disabled }"
                    ref="fileInput"
                />
                <div
                    class="file-input-display text-TableText bg-menuBG border border-selectedText border-solid focus-within:border-2 focus-within:border-selectedText"
                    @click="openFileDialog"
                >
                    <div class="file-input-content">
                        <div
                            v-html="selectedFileIconSvg"
                            class="file-icon document-type-icon"
                        ></div>
                        <span class="file-text">{{ displayText }}</span>
                    </div>
                    <button
                        type="button"
                        @click.stop="openFileDialog"
                        :disabled="disabled"
                        class="file-button bg-white border hover:border-transparent border-selectedText text-greenLabelText hover:bg-selectedBG hover:text-selectedText ease-in-out duration-200"
                    >
                        Browse
                    </button>
                </div>
            </div>
        </div>

        <div v-if="helpText" class="help-text">
            {{ helpText }}
        </div>

        <!-- File Preview Modal -->
        <FilePreview
            :is-open="showPreview && currentFile"
            :file-url="currentFileUrl"
            :file-name="currentFile ? getFileName(currentFile) : ''"
            @close="showPreview = false"
        />
    </div>
</template>

<script>
import FilePreview from "./FilePreview.vue";

export default {
    name: "FileField",
    components: {
        FilePreview,
    },
    props: {
        fieldName: {
            type: String,
            required: true,
        },
        modelValue: {
            type: [File, String],
            default: null,
        },
        currentFile: {
            type: String,
            default: "",
        },
        currentFileUrl: {
            type: String,
            default: "",
        },
        accept: {
            type: String,
            default:
                "application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        },
        customWidth: {
            type: String,
            default: "100%",
        },
        required: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        id: {
            type: String,
            required: true,
        },
        helpText: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            selectedFile: null,
            showPreview: false,
        };
    },
    computed: {
        displayText() {
            if (this.selectedFile) {
                return this.selectedFile.name;
            }
            return "Pilih dokumen...";
        },
        selectedFileIconSvg() {
            if (this.selectedFile) {
                return this.getFileIconSvg(this.selectedFile.name);
            }
            return this.getFileIconSvg("default");
        },
    },
    methods: {
        handleFileChange(event) {
            const file = event.target.files[0];
            this.selectedFile = file;
            this.$emit("update:modelValue", file);
        },
        openFileDialog() {
            if (!this.disabled) {
                this.$refs.fileInput.click();
            }
        },
        getFileIconSvg(fileName) {
            const extension = fileName.split(".").pop().toLowerCase();
            switch (extension) {
                case "pdf":
                    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-red-600">
            <path d="M8.267 14.68c-.184 0-.308.018-.372.036v1.178c.076.018.171.023.302.023.479 0 .774-.242.774-.651 0-.366-.254-.586-.704-.586zm3.487.012c-.2 0-.33.018-.407.036v2.61c.077.018.201.018.313.018.817.006 1.349-.444 1.349-1.396.006-.83-.479-1.268-1.255-1.268z"/>
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM9.498 16.19c-.309.29-.765.42-1.296.42a2.23 2.23 0 0 1-.308-.018v1.426H7v-3.936A7.558 7.558 0 0 1 8.219 14c.557 0 .953.106 1.22.319.254.202.426.533.426.923-.001.392-.131.723-.367.948zm3.807 1.355c-.42.349-1.059.515-1.84.515-.468 0-.799-.03-1.024-.06v-3.917A7.947 7.947 0 0 1 11.66 14c.757 0 1.249.136 1.633.426.415.308.675.799.675 1.504 0 .763-.279 1.29-.663 1.615zM17 14.77h-1.532v.911H16.9v.734h-1.432v1.604h-.906V14.03H17v.74zM14 9h-1V4l5 5h-4z"/>
          </svg>`;
                case "doc":
                case "docx":
                    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-blue-600">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM9.5 14.5l-.75-3.5h-.5l-.75 3.5-.5-2h-.75l.75 4h.75l.75-3.5.75 3.5h.75l.75-4h-.75l-.5 2zm4.5.5h-1v-4h1.5c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-.5v-1zm0-2v1h.5c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5h-.5zM14 9h-1V4l5 5h-4z"/>
          </svg>`;
                default:
                    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-gray-600">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM16 18H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
          </svg>`;
            }
        },
        getFileName(filePath) {
            return filePath.split("/").pop();
        },
        isPdfFile(fileName) {
            return fileName.toLowerCase().endsWith(".pdf");
        },
        previewFile() {
            this.showPreview = true;
        },
    },
    emits: ["update:modelValue"],
};
</script>

<style scoped>
.file-field-container {
    padding: 5.3px;
}

.field-name {
    font-size: 8px;
    margin-bottom: 4.4px;
}

.current-file-info {
    margin-bottom: 4.4px;
}

.current-file-label {
    font-size: 7px;
    color: #666;
    margin-bottom: 2px;
}

.current-file-link {
    display: flex;
    align-items: center;
    gap: 4px;
}

.file-icon {
    width: 12px;
    height: 12px;
    flex-shrink: 0;
}

.file-link {
    font-size: 7px;
    text-decoration: none;
}

.file-link:hover {
    color: #1d4ed8;
    text-decoration: underline;
}

.file-input-container {
    position: relative;
}

.file-input-wrapper {
    position: relative;
}

.file-input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.file-input-display {
    height: 31.05px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5.3px;
    border-radius: 2.2px;
    cursor: pointer;
    transition: border 0.2s ease;
}

.file-input-content {
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 1;
    overflow: hidden;
}

.document-type-icon {
    flex-shrink: 0;
    margin-right: 2px;
}

.file-text {
    font-size: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-button {
    font-size: 7px;
    padding: 2px 6px;
    border-radius: 1.5px;
    cursor: pointer;
    flex-shrink: 0;
}

.file-button:hover {
    background-color: #e5e7eb;
}

.file-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.help-text {
    font-size: 7px;
    color: #666;
    margin-top: 2px;
}

.file-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.file-preview-btn {
    font-size: 7px;
    padding: 1px 4px;
    border-radius: 1.5px;
    cursor: pointer;
    font-weight: 500;
}

/* Responsive styles */
@media (min-width: 640px) {
    .file-field-container {
        padding: 6px;
    }

    .field-name {
        font-size: 9px;
        margin-bottom: 5px;
    }

    .current-file-info {
        margin-bottom: 5px;
    }

    .current-file-label {
        font-size: 8px;
    }

    .file-icon {
        width: 14px;
        height: 14px;
    }

    .file-link {
        font-size: 8px;
    }

    .file-input-display {
        height: 35.25px;
        padding: 6px;
        border-radius: 2.5px;
    }

    .file-text {
        font-size: 9px;
    }

    .file-button {
        font-size: 8px;
        padding: 2px 7px;
        border-radius: 1.7px;
    }

    .file-preview-btn {
        font-size: 8px;
        padding: 2px 5px;
        border-radius: 1.7px;
    }

    .help-text {
        font-size: 8px;
    }
}

@media (min-width: 768px) {
    .file-field-container {
        padding: 6.7px;
    }

    .field-name {
        font-size: 10px;
        margin-bottom: 5.6px;
    }

    .current-file-info {
        margin-bottom: 5.6px;
    }

    .current-file-label {
        font-size: 9px;
    }

    .file-icon {
        width: 16px;
        height: 16px;
    }

    .file-link {
        font-size: 9px;
    }

    .file-input-display {
        height: 39.45px;
        padding: 6.7px;
        border-radius: 2.9px;
    }

    .file-text {
        font-size: 10px;
    }

    .file-button {
        font-size: 9px;
        padding: 3px 8px;
        border-radius: 2px;
    }

    .file-preview-btn {
        font-size: 9px;
        padding: 2px 6px;
        border-radius: 2px;
    }

    .help-text {
        font-size: 9px;
    }
}

@media (min-width: 1024px) {
    .file-field-container {
        padding: 7.3px;
    }

    .field-name {
        font-size: 11px;
        margin-bottom: 6.1px;
    }

    .current-file-info {
        margin-bottom: 6.1px;
    }

    .current-file-label {
        font-size: 10px;
    }

    .file-icon {
        width: 18px;
        height: 18px;
    }

    .file-link {
        font-size: 10px;
    }

    .file-input-display {
        height: 43.05px;
        padding: 7.3px;
        border-radius: 3.1px;
    }

    .file-text {
        font-size: 11px;
    }

    .file-button {
        font-size: 10px;
        padding: 3px 9px;
        border-radius: 2.2px;
    }

    .file-preview-btn {
        font-size: 10px;
        padding: 3px 7px;
        border-radius: 2.2px;
    }

    .help-text {
        font-size: 10px;
    }
}

@media (min-width: 1280px) {
    .file-field-container {
        padding: 8px;
    }

    .field-name {
        font-size: 12px;
        margin-bottom: 6.67px;
    }

    .current-file-info {
        margin-bottom: 6.67px;
    }

    .current-file-label {
        font-size: 11px;
    }

    .file-icon {
        width: 20px;
        height: 20px;
    }

    .file-link {
        font-size: 11px;
    }

    .file-input-display {
        height: 46.5px;
        padding: 8px;
        border-radius: 3.33px;
    }

    .file-text {
        font-size: 12px;
    }

    .file-button {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 2.5px;
    }

    .file-preview-btn {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 2.5px;
    }

    .help-text {
        font-size: 11px;
    }
}

@media (min-width: 1536px) {
    .file-field-container {
        padding: 9.3px;
    }

    .field-name {
        font-size: 14px;
        margin-bottom: 7.7px;
    }

    .current-file-info {
        margin-bottom: 7.7px;
    }

    .current-file-label {
        font-size: 13px;
    }

    .file-icon {
        width: 22px;
        height: 22px;
    }

    .file-link {
        font-size: 13px;
    }

    .file-input-display {
        height: 52.95px;
        padding: 9.3px;
        border-radius: 3.8px;
    }

    .file-text {
        font-size: 14px;
    }

    .file-button {
        font-size: 13px;
        padding: 4px 12px;
        border-radius: 2.8px;
    }

    .file-preview-btn {
        font-size: 13px;
        padding: 4px 10px;
        border-radius: 2.8px;
    }

    .help-text {
        font-size: 13px;
    }
}

@media (min-width: 1920px) {
    .file-field-container {
        padding: 10.7px;
    }

    .field-name {
        font-size: 16px;
        margin-bottom: 8.9px;
    }

    .current-file-info {
        margin-bottom: 8.9px;
    }

    .current-file-label {
        font-size: 15px;
    }

    .file-icon {
        width: 24px;
        height: 24px;
    }

    .file-link {
        font-size: 15px;
    }

    .file-input-display {
        height: 60px;
        padding: 10.7px;
        border-radius: 4.4px;
    }

    .file-text {
        font-size: 16px;
    }

    .file-button {
        font-size: 15px;
        padding: 5px 14px;
        border-radius: 3.2px;
    }

    .file-preview-btn {
        font-size: 15px;
        padding: 5px 12px;
        border-radius: 3.2px;
    }

    .help-text {
        font-size: 15px;
    }
}

@media (min-width: 2560px) {
    .file-field-container {
        padding: 12px;
    }

    .field-name {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .current-file-info {
        margin-bottom: 10px;
    }

    .current-file-label {
        font-size: 17px;
    }

    .file-icon {
        width: 28px;
        height: 28px;
    }

    .file-link {
        font-size: 17px;
    }

    .file-input-display {
        height: 69px;
        padding: 12px;
        border-radius: 5px;
    }

    .file-text {
        font-size: 18px;
    }

    .file-button {
        font-size: 17px;
        padding: 6px 16px;
        border-radius: 3.5px;
    }

    .file-preview-btn {
        font-size: 17px;
        padding: 6px 14px;
        border-radius: 3.5px;
    }

    .help-text {
        font-size: 17px;
    }
}
</style>
