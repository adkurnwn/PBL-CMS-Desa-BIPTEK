<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import Image from '@tiptap/extension-image'

import BoldIcon from 'vue-material-design-icons/FormatBold.vue'
import ItalicIcon from 'vue-material-design-icons/FormatItalic.vue'
import UnderlineIcon from 'vue-material-design-icons/FormatUnderline.vue'
import H1Icon from 'vue-material-design-icons/FormatHeader1.vue'
import H2Icon from 'vue-material-design-icons/FormatHeader2.vue'
import ListIcon from 'vue-material-design-icons/FormatListBulleted.vue'
import OrderedListIcon from 'vue-material-design-icons/FormatListNumbered.vue'
import BlockquoteIcon from 'vue-material-design-icons/FormatQuoteClose.vue'
import CodeIcon from 'vue-material-design-icons/CodeTags.vue'
import HorizontalRuleIcon from 'vue-material-design-icons/Minus.vue'
import UndoIcon from 'vue-material-design-icons/Undo.vue'
import RedoIcon from 'vue-material-design-icons/Redo.vue'
import ImageIcon from 'vue-material-design-icons/Image.vue'
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: String,
  type: {
    type: String,
    default: 'for_create', // 'for_create' or 'for_edit'
    validator: (value) => ['for_create', 'for_edit'].includes(value)
  }
})

const emit = defineEmits(['update:modelValue', 'image-added'])

// Store temporary image URLs for cleanup
const tempImageUrls = ref(new Set())
const editorContainer = ref(null)
const isResizing = ref(false)
const minHeight = ref(600) // Minimum height that can't be shrunk
const currentHeight = ref(300) // Current height of the editor

// Track placeholder count for edit mode
const existingImagePlaceholderCount = ref(0)

// Method to convert temporary images to placeholders (for create mode)
const convertTempImagesToPlaceholders = () => {
  if (props.type !== 'for_create') return editor.value.getHTML()
  
  let content = editor.value.getHTML()
  
  // Replace temporary images with placeholders
  content = content.replace(/<img[^>]*data-is-temp-upload="true"[^>]*>/g, '{{IMAGEUPLOADER_PLACEHOLDER}}')
  
  return content
}

// Method to restore placeholders for edit mode
const restoreImageUploaderPlaceholders = () => {
  if (props.type !== 'for_edit' || existingImagePlaceholderCount.value === 0) {
    return editor.value.getHTML()
  }
  
  let content = editor.value.getHTML()
  
  // Add EXISTING_IMAGE_PLACEHOLDER at the end based on count
  for (let i = 0; i < existingImagePlaceholderCount.value; i++) {
    content += '{{EXISTING_IMAGE_PLACEHOLDER}}'
  }
  
  return content
}

// Process content for edit mode - hide placeholders and count them
const processContentForEditMode = (content) => {
  if (props.type !== 'for_edit') return content
  
  // Count EXISTING_IMAGE_PLACEHOLDER
  const placeholderMatches = content.match(/\{\{EXISTING_IMAGE_PLACEHOLDER\}\}/g)
  existingImagePlaceholderCount.value = placeholderMatches ? placeholderMatches.length : 0
  
  // Remove EXISTING_IMAGE_PLACEHOLDER from display
  let processedContent = content.replace(/\{\{EXISTING_IMAGE_PLACEHOLDER\}\}/g, '')
  
  return processedContent
}

// Override the update event to handle different modes
const handleUpdate = ({ editor }) => {
  if (props.type === 'for_create') {
    // Convert temp images to placeholders before emitting
    const contentWithPlaceholders = convertTempImagesToPlaceholders()
    emit('update:modelValue', contentWithPlaceholders)
  } else {
    // For edit mode, emit as-is (without hidden placeholders)
    emit('update:modelValue', editor.getHTML())
  }
}

