<script setup>
import { ref, watch, nextTick } from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import ImageUploader from "@/Components/DashboardComponent/ImageUploader.vue";

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({
            title: '',
            content: '',
            images: [],
            alts: []
        })
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update:modelValue']);

const localValue = ref({ ...props.modelValue });
const contentEditor = ref(null);
const isSourceMode = ref(false);

// Watch for changes and emit updates
watch(localValue, (newValue) => {
    emit('update:modelValue', newValue);
}, { deep: true });

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
    localValue.value = { ...newValue };
    if (contentEditor.value && !isSourceMode.value) {
        contentEditor.value.innerHTML = newValue.content;
    }
}, { deep: true });

// Handle image changes
const handleImagesChange = (images) => {
    localValue.value.images = images;
};

const handleAltsChange = (alts) => {
    localValue.value.alts = alts;
};

// Handle content editor input
const handleContentInput = () => {
    if (contentEditor.value) {
        localValue.value.content = contentEditor.value.innerHTML;
    }
};

// Toggle between WYSIWYG and source code mode
const toggleSourceMode = () => {
    isSourceMode.value = !isSourceMode.value;
    
    nextTick(() => {
        if (isSourceMode.value) {
            // Switching to source mode - show HTML
            const textarea = document.getElementById('content-source');
            if (textarea) {
                textarea.focus();
            }
        } else {
            // Switching to WYSIWYG mode - show formatted content
            if (contentEditor.value) {
                contentEditor.value.innerHTML = localValue.value.content;
                contentEditor.value.focus();
            }
        }
    });
};

// Handle source code changes
const handleSourceInput = (event) => {
    localValue.value.content = event.target.value;
    if (contentEditor.value && !isSourceMode.value) {
        contentEditor.value.innerHTML = event.target.value;
    }
};

// WYSIWYG formatting functions using document.execCommand
const execCommand = (command, value = null) => {
    if (isSourceMode.value) return;
    
    document.execCommand(command, false, value);
    contentEditor.value.focus();
    handleContentInput(); // Update the content
};

const formatBold = () => execCommand('bold');
const formatItalic = () => execCommand('italic');
const formatUnderline = () => execCommand('underline');
const formatH1 = () => execCommand('formatBlock', '<h1>');
const formatH2 = () => execCommand('formatBlock', '<h2>');
const formatH3 = () => execCommand('formatBlock', '<h3>');
const formatParagraph = () => execCommand('formatBlock', '<p>');
const formatUnorderedList = () => execCommand('insertUnorderedList');
const formatOrderedList = () => execCommand('insertOrderedList');
const formatBlockquote = () => execCommand('formatBlock', '<blockquote>');

const formatLink = () => {
    const url = prompt('Enter URL:');
    if (url) {
        execCommand('createLink', url);
    }
};

const formatLineBreak = () => {
    execCommand('insertHTML', '<br>');
};

const formatCode = () => {
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
        const range = selection.getRangeAt(0);
        const selectedText = range.toString();
        
        if (selectedText) {
            execCommand('insertHTML', `<code>${selectedText}</code>`);
        } else {
            execCommand('insertHTML', '<code></code>');
            // Move cursor inside the code tags
            const codeElements = contentEditor.value.querySelectorAll('code');
            const lastCode = codeElements[codeElements.length - 1];
            if (lastCode) {
                const newRange = document.createRange();
                newRange.setStart(lastCode, 0);
                newRange.collapse(true);
                selection.removeAllRanges();
                selection.addRange(newRange);
            }
        }
    }
};

const formatCodeBlock = () => {
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
        const range = selection.getRangeAt(0);
        const selectedText = range.toString();
        
        if (selectedText) {
            execCommand('insertHTML', `<pre><code>${selectedText}</code></pre>`);
        } else {
            execCommand('insertHTML', '<pre><code></code></pre>');
        }
    }
};

