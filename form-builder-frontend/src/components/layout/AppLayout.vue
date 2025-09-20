<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <div class="w-64 bg-white border-r border-gray-200 flex flex-col">
      <!-- Logo/Brand -->
      <div class="p-6 border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-900">Form Builder</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your forms</p>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-2">
        <RouterLink
          to="/dashboard"
          class="nav-link"
          :class="{ 'nav-link-active': $route.name === 'dashboard' }"
        >
          <Home class="w-5 h-5" />
          Dashboard
        </RouterLink>

        <RouterLink
          to="/forms"
          class="nav-link"
          :class="{ 'nav-link-active': $route.name === 'forms' }"
        >
          <FileText class="w-5 h-5" />
          Forms
        </RouterLink>

        <RouterLink
          to="/forms/create"
          class="nav-link"
          :class="{ 'nav-link-active': $route.name === 'form-create' }"
        >
          <Plus class="w-5 h-5" />
          Create Form
        </RouterLink>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-4"></div>

        <!-- Dynamic sections -->
        <template v-if="currentFormId">
          <RouterLink
            :to="`/forms/${currentFormId}/preview`"
            class="nav-link"
            :class="{ 'nav-link-active': $route.name === 'form-preview' }"
          >
            <Eye class="w-5 h-5" />
            Preview
          </RouterLink>

          <RouterLink
            :to="`/forms/${currentFormId}/submissions`"
            class="nav-link"
            :class="{ 'nav-link-active': $route.name === 'form-submissions' }"
          >
            <BarChart3 class="w-5 h-5" />
            Submissions
          </RouterLink>
        </template>

        <!-- Settings -->
        <div class="border-t border-gray-200 my-4"></div>
        <div class="nav-link opacity-50 cursor-not-allowed">
          <Settings class="w-5 h-5" />
          Settings <span class="text-xs">(Coming Soon)</span>
        </div>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { RouterLink, useRoute } from 'vue-router'
import { computed } from 'vue'
import {
  HomeIcon as Home,
  FileTextIcon as FileText,
  PlusIcon as Plus,
  BarChart3Icon as BarChart3,
  EyeIcon as Eye,
  SettingsIcon as Settings,
} from 'lucide-vue-next'

const route = useRoute()

const currentFormId = computed(() => {
  return route.params.id || null
})
</script>

<style scoped>
.nav-link {
  @apply flex items-center space-x-3 px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors;
}

.nav-link-active {
  @apply bg-blue-50 text-blue-700;
}
</style>
