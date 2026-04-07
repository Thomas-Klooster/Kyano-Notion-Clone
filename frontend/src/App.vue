<script setup>
import { ref } from 'vue'

const drawer = ref(false)
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import FunctionButtons from '@/components/FunctionButtons.vue'
import SidebarItem from './components/SidebarItem.vue'

const pages = ref([
  {
    id: '1',
    title: 'Page 1',
    icon: 'mdi-file-document-outline',
    canHaveChildren: true,
    children: [
      {
        id: '1-1',
        title: 'Page 1-1',
        icon: 'mdi-file-document-outline',
        canHaveChildren: true,
        children: []
      },
      {
        id: '1-2',
        title: 'Page 1-2',
        icon: 'mdi-file-document-outline',
        canHaveChildren: true,
        children: [
          {
            id: '1-2-1',
            title: 'Page 1-2-1',
            icon: 'mdi-file-document-outline',
            canHaveChildren: true,
            children: []
          },
          {
            id: '1-2-2',
            title: 'Page 1-2-2',
            icon: 'mdi-file-document-outline',
            canHaveChildren: true,
            children: [
              {
                id: '1-2-2-1',
                title: 'Page 1-2-2-1',
                icon: 'mdi-file-document-outline',
                canHaveChildren: true,
              },
              {
                id: '1-2-2-2',
                title: 'Page 1-2-2-2',
                icon: 'mdi-file-document-outline',
                canHaveChildren: true,
              },
            ]
          }
        ]
      }
    ]
  }, {
    id: '2',
    title: 'Page 2',
    icon: 'mdi-file-document-outline',
    canHaveChildren: true,
    children: [
      {
        id: '2-1',
        title: 'Page 2-1',
        icon: 'mdi-file-document-outline',
        canHaveChildren: true,
        children: []
      },
      {
        id: '2-2',
        title: 'Page 2-2',
        icon: 'mdi-file-document-outline',
        canHaveChildren: true,
        children: [
          {
            id: '2-2-1',
            title: 'Page 2-2-1',
            icon: 'mdi-file-document-outline',
            canHaveChildren: true,
            children: []
          },
          {
            id: '2-2-2',
            title: 'Page 2-2-2',
            icon: 'mdi-file-document-outline',
            canHaveChildren: true,
            children: [
              {
                id: '2-2-2-1',
                title: 'Page 2-2-2-1',
                icon: 'mdi-file-document-outline',
                canHaveChildren: true,
              },
              {
                id: '2-2-2-2',
                title: 'Page 2-2-2-2',
                icon: 'mdi-file-document-outline',
                canHaveChildren: true,
              },
            ]
          }
        ]
      }
    ]
  },
])

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
const opened = ref(['Private'])

const admins = [
  ['Page 1', 'mdi-file-document-outline'],
  ['Page 2', 'mdi-file-document-outline'],
]
const cruds = [
  ['Page 3', 'mdi-file-document-outline'],
  ['Page 4', 'mdi-file-document-outline'],
  ['Page 5', 'mdi-file-document-outline'],
  ['Page 6', 'mdi-file-document-outline'],
]
const isHovering = ref(false)
</script>


<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" permanent rounded color="#fbfbfa">
      <v-list density="compact" :opened="opened" @update:opened="opened = $event">
        <v-list-item title="Notion Clone" subtitle="Built with Vuetify"></v-list-item>

        <v-list-item link prepend-icon="mdi-login" to="/auth/login" title="Login"></v-list-item>
        <v-list-item link prepend-icon="mdi-account-edit-outline" to="/auth/register" title="Register"></v-list-item>

        <v-divider></v-divider>

        <v-list-item link prepend-icon="mdi-home" to="/" title="Home"></v-list-item>
        <v-list-item link prepend-icon="mdi-cog" to="/settings" title="Settings"></v-list-item>
        <v-list-item link prepend-icon="mdi-headset" title="Meetings" to=""></v-list-item>
        <v-list-item link prepend-icon="mdi-inbox" title="Inbox"></v-list-item>
        <v-list-item link prepend-icon="mdi-bookshelf" title="Library"></v-list-item>

        <v-divider></v-divider>

        <v-list density="compact" nav>
          <SidebarItem v-for="page in pages" :key="page.id" :page="page" :depth="0" />
        </v-list>

      </v-list>

      <v-divider></v-divider>
    </v-navigation-drawer>

    <v-main @mouseenter="drawer = false">
      <div class="admin-page-shell">
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
        <!-- <v-container> -->
        <router-view />
        <!-- </v-container> -->
      </div>

    </v-main>
  </v-app>
</template>

<style scoped>
:deep(.v-list-item__spacer) {
  width: 0.5rem !important;
}

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