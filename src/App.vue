<script setup>
import { ref } from 'vue'

const drawer = ref(false)
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const breadcrumbItems = computed(() => {
  return route.matched
    .filter(segment => segment.meta?.breadcrumb || segment.name)
    .map(segment => {
      const path = segment.path
        .replace(/:\w+/g, param => route.params[param.replace(':', '')])
      return {
        text: segment.meta?.breadcrumb || segment.name,
        to: { path },
        disabled: route.path === segment.path
      }
    })
})   
</script>


<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" permanent rounded @mouseleave="drawer = false">
      <v-list density="compact" :opened="opened" @update:opened="opened = $event">
        <v-list-item title="Notion Clone" subtitle="Built with Vuetify"></v-list-item>

        <v-divider></v-divider>

        <v-list-item link prepend-icon="mdi-home" to="/" title="Home"></v-list-item>
        <v-list-item link prepend-icon="mdi-cog" to="/settings" title="Settings"></v-list-item>
        <v-list-item link prepend-icon="mdi-headset" title="Meetings" to=""></v-list-item>
        <v-list-item link prepend-icon="mdi-inbox" title="Inbox"></v-list-item>
        <v-list-item link prepend-icon="mdi-bookshelf" title="Library"></v-list-item>

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
          <v-breadcrumbs :items="breadcrumbItems">
            <template v-slot:item="{ item }">
              <v-breadcrumbs-item :to="item.to" :disabled="item.disabled">
                {{ item.text }}
              </v-breadcrumbs-item>
            </template>
          </v-breadcrumbs>
        </template>
      </v-app-bar>
      <v-container>
        <RouterView />
      </v-container>
    </v-main>
  </v-app>
</template>

<style scoped></style>
