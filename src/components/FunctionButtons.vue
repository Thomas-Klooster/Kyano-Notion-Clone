<template>
    <div class="function-buttons-container">


        <v-menu>

            <template v-slot:activator="{ props: menu }">
                <v-tooltip location="bottom" text="Delete, Duplicate and More...">
                    <template v-slot:activator="{ props: tooltip }">
                        <v-btn rounded icon size="small" density="compact" variant="text"
                            v-bind="mergeProps(menu, tooltip)">
                            <v-icon size="small">mdi-dots-horizontal</v-icon>
                        </v-btn>
                    </template>
                </v-tooltip>
            </template>


            <v-list @mouseenter="drawer = true">
                <v-list-item v-for="(item, index) in items" :key="index" :value="index" @mouseenter="drawer = true"
                    style="user-select: none;">
                    <v-list-item-title> {{ item.title }} </v-list-item-title>
                </v-list-item>
            </v-list>

        </v-menu>


        <v-tooltip location="bottom" text="Add a page inside">
            <template v-slot:activator="{ props }">
                <v-btn rounded icon size="small" density="compact" variant="text" v-bind="props">
                    <v-icon size="small">mdi-plus</v-icon>
                </v-btn>
            </template>
        </v-tooltip>
    </div>
</template>

<script setup>
import { mergeProps } from 'vue'

const items = [
    { title: 'Add to Favorites' },
    { title: 'Copy link' },
    { title: 'Duplicate' },
    { title: 'Rename' },
    { title: 'Move to' },
    { title: 'Move to Trash' },
]
</script>

<style>
.function-buttons-container {
    visibility: hidden;
    opacity: 0;
    transition: visibility 0s, opacity 0.2s;
}

.v-list-item:hover .function-buttons-container {
    visibility: visible;
    opacity: 1;
}
</style>