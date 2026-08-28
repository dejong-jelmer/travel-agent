<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import { TextStyle, FontSize } from '@tiptap/extension-text-style'

const props = defineProps({
    modelValue: String,
    name: String,
    label: String,
    feedback: {
        type: [String, Array],
        required: false,
    },
    required: {
        type: Boolean,
        required: false,
        default: null,
    },
})
const emit = defineEmits(['update:modelValue'])

const fontSizes = ['11px', '12px', '14px', '16px', '18px', '20px', '24px', '30px', '36px']

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Link.configure({ openOnClick: false }),
        TextStyle,
        FontSize
    ],
    onUpdate: ({ editor }) => emit('update:modelValue', editor.getHTML()),
})

function setFontSize(event) {
    const value = event.target.value
    if (value) {
        editor.value.chain().focus().setFontSize(value).run()
    } else {
        editor.value.chain().focus().unsetFontSize().run()
    }
}

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
    <div class="grid gap-1" v-if="editor">
        <Label v-if="label" :forField="name" :required="required">{{ label }}</Label>
        <div class="border rounded-lg">
            <!-- Toolbar -->
            <div class="flex flex-wrap gap-1 p-2 border-b">
                <button type="button" @click="editor.chain().focus().toggleBold().run()"
                    class="px-2 py-1 rounded text-sm font-bold"
                    :class="{ 'bg-gray-200': editor.isActive('bold') }">B</button>
                <button type="button" @click="editor.chain().focus().toggleItalic().run()"
                    class="px-2 py-1 rounded text-sm italic"
                    :class="{ 'bg-gray-200': editor.isActive('italic') }">I</button>
                <button type="button" @click="editor.chain().focus().toggleUnderline().run()"
                    class="px-2 py-1 rounded text-sm underline"
                    :class="{ 'bg-gray-200': editor.isActive('underline') }">U</button>
                <button type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                    class="px-2 py-1 rounded text-sm font-semibold"
                    :class="{ 'bg-gray-200': editor.isActive('heading', { level: 1 }) }">H1</button>
                <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                    class="px-2 py-1 rounded text-sm font-semibold"
                    :class="{ 'bg-gray-200': editor.isActive('heading', { level: 2 }) }">H2</button>
                <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                    class="px-2 py-1 rounded text-sm font-semibold"
                    :class="{ 'bg-gray-200': editor.isActive('heading', { level: 3 }) }">H3</button>
                <button type="button" @click="editor.chain().focus().toggleBulletList().run()"
                    class="px-2 py-1 rounded text-sm" :class="{ 'bg-gray-200': editor.isActive('bulletList') }">&#8226;
                    List</button>
                <button type="button" @click="editor.chain().focus().toggleOrderedList().run()"
                    class="px-2 py-1 rounded text-sm" :class="{ 'bg-gray-200': editor.isActive('orderedList') }">1.
                    List</button>
                <button type="button" @click="editor.chain().focus().toggleBlockquote().run()"
                    class="px-2 py-1 rounded text-sm" :class="{ 'bg-gray-200': editor.isActive('blockquote') }">&ldquo;
                    Quote</button>
                <button type="button" @click="setLink" class="px-2 py-1 rounded text-sm"
                    :class="{ 'bg-gray-200': editor.isActive('link') }">Link</button>
                <button type="button" @click="editor.chain().focus().setParagraph().run()"
                    class="px-2 py-1 rounded text-sm" :class="{ 'bg-gray-200': editor.isActive('paragraph') }"
                    title="Paragraph">&para;</button>
                <button type="button" @click="editor.chain().focus().setHardBreak().run()"
                    class="px-2 py-1 rounded text-sm" title="Hard break (Shift+Enter)">&crarr;</button>
                <button type="button" @click="editor.chain().focus().setHorizontalRule().run()"
                    class="px-2 py-1 rounded text-sm" title="Horizontal rule">&mdash;</button>
                <button type="button" @click="editor.chain().focus().unsetAllMarks().clearNodes().run()"
                    class="px-2 py-1 rounded text-sm" title="Clear formatting">&times;</button>
                <select :value="editor.getAttributes('textStyle').fontSize || ''" @change="setFontSize"
                    class="px-2 py-1 rounded text-sm border" title="Font size">
                    <option value="">Size</option>
                    <option v-for="size in fontSizes" :key="size" :value="size">{{ size }}</option>
                </select>
            </div>
            <EditorContent :editor="editor" :id="name" class="prose max-w-none p-4" />
        </div>
        <FormFeedback v-if="feedback" :message="feedback" />
    </div>
</template>