// Update the editor configuration
const editor = useEditor({
  content: processContentForEditMode(props.modelValue),
  onUpdate: handleUpdate,
  extensions: [
    StarterKit, 
    Underline,
    Image.configure({
      inline: false,
      allowBase64: true,
      HTMLAttributes: {
        class: 'richtext-preview-image',
      },
    }),
  ],
  editorProps: {
    attributes: {
      class:
        'p-4 overflow-y-auto outline-none prose max-w-none resize-none',
    },
  },
  onDestroy: () => {
    // Clean up temporary URLs when editor is destroyed
    tempImageUrls.value.forEach(url => {
      URL.revokeObjectURL(url)
    })
    tempImageUrls.value.clear()
  }
})

// Resize functionality
const startResize = (e) => {
  e.preventDefault()
  isResizing.value = true
  const startY = e.clientY
  const startHeight = currentHeight.value
  
  const doDrag = (e) => {
    const newHeight = startHeight + (e.clientY - startY)
    if (newHeight >= minHeight.value && newHeight <= 800) { // Min to max height
      currentHeight.value = newHeight
      if (editorContainer.value) {
        editorContainer.value.style.height = newHeight + 'px'
      }
    }
  }
  
  const stopDrag = () => {
    isResizing.value = false
    document.removeEventListener('mousemove', doDrag)
    document.removeEventListener('mouseup', stopDrag)
  }
  
  document.addEventListener('mousemove', doDrag)
  document.addEventListener('mouseup', stopDrag)
}

onMounted(() => {
  if (editorContainer.value) {
    currentHeight.value = minHeight.value
    editorContainer.value.style.height = minHeight.value + 'px'
  }
})

const handleImageUpload = () => {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'
  input.onchange = (event) => {
    const file = event.target.files[0]
    if (file) {
      // Emit image file to parent component
      emit('image-added', file)
      
      if (props.type === 'for_create') {
        // For create mode: Show actual image in editor but it will be converted to placeholder on submit
        const tempUrl = URL.createObjectURL(file)
        tempImageUrls.value.add(tempUrl)
        
        // Insert actual image with temporary URL and special attributes for identification
        const imageId = `temp_image_${Date.now()}`
        editor.value.chain().focus().setImage({ 
          src: tempUrl,
          alt: '',
          'data-temp-image-id': imageId,
          'data-is-temp-upload': 'true',
          'data-blob-url': tempUrl
        }).run()
      } else {
        // For edit mode: Just increment the counter (don't show placeholder text)
        existingImagePlaceholderCount.value++
      }
    }
  }
  input.click()
}

// Method to replace placeholder with actual image path - simplified
const replaceImagePlaceholder = (placeholderId, imagePath) => {
  const content = editor.value.getHTML()
  const updatedContent = content.replace(
    new RegExp(`{{${placeholderId}}}`, 'g'),
    imagePath
  )
  editor.value.commands.setContent(updatedContent)
}

// Expose methods to parent
defineExpose({
  replaceImagePlaceholder,
  convertTempImagesToPlaceholders,
  restoreImageUploaderPlaceholders
})
</script>

