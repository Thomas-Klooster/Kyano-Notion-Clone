<template>
    <div v-if="editor" class="editor-wrapper" ref="editorWrapper">
        <BubbleMenu :editor="editor" :tippy-options="{
            duration: 120,
            placement: 'top',
            maxWidth: 'none',
            interactive: true,
        }" :should-show="shouldShowBubbleMenu">
            <div class="notion-bubble-menu" ref="menuRef">
                <div class="menu-item-wrapper">
                    <button type="button" class="menu-btn" :class="{ active: openPanel === 'turnInto' }"
                        @click="togglePanel('turnInto')">
                        T
                    </button>

                    <div v-if="openPanel === 'turnInto'" class="submenu">
                        <div class="submenu-title">Turn into</div>

                        <button class="submenu-item" @click="setParagraph">
                            <span>Text</span>
                            <span v-if="editor.isActive('paragraph')">✓</span>
                        </button>

                        <button class="submenu-item" @click="setHeading(1)">
                            <span>Heading 1</span>
                            <span v-if="editor.isActive('heading', { level: 1 })">✓</span>
                        </button>

                        <button class="submenu-item" @click="setHeading(2)">
                            <span>Heading 2</span>
                            <span v-if="editor.isActive('heading', { level: 2 })">✓</span>
                        </button>

                        <button class="submenu-item" @click="setHeading(3)">
                            <span>Heading 3</span>
                            <span v-if="editor.isActive('heading', { level: 3 })">✓</span>
                        </button>

                        <button class="submenu-item" @click="toggleBulletList">
                            <span>Bulleted list</span>
                            <span v-if="editor.isActive('bulletList')">✓</span>
                        </button>

                        <button class="submenu-item" @click="toggleOrderedList">
                            <span>Numbered list</span>
                            <span v-if="editor.isActive('orderedList')">✓</span>
                        </button>

                        <button class="submenu-item" @click="toggleCodeBlock">
                            <span>Code</span>
                            <span v-if="editor.isActive('codeBlock')">✓</span>
                        </button>

                        <button class="submenu-item" @click="toggleBlockquote">
                            <span>Quote</span>
                            <span v-if="editor.isActive('blockquote')">✓</span>
                        </button>
                    </div>
                </div>

                <button type="button" class="menu-btn" :class="{ active: editor.isActive('bold') }"
                    @click="editor.chain().focus().toggleBold().run()">
                    <strong>B</strong>
                </button>

                <button type="button" class="menu-btn" :class="{ active: editor.isActive('italic') }"
                    @click="editor.chain().focus().toggleItalic().run()">
                    <em>I</em>
                </button>

                <button type="button" class="menu-btn" :class="{ active: editor.isActive('underline') }"
                    @click="editor.chain().focus().toggleUnderline().run()">
                    <u>U</u>
                </button>

                <button type="button" class="menu-btn" :class="{ active: editor.isActive('strike') }"
                    @click="editor.chain().focus().toggleStrike().run()">
                    <s>S</s>
                </button>

                <button type="button" class="menu-btn" :class="{ active: editor.isActive('code') }"
                    @click="setSelectionToSingleCodeBlock">
                    &lt;/&gt;
                </button>

                <!-- Link -->
                <div class="menu-item-wrapper">
                    <button type="button" class="menu-btn"
                        :class="{ active: openPanel === 'link' || editor.isActive('link') }" @click="openLinkPanel">
                        🔗
                    </button>

                    <div v-if="openPanel === 'link'" class="submenu link-submenu">
                        <input v-model="linkUrl" class="link-input" type="text" placeholder="Paste link..."
                            @keydown.enter.prevent="applyLink" />

                        <div class="submenu-actions">
                            <button class="submenu-action-btn" @click="applyLink">Apply</button>
                            <button class="submenu-action-btn ghost" @click="removeLink">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="menu-item-wrapper">
                    <button type="button" class="menu-btn" :class="{ active: openPanel === 'more' }"
                        @click="togglePanel('more')">
                        …
                    </button>

                    <div v-if="openPanel === 'more'" class="submenu">
                        <button class="submenu-item" @click="clearFormatting">
                            <span>Clear formatting</span>
                        </button>
                    </div>
                </div>
            </div>
        </BubbleMenu>

        <div class="editor-shell">
            <EditorContent :editor="editor" />
        </div>

        <div v-if="slashMenu.isOpen" class="slash-menu" :style="{
            left: `${slashMenu.x}px`,
            top: `${slashMenu.y}px`,
        }">
            <button v-for="(item, index) in filteredSlashItems" :key="item.key" class="slash-menu-item"
                :class="{ active: index === slashMenu.selectedIndex }" @mousedown.prevent="runSlashCommand(item)">
                <div class="slash-menu-item-icon">
                    <component :is="item.icon" :size="16" />
                </div>

                <div class="slash-menu-item-title">
                    {{ item.title }}
                </div>
            </button>
        </div>
    </div>
