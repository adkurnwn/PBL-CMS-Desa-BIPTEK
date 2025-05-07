<template>
  <div class="editor-wrapper">
    <div class="editor-menu-bar">
      <button
        type="button"
        @click.prevent="editor?.chain().focus().toggleBold().run()"
        :class="{ 'is-active': editor?.isActive('bold') }"
      >
        bold
      </button>
      <button
        type="button"
        @click.prevent="editor?.chain().focus().toggleItalic().run()"
        :class="{ 'is-active': editor?.isActive('italic') }"
      >
        italic
      </button>
      <button
        type="button"
        @click.prevent="editor?.chain().focus().toggleStrike().run()"
        :class="{ 'is-active': editor?.isActive('strike') }"
      >
        strike
      </button>
      <button
        type="button"
        @click.prevent="editor?.chain().focus().setParagraph().run()"
        :class="{ 'is-active': editor?.isActive('paragraph') }"
      >
        paragraph
      </button>
      <button
        type="button"
        @click.prevent="editor?.chain().focus().toggleHeading({ level: 1 }).run()"
        :class="{ 'is-active': editor?.isActive('heading', { level: 1 }) }"
      >
        h1
      </button>
      <button
        type="button"
        @click.prevent="editor?.chain().focus().toggleHeading({ level: 2 }).run()"
        :class="{ 'is-active': editor?.isActive('heading', { level: 2 }) }"
      >
        h2
      </button>
      <button
        type="button"
        @click.prevent="editor?.chain().focus().toggleBulletList().run()"
        :class="{ 'is-active': editor?.isActive('bulletList') }"
      >
        bullet list
      </button>
      <button
        type="button"
        @click.prevent="editor?.chain().focus().toggleOrderedList().run()"
        :class="{ 'is-active': editor?.isActive('orderedList') }"
      >
        ordered list
      </button>
    </div>
    <div 
      class="tiptap-editor" 
      @click="focusEditor"
    >
      <editor-content :editor="editor" />
    </div>
  </div>
</template>

<script setup>
import StarterKit from '@tiptap/starter-kit'
import { Editor, EditorContent } from '@tiptap/vue-3'
import { onMounted, onBeforeUnmount, ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])

const editor = ref(null)

const focusEditor = () => {
  editor.value?.commands.focus()
}

onMounted(() => {
  editor.value = new Editor({
    extensions: [
      StarterKit,
    ],
    content: props.modelValue,
    autofocus: 'end',
    editable: true,
    onUpdate: ({ editor }) => {
      emit('update:modelValue', editor.getHTML())
    },
  })
})

onBeforeUnmount(() => {
  editor.value?.destroy()
})
</script>

<style>
.editor-wrapper {
  border: 1px solid #e2e8f0;
  border-radius: 0.375rem;
  overflow: hidden;
}

.editor-menu-bar {
  padding: 0.5rem;
  border-bottom: 1px solid #e2e8f0;
  background-color: #f8fafc;
}

.editor-menu-bar button {
  margin-right: 0.25rem;
  padding: 0.25rem 0.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.25rem;
  background-color: white;
  font-size: 0.875rem;
}

.editor-menu-bar button:hover {
  background-color: #f1f5f9;
}

.editor-menu-bar button.is-active {
  background-color: #e2e8f0;
}

.tiptap-editor {
  padding: 1rem;
  min-height: 200px;
  cursor: text;
}

.tiptap-editor > * + * {
  margin-top: 0.75em;
}

.tiptap-editor ul,
.tiptap-editor ol {
  padding: 0 1rem;
  margin: 1.25rem 1rem 1.25rem 0.4rem;
}

.tiptap-editor ul li p,
.tiptap-editor ol li p {
  margin: 0.25em 0;
}

.tiptap-editor ul {
  list-style-type: disc;
  padding-left: 1.5rem;
}

.tiptap-editor ol {
  list-style-type: decimal;
  padding-left: 1.5rem;
}

.tiptap-editor ul li,
.tiptap-editor ol li {
  margin: 0.5em 0;
}

.tiptap-editor ul[data-type="taskList"] {
  list-style-type: none;
  padding-left: 0;
}

.tiptap-editor h1,
.tiptap-editor h2,
.tiptap-editor h3,
.tiptap-editor h4,
.tiptap-editor h5,
.tiptap-editor h6 {
  line-height: 1.1;
  margin-top: 2rem;
}

.tiptap-editor code {
  background-color: rgba(97, 97, 97, 0.1);
  border-radius: 0.25em;
  padding: 0.25em 0.3em;
}

.tiptap-editor pre {
  background: #0D0D0D;
  border-radius: 0.5rem;
  color: #FFF;
  padding: 0.75rem 1rem;
  margin: 1.5rem 0;
}

.tiptap-editor pre code {
  background: none;
  color: inherit;
  padding: 0;
}

.tiptap-editor blockquote {
  border-left: 3px solid rgba(13, 13, 13, 0.1);
  padding-left: 1rem;
}

.tiptap-editor hr {
  border: none;
  border-top: 2px solid rgba(13, 13, 13, 0.1);
  margin: 2rem 0;
}

.ProseMirror {
  outline: none;
}

.ProseMirror p.is-editor-empty:first-child::before {
  color: #adb5bd;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
}
</style>