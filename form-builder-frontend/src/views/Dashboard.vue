<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
      <p class="text-gray-600 mt-2">Overview of your form building activity</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="card">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <FileTextIcon class="w-6 h-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Forms</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalForms }}</p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <CheckCircleIcon class="w-6 h-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Submissions</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalSubmissions }}</p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center">
          <div class="p-2 bg-purple-100 rounded-lg">
            <TrendingUpIcon class="w-6 h-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Active Forms</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.activeForms }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Forms -->
    <div class="card">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Recent Forms</h2>
        <RouterLink to="/forms" class="text-blue-600 hover:text-blue-700 font-medium">
          View all forms →
        </RouterLink>
      </div>

      <div v-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
      </div>

      <div v-else-if="recentForms.length === 0" class="text-center py-8 text-gray-500">
        <FileTextIcon class="w-12 h-12 mx-auto mb-4 opacity-50" />
        <p>No forms created yet</p>
        <RouterLink to="/forms/create" class="btn-primary mt-4 inline-flex items-center">
          <PlusIcon class="w-4 h-4 mr-2" />
          Create your first form
        </RouterLink>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="form in recentForms"
          :key="form.id"
          class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <div class="flex-1">
            <h3 class="font-medium text-gray-900">{{ form.name }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ form.description || 'No description' }}</p>
            <p class="text-xs text-gray-400 mt-2">Created {{ formatDate(form.created_at) }}</p>
          </div>

          <div class="flex items-center space-x-2">
            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
              {{ form.submissions_count }} submissions
            </span>

            <div class="flex space-x-1">
              <RouterLink
                :to="`/forms/${form.id}/edit`"
                class="p-2 text-gray-400 hover:text-blue-600 rounded-md"
                title="Edit form"
              >
                <EditIcon class="w-4 h-4" />
              </RouterLink>

              <RouterLink
                :to="`/forms/${form.id}/preview`"
                class="p-2 text-gray-400 hover:text-green-600 rounded-md"
                title="Preview form"
              >
                <EyeIcon class="w-4 h-4" />
              </RouterLink>

              <RouterLink
                :to="`/forms/${form.id}/submissions`"
                class="p-2 text-gray-400 hover:text-purple-600 rounded-md"
                title="View submissions"
              >
                <BarChart3Icon class="w-4 h-4" />
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardView'
}
</script>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { formService } from '../services/formService'
import {
  FileTextIcon,
  CheckCircleIcon,
  TrendingUpIcon,
  PlusIcon,
  EditIcon,
  EyeIcon,
  BarChart3Icon,
} from 'lucide-vue-next'

const loading = ref(true)
const recentForms = ref([])
const stats = ref({
  totalForms: 0,
  totalSubmissions: 0,
  activeForms: 0,
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const fetchDashboardData = async () => {
  try {
    loading.value = true
    const forms = await formService.getForms()

    // Calculate stats
    stats.value.totalForms = forms.length
    stats.value.activeForms = forms.filter((form) => form.is_active).length
    stats.value.totalSubmissions = forms.reduce((total, form) => total + form.submissions_count, 0)

    // Get recent forms (last 5)
    recentForms.value = forms.slice(0, 5)
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>
