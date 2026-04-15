<template>
  <div>
    <v-list-item
      :to="page.to"
      :link="!!page.to"
      :style="{ paddingLeft: `${depth * 12 + 8}px` }"
      rounded="lg"
      class="sidebar-item"
      @click="handleClick"
      @mouseenter="isHovered = true"
      @mouseleave="isHovered = false"
    >
      <template #prepend>
        <v-btn
          v-if="page.canHaveChildren"
          :icon="isHovered ? 'mdi-chevron-right' : page.icon"
          rounded
          :style="{
            transform: isOpen ? 'rotate(35deg)' : 'rotate(0deg)',
            transition: 'transform 0.2s ease-in-out',
          }"
          style="color: #5f5f5f"
          size="small"
          variant="text"
          density="compact"
          @click.stop="handleIconClick"
        />
        <v-icon v-else size="small" style="color: #5f5f5f">
          {{ page.icon }}
        </v-icon>
      </template>

      <v-list-item-title style="color: #5f5f5f; user-select: none">
        {{ page.title }}
      </v-list-item-title>

      <template #append>
        <FunctionButtons v-if="!page.to && !page.hideActions" />
      </template>
    </v-list-item>

    <template v-if="isOpen && page.canHaveChildren">
      <template v-if="page.children?.length">
        <SidebarItem
          v-for="child in page.children"
          :key="child.id"
          :page="child"
          :depth="depth + 1"
        />
      </template>

      <div
        v-else
        :style="{
          paddingLeft: `${depth * 12 + 32}px`,
          color: '#666',
          fontSize: '0.875rem',
        }"
      >
        No pages inside
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import FunctionButtons from './FunctionButtons.vue'

defineProps({
  page: {
    type: Object,
    required: true,
  },
  depth: {
    type: Number,
    default: 0,
  },
})

const isOpen = ref(false)
const isHovered = ref(false)

function handleClick() {
}

function handleIconClick() {
  isOpen.value = !isOpen.value
}
</script>