// Initialize the editor when component mounts
nextTick(() => {
    if (contentEditor.value && localValue.value.content) {
        contentEditor.value.innerHTML = localValue.value.content;
    }
    
    // Ensure proper text direction
    if (contentEditor.value) {
        contentEditor.value.style.direction = 'ltr';
        contentEditor.value.style.textAlign = 'left';
        contentEditor.value.style.unicodeBidi = 'normal';
    }
});
</script>

<template>
    <div class="space-y-6">
        <!-- Title Field -->
        <div>
            <InputLabel for="title" value="Title" />
            <TextInput 
                id="title" 
                v-model="localValue.title" 
                type="text" 
                class="mt-1 block w-full text-2xl font-bold p-4"
                placeholder="Enter your news title..."
                required 
            />
            <InputError :message="errors.title" class="mt-2" />
        </div>

        <!-- Content Field with WYSIWYG Editor -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <InputLabel for="content" value="Content" />
                
                <!-- Source/WYSIWYG Toggle Button -->
                <button
                    type="button"
                    @click="toggleSourceMode"
                    :class=" [
                        'px-3 py-1 text-sm rounded-md border transition-colors',
                        isSourceMode 
                            ? 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700' 
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                    ]">
                    {{ isSourceMode ? '🎨 Visual Editor' : '📝 Source Code' }}
                </button>
            </div>
            
            <!-- WYSIWYG Formatting Toolbar (only visible in WYSIWYG mode) -->
            <div v-show="!isSourceMode" class="mt-2 mb-3 p-3 bg-gray-50 border border-gray-300 rounded-t-md">
                <div class="flex flex-wrap gap-2">
                    <!-- Text Formatting -->
                    <div class="flex gap-1 border-r border-gray-300 pr-2">
                        <button
                            type="button"
                            @click="formatBold"
                            class="px-2 py-1 text-sm font-bold bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Bold">
                            <strong>B</strong>
                        </button>
                        <button
                            type="button"
                            @click="formatItalic"
                            class="px-2 py-1 text-sm italic bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Italic">
                            <em>I</em>
                        </button>
                        <button
                            type="button"
                            @click="formatUnderline"
                            class="px-2 py-1 text-sm underline bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Underline">
                            U
                        </button>
                    </div>

                    <!-- Headings -->
                    <div class="flex gap-1 border-r border-gray-300 pr-2">
                        <button
                            type="button"
                            @click="formatH1"
                            class="px-2 py-1 text-sm font-bold bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Heading 1">
                            H1
                        </button>
                        <button
                            type="button"
                            @click="formatH2"
                            class="px-2 py-1 text-sm font-semibold bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Heading 2">
                            H2
                        </button>
                        <button
                            type="button"
                            @click="formatH3"
                            class="px-2 py-1 text-sm font-medium bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Heading 3">
                            H3
                        </button>
                    </div>

                    <!-- Lists -->
                    <div class="flex gap-1 border-r border-gray-300 pr-2">
                        <button
                            type="button"
                            @click="formatUnorderedList"
                            class="px-2 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Bullet List">
                            • List
                        </button>
                        <button
                            type="button"
                            @click="formatOrderedList"
                            class="px-2 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Numbered List">
                            1. List
                        </button>
                    </div>

                    <!-- Other Elements -->
                    <div class="flex gap-1 border-r border-gray-300 pr-2">
                        <button
                            type="button"
                            @click="formatParagraph"
                            class="px-2 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Paragraph">
                            ¶
                        </button>
                        <button
                            type="button"
                            @click="formatLineBreak"
                            class="px-2 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Line Break">
                            BR
                        </button>
                        <button
                            type="button"
                            @click="formatLink"
                            class="px-2 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Link">
                            🔗
                        </button>
                    </div>

                    <!-- Advanced -->
                    <div class="flex gap-1">
                        <button
                            type="button"
                            @click="formatBlockquote"
                            class="px-2 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Blockquote">
                            " "
                        </button>
                        <button
                            type="button"
                            @click="formatCode"
                            class="px-2 py-1 text-sm font-mono bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Inline Code">
                            &lt;&gt;
                        </button>
                        <button
                            type="button"
                            @click="formatCodeBlock"
                            class="px-2 py-1 text-sm font-mono bg-white border border-gray-300 rounded hover:bg-gray-100"
                            title="Code Block">
                            { }
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Editor Container -->
            <div class="relative">
                <!-- WYSIWYG Editor (visible when not in source mode) -->
                <div 
                    v-show="!isSourceMode"
                    ref="contentEditor"
                    contenteditable="true"
                    @input="handleContentInput"
                    @paste="handleContentInput"
                    dir="ltr"
                    lang="en"
                    class="mt-0 block w-full min-h-[400px] border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 p-4 text-base leading-relaxed bg-white overflow-y-auto outline-none rounded-b-md resize-y
                    [&_h1]:text-3xl [&_h1]:font-bold [&_h1]:my-6 [&_h1]:leading-tight
                    [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:my-5 [&_h2]:leading-tight
                    [&_h3]:text-xl [&_h3]:font-medium [&_h3]:my-4 [&_h3]:leading-tight
                    [&_p]:my-3 [&_p]:leading-relaxed
                    [&_ul]:my-4 [&_ul]:pl-6 [&_ol]:my-4 [&_ol]:pl-6
                    [&_li]:my-1
                    [&_blockquote]:border-l-4 [&_blockquote]:border-gray-300 [&_blockquote]:pl-4 [&_blockquote]:my-4 [&_blockquote]:italic [&_blockquote]:text-gray-600
                    [&_code]:bg-gray-100 [&_code]:px-1 [&_code]:py-0.5 [&_code]:rounded [&_code]:text-sm [&_code]:font-mono
                    [&_pre]:bg-gray-100 [&_pre]:p-4 [&_pre]:rounded-lg [&_pre]:overflow-x-auto [&_pre]:my-4
                    [&_pre_code]:bg-transparent [&_pre_code]:p-0 [&_pre_code]:rounded-none
                    [&_strong]:font-semibold
                    [&_em]:italic
                    [&_u]:underline
                    [&_a]:text-blue-600 [&_a]:underline [&_a:hover]:text-blue-800
                    empty:before:content-[attr(placeholder)] empty:before:text-gray-400 empty:before:italic"
                    placeholder="Start typing your content here... Use the formatting buttons above to style your text."
                    :style="{
                        direction: 'ltr',
                        textAlign: 'left',
                        unicodeBidi: 'normal',
                        writingMode: 'horizontal-tb'
                    }">
                </div>

                <!-- Source Code Editor (visible when in source mode) -->
                <textarea 
                    v-show="isSourceMode"
                    id="content-source"
                    :value="localValue.content"
                    @input="handleSourceInput"
                    dir="ltr"
                    class="mt-0 block w-full min-h-[400px] rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-4 text-base leading-relaxed font-mono resize-y"
                    placeholder="Edit HTML source code here..."
                    :style="{
                        direction: 'ltr',
                        textAlign: 'left',
                        unicodeBidi: 'normal'
                    }">
                </textarea>
            </div>

            <InputError :message="errors.content" class="mt-2" />
            
            <!-- Mode Indicator -->
            <div class="mt-2 text-sm text-gray-600">
                <span v-if="isSourceMode" class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    Source Code Mode - Edit raw HTML
                </span>
                <span v-else class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    Visual Editor Mode - WYSIWYG formatting
                </span>
            </div>
        </div>

        <!-- Images Section -->
        <div>
            <ImageUploader 
                :modelValue="localValue.images"
                @update:modelValue="handleImagesChange"
                :modelValueAlts="localValue.alts"
                @update:modelValueAlts="handleAltsChange"
                label="Article Images"
                :error="errors" 
                :alt-error="errors.alts" 
                :defaultAltText="localValue.title"
                :required="true" 
            />
        </div>
    </div>
</template>
