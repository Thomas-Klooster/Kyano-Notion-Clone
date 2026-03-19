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

<style scoped>
.editor-wrapper {
    position: relative;
    width: 100%;
}

.editor-shell {
    width: 100%;
    min-height: 220px;
    border-radius: 12px;
    transition: border-color .15s ease, box-shadow .15s ease;
}

.editor-shell:focus-within {
    box-shadow: 0 0 0 1px rgba(15, 23, 42, .04);
}

:deep(.tiptap) {
    min-height: 220px;
    padding: 20px 24px;
    outline: none;
    box-sizing: border-box;
    color: #191919;
    line-height: 1.6;
}

:deep(.tiptap:focus) {
    outline: none;
}

:deep(.tiptap > *:first-child) {
    margin-top: 0;
}

:deep(.tiptap p) {
    margin: .35rem 0;
}

:deep(.tiptap h1) {
    margin: 0 0 .75rem;
    font-size: 2rem;
    line-height: 1.2;
    font-weight: 700;
}

:deep(.tiptap h2) {
    margin: 1.5rem 0 .5rem;
    font-size: 1.5rem;
    line-height: 1.25;
    font-weight: 700;
}

:deep(.tiptap h3) {
    margin: 1.25rem 0 .5rem;
    font-size: 1.25rem;
    line-height: 1.3;
    font-weight: 700;
}

:deep(.tiptap ul),
:deep(.tiptap ol) {
    margin: .5rem 0;
    padding-left: 1.4rem;
}

:deep(.tiptap li) {
    margin: .2rem 0;
}

:deep(.tiptap blockquote) {
    margin: .75rem 0;
    padding-left: 1rem;
    border-left: 3px solid #e5e7eb;
    color: #666;
}

:deep(.tiptap pre) {
    margin: .75rem 0;
    padding: 1rem 1.1rem;
    border-radius: 10px;
    background: #f7f7f5;
    border: 1px solid #e6e6e3;
    color: #2f3437;
    overflow-x: auto;
}

:deep(.tiptap pre code) {
    display: block;
    background: transparent;
    padding: 0;
    color: inherit;
    font-size: .9rem;
    line-height: 1.6;
    font-family: monospace;
}

:deep(.tiptap code) {
    background: transparent;
    padding: 0;
    color: inherit;
}

.notion-bubble-menu {
    position: relative;
    display: flex;
    align-items: center;
    gap: 2px;
    padding: 6px;
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    background: #fff;
    box-shadow:
        0 8px 24px rgba(15, 15, 15, .12),
        0 2px 8px rgba(15, 15, 15, .08);
}

.menu-item-wrapper {
    position: relative;
}

.menu-btn {
    height: 30px;
    min-width: 30px;
    padding: 0 10px;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #37352f;
    font-size: 14px;
    cursor: pointer;
}

.menu-btn:hover {
    background: #f1f1ef;
}

.menu-btn.active {
    background: #ececeb;
}

.submenu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    z-index: 1000;
    min-width: 220px;
    padding: 8px;
    border: 1px solid #e6e6e3;
    border-radius: 12px;
    background: #fff;
    box-shadow:
        0 14px 28px rgba(15, 15, 15, .12),
        0 6px 10px rgba(15, 15, 15, .08);
}

.submenu-title {
    margin-bottom: 8px;
    padding: 4px 8px;
    color: #787774;
    font-size: 12px;
}

.submenu-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 9px 10px;
    border: none;
    border-radius: 8px;
    background: transparent;
    text-align: left;
    color: #37352f;
    cursor: pointer;
}

.submenu-item:hover {
    background: #f7f7f5;
}

.link-submenu {
    width: 280px;
}

.link-input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d9d9d6;
    border-radius: 8px;
    outline: none;
    font-size: 14px;
}

.link-input:focus {
    border-color: #2383e2;
}

.submenu-actions {
    display: flex;
    gap: 8px;
    margin-top: 10px;
}

.submenu-action-btn {
    border: none;
    border-radius: 8px;
    padding: 8px 10px;
    cursor: pointer;
    background: #2383e2;
    color: white;
}

.submenu-action-btn.ghost {
    background: #f1f1ef;
    color: #37352f;
}

.slash-menu {
    position: absolute;
    z-index: 1100;
    width: 260px;
    padding: .25rem;
    border: 1px solid #e6e6e3;
    border-radius: 12px;
    background: #fff;
    box-shadow:
        0 14px 28px rgba(15, 15, 15, .12),
        0 6px 10px rgba(15, 15, 15, .08);
}

.slash-menu-item {
    display: flex;
    align-items: center;
    gap: .25rem;
    width: 100%;
    padding: .25rem;
    border: none;
    border-radius: 10px;
    background: transparent;
    text-align: left;
    cursor: pointer;
}

.slash-menu-item:hover,
.slash-menu-item.active {
    background: #f7f7f5;
}

.slash-menu-item-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    flex-shrink: 0;
    color: #37352f;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
}

.slash-menu-item-content {
    min-width: 0;
    flex: 1;
}

.slash-menu-item-title {
    font-size: .86rem;
}
</style>
