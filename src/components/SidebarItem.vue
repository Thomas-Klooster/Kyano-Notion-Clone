<template>
    <div>
        <v-list-item :style="{ paddingLeft: `${depth * 12 + 8}px` }" rounded="lg" class="sidebar-item"
            @click="handleClick" @mouseenter="isHovered = true" @mouseleave="isHovered = false">
            <template #prepend>
                <v-btn v-if="page.children?.length || page.canHaveChildren"
                    :icon="isHovered ? (isOpen ? 'mdi-chevron-down' : 'mdi-chevron-right') : page.icon" size="small"
                    rounded variant="" density="compact" @click.stop="handleIconClick" />
                <v-icon v-else size="small">
                    {{ page.icon }}
                </v-icon>
            </template>

            <v-list-item-title>
                {{ page.title }}
            </v-list-item-title>

            <template v-slot:append>
                <FunctionButtons />
            </template>
        </v-list-item>

        <template v-if="isOpen && page.children?.length">
            <SidebarItem v-for="child in page.children" :key="child.id" :page="child" :depth="depth + 1" />
        </template>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import FunctionButtons from './FunctionButtons.vue'

const props = defineProps({
    page: Object,
    depth: {
        type: Number,
        default: 0
    }
})

const isOpen = ref(false)
const isHovered = ref(false)

function handleClick() {
    navigateTo(props.page)
}

function handleIconClick() {
    if (props.page.children?.length) {
        isOpen.value = !isOpen.value
    } else if (props.page.canHaveChildren) {
        onAddChildPage(props.page)
    }
}

function navigateTo(page) {
    console.log('Navigate to:', page.title)
}

function onAddChildPage(page) {
    console.log('Add page to:', page.title)
}
</script>