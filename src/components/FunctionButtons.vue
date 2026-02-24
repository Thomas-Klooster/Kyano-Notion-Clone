<template>
    <div class="function-buttons-container">


        <v-menu>

            <template v-slot:activator="{ props: menu }">
                <v-tooltip location="bottom" text="Delete, Duplicate and More...">
                    <template v-slot:activator="{ props: tooltip }">
                        <v-btn icon size="x-small" variant="text" v-bind="mergeProps(menu, tooltip)">
                            <v-icon>mdi-dots-horizontal</v-icon>
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
                <v-btn icon size="x-small" variant="text" v-bind="props">
                    <v-icon>mdi-plus</v-icon>
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