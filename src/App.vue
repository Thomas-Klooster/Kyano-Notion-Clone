<script setup>
import { ref } from 'vue'

const drawer = ref(false)
const items = [
  {
    title: 'Dashboard',
    disabled: false,
    href: 'test',
  },
  {
    title: 'Link 1',
    disabled: false,
    href: 'test',
  },
  {
    title: 'Link 2',
    disabled: true,
    href: 'test',
  },
]
</script>

<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" permanent rounded @mouseleave="drawer = false">
      <v-list density="compact" :opened="opened" @update:opened="opened = $event">
        <v-list-item title="Notion Clone" subtitle="Built with Vuetify"></v-list-item>
        <v-divider></v-divider>
        <v-list-item link prepend-icon="mdi-home" title="Home"></v-list-item>
        <v-list-item link prepend-icon="mdi-cog" title="Settings"></v-list-item>
        <v-list-subheader>Recents</v-list-subheader>
        <v-list-item prepend-icon="mdi-plus" title="Add New" link></v-list-item>
        <v-list-subheader>Private</v-list-subheader>
        <v-list-item prepend-icon="mdi-plus" title="Add New" link></v-list-item>
        <v-list-subheader>Teamspaces</v-list-subheader>
        <v-list-item prepend-icon="mdi-plus" title="Add New" link></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main @mouseenter="drawer = false">
      <v-app-bar :elevation="0" density="compact">
        <template v-slot:prepend>
          <v-app-bar-nav-icon @mouseenter="drawer = true"></v-app-bar-nav-icon>
          <v-breadcrumbs :items="items"></v-breadcrumbs>
        </template>
      </v-app-bar>
      <v-container>
        <router-view />
      </v-container>
    </v-main>
  </v-app>
</template>

<style scoped></style>
