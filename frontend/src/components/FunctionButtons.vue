
<template>
    <div class="function-buttons-container">
        <v-menu>
            <template v-slot:activator="{ props: menu }">
                <v-tooltip location="bottom" text="Delete, Duplicate and More...">
                    <template v-slot:activator="{ props: tooltip }">
                        <v-btn rounded icon size="small" density="compact" variant="text" style="color: #5f5f5f"
                            v-bind="mergeProps(menu, tooltip)">
                            <v-icon size="small">mdi-dots-horizontal</v-icon>
                        </v-btn>
                    </template>
                </v-tooltip>
            </template>

            <v-list @mouseenter="drawer = true" rounded="lg">
                <template v-for="(item, index) in items" :key="index">
                    <v-divider v-if="item.divider" style="margin: 0.5rem;" />

                    <v-list-item v-else :value="index" @mouseenter="drawer = true" rounded
                        style="user-select: none; min-width: 10rem; min-height: 1.25rem; margin-inline: 0.5rem">
                        <template v-slot:prepend>
                            <v-icon :icon="item.icon" size="small" />
                        </template>
                        <v-list-item-title>{{ item.title }}</v-list-item-title>
                    </v-list-item>
                </template>
            </v-list>
        </v-menu>

        <v-tooltip location="bottom" text="Add a page inside">
            <template v-slot:activator="{ props }">
                <v-btn rounded icon size="small" density="compact" variant="text" v-bind="props" style="color: #5f5f5f">
                    <v-icon size="small">mdi-plus</v-icon>
                </v-btn>
            </template>
        </v-tooltip>
    </div>
</template>

<script setup>
import { mergeProps } from 'vue'

const items = [
    { title: 'Add to Favorites', icon: 'mdi-star' },
    { divider: true },
    { title: 'Copy link', icon: 'mdi-link-variant' },
    { title: 'Duplicate', icon: 'mdi-checkbox-multiple-blank-outline' },
    { title: 'Rename', icon: 'mdi-square-edit-outline' },
    { divider: true },
    { title: 'Move to', icon: 'mdi-arrow-right-top' },
    { title: 'Move to Trash', icon: 'mdi-trash-can-outline' },
]
</script>