<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'

const props = defineProps({ modelValue: String })
const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
    content: props.modelValue,
    extensions: [StarterKit, Link.configure({ openOnClick: false })],
    onUpdate: ({ editor }) => emit('update:modelValue', editor.getHTML()),
})

function setLink() {
    const previousUrl = editor.value.getAttributes('link').href
    const url = window.prompt('URL', previousUrl)

    if (url === null) return
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
        return
    }

    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}
</script>

<template>
    <div class="border rounded-lg" v-if="editor">
        <!-- Toolbar -->
        <div class="flex flex-wrap gap-1 p-2 border-b">
            <button type="button" @click="editor.chain().focus().toggleBold().run()"
                class="px-2 py-1 rounded text-sm font-bold"
                :class="{ 'bg-gray-200': editor.isActive('bold') }">B</button>
            <button type="button" @click="editor.chain().focus().toggleItalic().run()"
                class="px-2 py-1 rounded text-sm italic"
                :class="{ 'bg-gray-200': editor.isActive('italic') }">I</button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                class="px-2 py-1 rounded text-sm font-semibold"
                :class="{ 'bg-gray-200': editor.isActive('heading', { level: 2 }) }">H2</button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                class="px-2 py-1 rounded text-sm font-semibold"
                :class="{ 'bg-gray-200': editor.isActive('heading', { level: 3 }) }">H3</button>
            <button type="button" @click="editor.chain().focus().toggleBulletList().run()"
                class="px-2 py-1 rounded text-sm"
                :class="{ 'bg-gray-200': editor.isActive('bulletList') }">&#8226; List</button>
            <button type="button" @click="editor.chain().focus().toggleOrderedList().run()"
                class="px-2 py-1 rounded text-sm"
                :class="{ 'bg-gray-200': editor.isActive('orderedList') }">1. List</button>
            <button type="button" @click="editor.chain().focus().toggleBlockquote().run()"
                class="px-2 py-1 rounded text-sm"
                :class="{ 'bg-gray-200': editor.isActive('blockquote') }">&ldquo; Quote</button>
            <button type="button" @click="setLink"
                class="px-2 py-1 rounded text-sm"
                :class="{ 'bg-gray-200': editor.isActive('link') }">Link</button>
        </div>
        <EditorContent :editor="editor" class="prose max-w-none p-4" />
    </div>
</template>