<template>
  <div 
    ref="editorContainer" 
    class="relative border border-selectedText rounded-[5px] flex flex-col"
    :class="{ 'select-none': isResizing }"
    :style="{ height: currentHeight + 'px', minHeight: minHeight + 'px' }"
  >
    <!-- Toolbar -->
    <section
      v-if="editor"
      class="buttons text-selectedText flex items-center rounded-[5px] flex-wrap gap-x-4 p-4 bg-menuBG flex-shrink-0"
    >
      <button
        type="button"
        @click="editor.chain().focus().toggleBold().run()"
        :class="{ 'bg-gray-200 rounded': editor.isActive('bold') }"
        class="p-1"
      >
        <BoldIcon title="Bold" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleItalic().run()"
        :class="{ 'bg-gray-200 rounded': editor.isActive('italic') }"
        class="p-1"
      >
        <ItalicIcon title="Italic" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleUnderline().run()"
        :class="{ 'bg-gray-200 rounded': editor.isActive('underline') }"
        class="p-1"
      >
        <UnderlineIcon title="Underline" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
        :class="{
          'bg-gray-200 rounded': editor.isActive('heading', { level: 1 }),
        }"
        class="p-1"
      >
        <H1Icon title="H1" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
        :class="{
          'bg-gray-200 rounded': editor.isActive('heading', { level: 2 }),
        }"
        class="p-1"
      >
        <H2Icon title="H2" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleBulletList().run()"
        :class="{ 'bg-gray-200 rounded': editor.isActive('bulletList') }"
        class="p-1"
      >
        <ListIcon title="Bullet List" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleOrderedList().run()"
        :class="{ 'bg-gray-200 rounded': editor.isActive('orderedList') }"
        class="p-1"
      >
        <OrderedListIcon title="Ordered List" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleBlockquote().run()"
        :class="{ 'bg-gray-200 rounded': editor.isActive('blockquote') }"
        class="p-1"
      >
        <BlockquoteIcon title="Blockquote" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleCode().run()"
        :class="{ 'bg-gray-200 rounded': editor.isActive('code') }"
        class="p-1"
      >
        <CodeIcon title="Code" />
      </button>
      <button
        type="button"
        @click="handleImageUpload"
        class="p-1"
      >
        <ImageIcon title="Upload Image" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().setHorizontalRule().run()"
        class="p-1"
      >
        <HorizontalRuleIcon title="Horizontal Rule" />
      </button>
      <button
        type="button"
        class="p-1 disabled:text-gray-400"
        @click="editor.chain().focus().undo().run()"
        :disabled="!editor.can().chain().focus().undo().run()"
      >
        <UndoIcon title="Undo" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().redo().run()"
        :disabled="!editor.can().chain().focus().redo().run()"
        class="p-1 disabled:text-gray-400"
      >
        <RedoIcon title="Redo" />
      </button>
    </section>
    
    <!-- Editor content with resize handle -->
    <div class="relative flex-1 flex flex-col min-h-0">
      <EditorContent :editor="editor" class="flex-1 min-h-0" />
      
      <!-- Resize handle - positioned at bottom-right edge -->
      <div
        @mousedown="startResize"
        class="absolute bottom-0 right-0 w-4 h-4 cursor-se-resize bg-gray-300 hover:bg-gray-400 transition-colors duration-200 flex items-center justify-center z-10"
        title="Drag to resize"
      >
        <!-- Triangle resize icon -->
        <svg width="10" height="10" viewBox="0 0 10 10" class="text-gray-600">
          <path d="M10 10L0 10L10 0Z" fill="currentColor" opacity="0.8"/>
          <path d="M7 10L10 7L10 10Z" fill="currentColor" opacity="0.5"/>
          <path d="M4 10L10 4L10 10Z" fill="currentColor" opacity="0.3"/>
        </svg>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Style for preview images in the editor */
:deep(.richtext-preview-image) {
  max-width: 50% !important;
  height: auto !important;
  display: block;
  margin: 0.5rem 0;
}

/* For create mode, show temp images at full width in editor */
:deep(img[data-is-temp-upload="true"]) {
  max-width: 100% !important;
  width: 100% !important;
  height: auto !important;
  display: block;
  margin: 0.5rem 0;
}

/* Ensure the editor content doesn't interfere with image sizing */
:deep(.ProseMirror img) {
  max-width: 50% !important;
  height: auto !important;
}

/* Make the editor content fill available space */
:deep(.ProseMirror) {
  min-height: 200px;
  height: 100%;
  border: none;
  padding: 1rem;
  outline: none;
  overflow-y: auto;
}

/* Ensure the editor container is flexible */
:deep(.ProseMirror-focused) {
  outline: none;
}

/* Style for when resizing */
.select-none * {
  user-select: none !important;
}

/* Ensure proper flexbox behavior */
:deep(.tiptap) {
  height: 100%;
  display: flex;
  flex-direction: column;
}
</style>
