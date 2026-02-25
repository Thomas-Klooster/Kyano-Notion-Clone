<template>
    <div>
        <v-list-item :style="{ paddingLeft: `${depth * 12 + 8}px` }" rounded="lg" class="sidebar-item"
            @click="handleClick" @mouseenter="isHovered = true" @mouseleave="isHovered = false">
            <template #prepend>
                <v-btn v-if="props.page.canHaveChildren" :icon="isHovered ? 'mdi-chevron-right' : props.page.icon"
                    rounded
                    :style="{ transform: isOpen ? 'rotate(35deg)' : 'rotate(0deg)', transition: 'transform 0.2s ease-in-out' }"
                    style="color: #5f5f5f;" size="small" variant="text" density="compact"
                    @click.stop="handleIconClick" />
                <v-icon v-else size="small" style="color: #5f5f5f;">
                    {{ props.page.icon }}
                </v-icon>
            </template>

            <v-list-item-title style="color: #5f5f5f; user-select: none;">
                {{ props.page.title }}
            </v-list-item-title>

            <template v-slot:append>
                <FunctionButtons />
            </template>
        </v-list-item>

        <template v-if="isOpen && props.page.canHaveChildren">
            <template v-if="props.page.children?.length">
                <SidebarItem v-for="child in props.page.children" :key="child.id" :page="child" :depth="depth + 1" />
            </template>
            <div v-else :style="{
                paddingLeft: `${depth * 12 + 32}px`,
                color: '#666',
                fontSize: '0.875rem',
            }">
                No pages inside
            </div>
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
    isOpen.value = !isOpen.value
}

function navigateTo(page) {
    console.log('Navigate to:', page.title)
}
</script>

<style scoped>
:deep(.v-list-item--density-compact) {
    min-height: 0 !important;
    padding-block: 2px;
}
</style>