</template>

<script>
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Underline from '@tiptap/extension-underline'
import { Editor, EditorContent } from '@tiptap/vue-3'
import { BubbleMenu } from '@tiptap/vue-3/menus'
import {
    Type,
    Heading1,
    Heading2,
    Heading3,
    List,
    ListOrdered,
    Quote,
    Code
} from 'lucide-vue-next'

export default {
    components: {
        EditorContent,
        BubbleMenu,

        // icons
        Type,
        Heading1,
        Heading2,
        Heading3,
        List,
        ListOrdered,
        Quote,
        Code,
    },

    data() {
        return {
            editor: null,
            openPanel: null,
            linkUrl: '',
            slashMenu: {
                isOpen: false,
                query: '',
                x: 0,
                y: 0,
                range: null,
                selectedIndex: 0,
            },
            slashItems: [
                {
                    key: 'text',
                    title: 'Text',
                    icon: 'Type',
                    command: ({ editor, range }) => {
                        editor.chain().focus().deleteRange(range).setParagraph().run()
                    },
                },
                {
                    key: 'h1',
                    title: 'Heading 1',
                    icon: 'Heading1',
                    command: ({ editor, range }) => {
                        editor.chain().focus().deleteRange(range).toggleHeading({ level: 1 }).run()
                    },
                },
                {
                    key: 'h2',
                    title: 'Heading 2',
                    icon: 'Heading2',
                    command: ({ editor, range }) => {
                        editor.chain().focus().deleteRange(range).toggleHeading({ level: 2 }).run()
                    },
                },
                {
                    key: 'h3',
                    title: 'Heading 3',
                    icon: 'Heading3',
                    command: ({ editor, range }) => {
                        editor.chain().focus().deleteRange(range).toggleHeading({ level: 3 }).run()
                    },
                },
                {
                    key: 'bullet',
                    title: 'Bulleted list',
                    icon: 'List',
                    command: ({ editor, range }) => {
                        editor.chain().focus().deleteRange(range).toggleBulletList().run()
                    },
                },
                {
                    key: 'numbered',
                    title: 'Numbered list',
                    icon: 'ListOrdered',
                    command: ({ editor, range }) => {
                        editor.chain().focus().deleteRange(range).toggleOrderedList().run()
                    },
                },
                {
                    key: 'quote',
                    title: 'Quote',
                    icon: 'Quote',
                    command: ({ editor, range }) => {
                        editor.chain().focus().deleteRange(range).toggleBlockquote().run()
                    },
                },
                {
                    key: 'code-block',
                    title: 'Code block',
                    icon: 'Code',
                    command: ({ editor, range }) => {
                        editor
                            .chain()
                            .focus()
                            .deleteRange(range)
                            .insertContent({ type: 'codeBlock' })
                            .run()
                    },
                }
            ]
        }
    },

    computed: {
        filteredSlashItems() {
            const query = this.slashMenu.query.trim().toLowerCase()

            if (!query) return this.slashItems

            return this.slashItems.filter(item =>
                item.title.toLowerCase().includes(query) ||
                item.description.toLowerCase().includes(query)
            )
        },
    },

    mounted() {
        this.editor = new Editor({
            extensions: [
                StarterKit,
                Underline,
                Link.configure({
                    openOnClick: false,
                    autolink: true,
                    defaultProtocol: 'https',
                }),
            ],
            content: `
        <h1> Untitled</h1>
        <p> Select text to open the menu.</p>
        <p> Type / to open the slash commands menu.</p>
      `,
            editorProps: {
                attributes: {
                    class: 'tiptap',
                },
                handleKeyDown: (_view, event) => {
                    if (!this.slashMenu.isOpen) return false

                    if (event.key === 'ArrowDown') {
                        event.preventDefault()
                        const max = this.filteredSlashItems.length - 1
                        this.slashMenu.selectedIndex = Math.min(this.slashMenu.selectedIndex + 1, max)
                        return true
                    }

                    if (event.key === 'ArrowUp') {
                        event.preventDefault()
                        this.slashMenu.selectedIndex = Math.max(this.slashMenu.selectedIndex - 1, 0)
                        return true
                    }

                    if (event.key === 'Enter') {
                        event.preventDefault()
                        const item = this.filteredSlashItems[this.slashMenu.selectedIndex]
                        if (item) this.runSlashCommand(item)
                        return true
                    }

                    if (event.key === 'Escape') {
                        event.preventDefault()
                        this.closeSlashMenu()
                        return true
                    }

                    return false
                },
            },
            onUpdate: () => {
                this.updateSlashMenu()
            },
            onSelectionUpdate: () => {
                this.updateSlashMenu()
            },
        })

        document.addEventListener('click', this.handleClickOutside)
    },

    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside)

        if (this.editor) {
            this.editor.destroy()
        }
    },

    methods: {
        shouldShowBubbleMenu({ editor, state }) {
            const { from, to } = state.selection
            return editor.isEditable && from !== to
        },

        togglePanel(panel) {
            this.openPanel = this.openPanel === panel ? null : panel
        },

        openLinkPanel() {
            this.linkUrl = this.editor.getAttributes('link').href || ''
            this.openPanel = this.openPanel === 'link' ? null : 'link'
        },

        handleClickOutside(event) {
            const menu = this.$refs.menuRef
            if (menu && !menu.contains(event.target)) {
                this.openPanel = null
            }
        },

        setParagraph() {
            this.editor.chain().focus().setParagraph().run()
            this.openPanel = null
        },

        setHeading(level) {
            this.editor.chain().focus().toggleHeading({ level }).run()
            this.openPanel = null
        },

        toggleBulletList() {
            this.editor.chain().focus().toggleBulletList().run()
            this.openPanel = null
        },

        toggleOrderedList() {
            this.editor.chain().focus().toggleOrderedList().run()
            this.openPanel = null
        },

        toggleCodeBlock() {
            this.editor.chain().focus().toggleCodeBlock().run()
            this.openPanel = null
        },

        setSelectionToSingleCodeBlock() {
            const { state } = this.editor
            const { from, to } = state.selection
            const selectedText = state.doc.textBetween(from, to, '\n')

            this.editor.chain().focus().deleteSelection().insertContent({
                type: 'codeBlock',
                content: selectedText ? [{ type: 'text', text: selectedText }] : [],
            }).run()

            this.openPanel = null
        },

        toggleBlockquote() {
            this.editor.chain().focus().toggleBlockquote().run()
            this.openPanel = null
        },

        applyLink() {
            const url = this.linkUrl.trim()

            if (!url) {
                this.removeLink()
                return
            }

            this.editor
                .chain()
                .focus()
                .extendMarkRange('link')
                .setLink({ href: url })
                .run()

            this.openPanel = null
        },

        removeLink() {
            this.editor
                .chain()
                .focus()
                .extendMarkRange('link')
                .unsetLink()
                .run()

            this.linkUrl = ''
            this.openPanel = null
        },

        clearFormatting() {
            this.editor.chain().focus().unsetAllMarks().clearNodes().run()
            this.openPanel = null
        },

        updateSlashMenu() {
            if (!this.editor) return

            const { state, view } = this.editor
            const { from, empty, $from } = state.selection

            if (!empty) {
                this.closeSlashMenu()
                return
            }

            const textBefore = $from.parent.textBetween(0, $from.parentOffset, undefined, '\ufffc')
            const match = textBefore.match(/\/([a-zA-Z0-9-]*)$/)

            if (!match) {
                this.closeSlashMenu()
                return
            }

            const coords = view.coordsAtPos(from)
            const wrapper = this.$refs.editorWrapper

            if (!wrapper) return

            const wrapperRect = wrapper.getBoundingClientRect()

            const query = match[1] || ''
            const fromPos = from - match[0].length

            this.slashMenu.isOpen = true
            this.slashMenu.query = query
            this.slashMenu.range = { from: fromPos, to: from }
            this.slashMenu.selectedIndex = 0

            this.slashMenu.x = coords.left - wrapperRect.left + wrapper.scrollLeft
            this.slashMenu.y = coords.bottom - wrapperRect.top + wrapper.scrollTop + 8
        },

        closeSlashMenu() {
            this.slashMenu.isOpen = false
            this.slashMenu.query = ''
            this.slashMenu.range = null
            this.slashMenu.selectedIndex = 0
        },

        runSlashCommand(item) {
            if (!this.slashMenu.range) return

            item.command({
                editor: this.editor,
                range: this.slashMenu.range,
            })

            this.closeSlashMenu()
        },
    },
}
</script>

