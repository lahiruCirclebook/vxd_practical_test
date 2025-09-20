<template>
  <div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
      <div class="flex items-center space-x-4">
        <button @click="$router.go(-1)" class="p-2 text-gray-400 hover:text-gray-600 rounded-md">
          <ArrowLeftIcon class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Form Preview</h1>
          <p class="text-gray-600 mt-1">{{ form.name }}</p>
        </div>
      </div>

      <div class="flex space-x-3">
        <button @click="copyUrl" class="btn-secondary inline-flex items-center">
          <LinkIcon class="w-4 h-4 mr-2" />
          Copy URL
        </button>

        <button class="btn-secondary inline-flex items-center">
          <DownloadIcon class="w-4 h-4 mr-2" />
          Export
        </button>

        <button class="btn-primary inline-flex items-center">
          <ShareIcon class="w-4 h-4 mr-2" />
          Share
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="card text-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p class="text-gray-600">Loading form...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="loadError" class="card text-center py-12">
      <div class="text-red-500 mb-4">
        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          ></path>
        </svg>
      </div>
      <p class="text-gray-600 mb-4">{{ loadError }}</p>
      <button @click="loadForm" class="btn-primary">Try Again</button>
    </div>

    <!-- Form Preview -->
    <div v-else class="max-w-2xl mx-auto">
      <div class="card">
        <!-- Form Header -->
        <div class="mb-8 text-center">
          <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ form.name }}</h2>
          <p v-if="form.description" class="text-gray-600">{{ form.description }}</p>
        </div>

        <!-- Success Message -->
        <div
          v-if="submitted"
          class="mb-8 p-4 bg-green-50 border border-green-200 rounded-lg text-center"
        >
          <CheckCircleIcon class="w-8 h-8 text-green-600 mx-auto mb-2" />
          <h3 class="text-lg font-semibold text-green-900 mb-1">Form Submitted Successfully!</h3>
          <p class="text-green-700">Thank you for your submission. We'll get back to you soon.</p>
          <button @click="resetForm" class="mt-4 text-green-700 hover:text-green-800 font-medium">
            Submit Another Response
          </button>
        </div>

        <!-- Form Fields -->
        <form v-else @submit.prevent="submitForm" class="space-y-6">
          <div v-for="field in form.fields" :key="field.id" class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ field.label }}
              <span v-if="field.required" class="text-red-500 ml-1">*</span>
            </label>

            <!-- Text Input -->
            <input
              v-if="field.type === 'text'"
              v-model="formData[`field_${field.id}`]"
              type="text"
              :required="field.required"
              class="form-input"
              :class="{ 'border-red-300': errors[`field_${field.id}`] }"
              placeholder="Enter your response"
            />

            <!-- Textarea -->
            <textarea
              v-else-if="field.type === 'textarea'"
              v-model="formData[`field_${field.id}`]"
              :required="field.required"
              rows="4"
              class="form-input"
              :class="{ 'border-red-300': errors[`field_${field.id}`] }"
              placeholder="Enter your response"
            ></textarea>

            <!-- Radio Buttons -->
            <div v-else-if="field.type === 'radio'" class="space-y-2">
              <div v-for="option in field.options" :key="option" class="flex items-center">
                <input
                  v-model="formData[`field_${field.id}`]"
                  :value="option"
                  type="radio"
                  :required="field.required"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                />
                <label class="ml-2 text-sm text-gray-900">{{ option }}</label>
              </div>
            </div>

            <!-- Checkboxes -->
            <div v-else-if="field.type === 'checkbox'" class="space-y-2">
              <div v-for="option in field.options" :key="option" class="flex items-center">
                <input
                  v-model="formData[`field_${field.id}`]"
                  :value="option"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label class="ml-2 text-sm text-gray-900">{{ option }}</label>
              </div>
            </div>

            <!-- Error Message -->
            <p v-if="errors[`field_${field.id}`]" class="text-red-600 text-sm mt-1">
              {{ errors[`field_${field.id}`] }}
            </p>
          </div>

          <!-- Submit Button -->
          <div class="pt-6 border-t border-gray-200">
            <!-- Submission Error -->
            <div v-if="errors.submit" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
              <p class="text-red-700 text-sm">{{ errors.submit }}</p>
            </div>

            <button
              type="submit"
              :disabled="submitting"
              class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="submitting">
                <div
                  class="animate-spin rounded-full h-4 w-4 border-b-2 border-white inline-block mr-2"
                ></div>
                Submitting...
              </span>
              <span v-else> Submit Form </span>
            </button>
          </div>
        </form>
      </div>

      <!-- Form Info -->
      <div class="mt-8 card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Form Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
          <div>
            <span class="text-gray-500">Fields:</span>
            <span class="ml-2 font-medium">{{ form.fields?.length || 0 }}</span>
          </div>
          <div>
            <span class="text-gray-500">Required fields:</span>
            <span class="ml-2 font-medium">{{ requiredFieldsCount }}</span>
          </div>
          <div>
            <span class="text-gray-500">Created:</span>
            <span class="ml-2 font-medium">{{ formatDate(form.created_at) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Copy URL Toast -->
    <div
      v-if="showCopyToast"
      class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg"
    >
      URL copied to clipboard!
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormPreviewView',
}
</script>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { formService } from '../services/formService'
import { ArrowLeftIcon, LinkIcon, DownloadIcon, ShareIcon, CheckCircleIcon } from 'lucide-vue-next'

const props = defineProps(['id'])
const route = useRoute()

const loading = ref(true)
const submitting = ref(false)
const submitted = ref(false)
const showCopyToast = ref(false)
const form = ref({})
const formData = ref({})
const errors = ref({})
const loadError = ref(null)

const requiredFieldsCount = computed(() => {
  return form.value.fields?.filter((field) => field.required).length || 0
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const loadForm = async () => {
  try {
    loading.value = true
    loadError.value = null
    const formId = props.id || route.params.id
    const data = await formService.getForm(formId)
    form.value = data

    // Initialize form data
    initializeFormData()
  } catch (error) {
    console.error('Failed to load form:', error)
    loadError.value = 'Failed to load form. Please refresh the page and try again.'
  } finally {
    loading.value = false
  }
}

const initializeFormData = () => {
  const data = {}
  form.value.fields?.forEach((field) => {
    if (field.type === 'checkbox') {
      data[`field_${field.id}`] = []
    } else {
      data[`field_${field.id}`] = ''
    }
  })
  formData.value = data
}

const validateForm = () => {
  const newErrors = {}
  let isValid = true

  form.value.fields?.forEach((field) => {
    const fieldKey = `field_${field.id}`
    const value = formData.value[fieldKey]

    if (field.required) {
      if (field.type === 'checkbox') {
        if (!value || value.length === 0) {
          newErrors[fieldKey] = 'This field is required'
          isValid = false
        }
      } else {
        if (!value || value.trim() === '') {
          newErrors[fieldKey] = 'This field is required'
          isValid = false
        }
      }
    }
  })

  errors.value = newErrors
  return isValid
}

const submitForm = async () => {
  if (!validateForm()) {
    return
  }

  try {
    submitting.value = true
    const formId = props.id || route.params.id
    await formService.submitForm(formId, formData.value)
    submitted.value = true

    // Reset form data and errors
    initializeFormData()
    errors.value = {}
  } catch (error) {
    console.error('Failed to submit form:', error)

    // Show user-friendly error message
    errors.value = {
      ...errors.value,
      submit: 'Failed to submit form. Please check your connection and try again.',
    }
  } finally {
    submitting.value = false
  }
}

const resetForm = () => {
  submitted.value = false
  initializeFormData()
  errors.value = {} // This will clear both field errors and submission errors
}

const copyUrl = async () => {
  try {
    const url = window.location.href
    await navigator.clipboard.writeText(url)
    showCopyToast.value = true
    setTimeout(() => {
      showCopyToast.value = false
    }, 3000)
  } catch (error) {
    console.error('Failed to copy URL:', error)
  }
}

onMounted(() => {
  loadForm()
})
</script>
