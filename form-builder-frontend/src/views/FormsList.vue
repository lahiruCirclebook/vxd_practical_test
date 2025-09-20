<template>
  <div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Forms</h1>
        <p class="text-gray-600 mt-2">Manage your forms and view submissions</p>
      </div>
      <RouterLink to="/forms/create" class="btn-primary inline-flex items-center">
        <PlusIcon class="w-4 h-4 mr-2" />
        Create Form
      </RouterLink>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="card text-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p class="text-gray-600">Loading forms...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="card text-center py-12">
      <div class="text-red-500 mb-4">
        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
      <p class="text-gray-600 mb-4">{{ error }}</p>
      <button @click="fetchForms" class="btn-primary">Try Again</button>
    </div>

    <!-- Empty State -->
    <div v-else-if="forms.length === 0" class="card text-center py-12">
      <FileTextIcon class="w-16 h-16 mx-auto mb-4 text-gray-400" />
      <h3 class="text-lg font-medium text-gray-900 mb-2">No forms yet</h3>
      <p class="text-gray-600 mb-6">Create your first form to get started collecting responses.</p>
      <RouterLink to="/forms/create" class="btn-primary inline-flex items-center">
        <PlusIcon class="w-4 h-4 mr-2" />
        Create your first form
      </RouterLink>
    </div>

    <!-- Forms Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="form in forms" :key="form.id" class="card hover:shadow-md transition-shadow">
        <!-- Form Header -->
        <div class="flex justify-between items-start mb-4">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ form.name }}</h3>
            <p class="text-sm text-gray-600">{{ form.description || 'No description' }}</p>
          </div>

          <!-- Form Actions Dropdown -->
          <div class="relative ml-4">
            <button
              @click="toggleDropdown(form.id)"
              class="p-2 text-gray-400 hover:text-gray-600 rounded-md"
            >
              <MoreVerticalIcon class="w-4 h-4" />
            </button>

            <div
              v-if="activeDropdown === form.id"
              class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10"
            >
              <RouterLink
                :to="`/forms/${form.id}/edit`"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                @click="activeDropdown = null"
              >
                <EditIcon class="w-4 h-4 mr-3" />
                Edit form
              </RouterLink>

              <RouterLink
                :to="`/forms/${form.id}/preview`"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                @click="activeDropdown = null"
              >
                <EyeIcon class="w-4 h-4 mr-3" />
                Preview form
              </RouterLink>

              <RouterLink
                :to="`/forms/${form.id}/submissions`"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                @click="activeDropdown = null"
              >
                <BarChart3Icon class="w-4 h-4 mr-3" />
                View submissions
              </RouterLink>

              <hr class="my-1" />

              <button
                @click="deleteForm(form)"
                class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50"
              >
                <TrashIcon class="w-4 h-4 mr-3" />
                Delete form
              </button>
            </div>
          </div>
        </div>

        <!-- Form Stats -->
        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
          <span class="flex items-center">
            <CalendarIcon class="w-4 h-4 mr-1" />
            Created {{ formatDate(form.created_at) }}
          </span>
          <span class="flex items-center">
            <BarChart3Icon class="w-4 h-4 mr-1" />
            {{ form.submissions_count }} submissions
          </span>
        </div>

        <!-- Form Actions -->
        <div class="flex space-x-2">
          <RouterLink :to="`/forms/${form.id}/edit`" class="btn-primary flex-1 text-center">
            <EditIcon class="w-4 h-4 mr-2 inline" />
            Edit
          </RouterLink>

          <RouterLink :to="`/forms/${form.id}/preview`" class="btn-secondary flex-1 text-center">
            <EyeIcon class="w-4 h-4 mr-2 inline" />
            Preview
          </RouterLink>
        </div>

        <!-- Submissions Badge -->
        <div class="mt-4 pt-4 border-t border-gray-200">
          <RouterLink
            :to="`/forms/${form.id}/submissions`"
            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
          >
            {{ form.submissions_count }} submissions →
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Delete Form</h3>
        <p class="text-gray-600 mb-6">
          Are you sure you want to delete "{{ formToDelete?.name }}"? This action cannot be undone
          and will also delete all submissions.
        </p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="btn-secondary">Cancel</button>
          <button
            @click="confirmDelete"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormsListView'
}
</script>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import { formService } from '../services/formService'
import {
  PlusIcon,
  FileTextIcon,
  EditIcon,
  EyeIcon,
  BarChart3Icon,
  TrashIcon,
  CalendarIcon,
  MoreVerticalIcon,
} from 'lucide-vue-next'

const loading = ref(true)
const forms = ref([])
const activeDropdown = ref(null)
const showDeleteModal = ref(false)
const formToDelete = ref(null)
const error = ref(null)

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const fetchForms = async () => {
  try {
    loading.value = true
    error.value = null
    const data = await formService.getForms()
    forms.value = data
  } catch (err) {
    console.error('Failed to fetch forms:', err)
    error.value = 'Failed to load forms. Please try again.'
  } finally {
    loading.value = false
  }
}

const toggleDropdown = (formId) => {
  activeDropdown.value = activeDropdown.value === formId ? null : formId
}

const deleteForm = (form) => {
  formToDelete.value = form
  showDeleteModal.value = true
  activeDropdown.value = null
}

const confirmDelete = async () => {
  try {
    await formService.deleteForm(formToDelete.value.id)
    forms.value = forms.value.filter((form) => form.id !== formToDelete.value.id)
    showDeleteModal.value = false
    formToDelete.value = null
  } catch (error) {
    console.error('Failed to delete form:', error)
  }
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  // More specific check for dropdown elements
  if (!event.target.closest('.relative') && !event.target.closest('[data-dropdown]')) {
    activeDropdown.value = null
  }
}

onMounted(() => {
  fetchForms()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
