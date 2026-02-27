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
const open = ref(['Private'])

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
    <v-navigation-drawer v-model="drawer" permanent rounded>
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

        <v-list density="compact">
          <v-list-group data-depth="1">
            <template v-slot:activator="{ props }">
              <v-list-subheader v-bind="props" title="Recents"></v-list-subheader>
            </template>

            <v-list-item prepend-icon="mdi-plus" title="Add New" link></v-list-item>
          </v-list-group>
        </v-list>

        <v-list density="compact" v-model:opened="open">

          <v-list-group data-depth="1">

            <template v-slot:activator="{ props }">
              <v-list-subheader v-bind="props" prepend-icon="mdi-account-circle" title="Private"></v-list-subheader>
            </template>

            <v-list-group data-depth="2">

              <template v-slot:activator="{ props }">
                <v-list-item v-bind="props" title="Group 1" style="user-select: none;">
                  <template v-slot:prepend>
                    <v-icon size="small" icon="mdi-file-document-outline"></v-icon>
                  </template>
                  <template v-slot:append>
                    <FunctionButtons />
                  </template>
                </v-list-item>
              </template>


              <v-list-item data-depth="3" v-for="([title, icon], i) in admins" :key="i" :title="title" :value="title"
                style="user-select: none;">
                <template v-slot:prepend>
                  <v-icon size="small" :icon="icon"></v-icon>
                </template>
                <template v-slot:append>
                  <FunctionButtons />
                </template>
              </v-list-item>

            </v-list-group>

            <v-list-group data-depth="2">

              <template v-slot:activator="{ props }">
                <v-list-item v-bind="props" title="Group 2" style="user-select: none;">
                  <template v-slot:prepend>
                    <v-icon size="small" icon="mdi-file-document-outline"></v-icon>
                  </template>
                  <template v-slot:append>
                    <FunctionButtons />
                  </template>
                </v-list-item>
              </template>

              <v-list-item data-depth="3" v-for="([title, icon], i) in cruds" :key="i" :title="title" :value="title"
                style="user-select: none;">
                <template v-slot:prepend>
                  <v-icon size="small" :icon="icon"></v-icon>
                </template>
                <template v-slot:append>
                  <FunctionButtons />
                </template>
              </v-list-item>

            </v-list-group>

            <v-list-item prepend-icon="mdi-plus" title="Add New" link></v-list-item>

          </v-list-group>

          <v-list density="compact" nav>
            <SidebarItem v-for="page in pages" :key="page.id" :page="page" :depth="0" />
          </v-list>

        </v-list>

      </v-list>

      <v-divider></v-divider>
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
        <router-view />
      </v-container>
    </v-main>
  </v-app>
</template>

<style scoped>
[data-depth="1"] {
  --depth: 1
}

[data-depth="2"] {
  --depth: 2
}

[data-depth="3"] {
  --depth: 3
}

[data-depth="4"] {
  --depth: 4
}

:deep(.v-list-group__items) {
  --indent-padding: calc(var(--depth, 1) * .75rem) !important;
}

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