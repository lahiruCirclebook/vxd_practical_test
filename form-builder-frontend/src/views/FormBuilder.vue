<template>
  <div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">
          {{ isEditing ? 'Edit Form' : 'Create Form' }}
        </h1>
        <p class="text-gray-600 mt-2">
          {{
            isEditing ? 'Update your form fields and settings' : 'Build a new form by adding fields'
          }}
        </p>
      </div>
      <div class="flex space-x-3">
        <button @click="$router.go(-1)" class="btn-secondary">Cancel</button>
        <button
          @click="saveForm"
          :disabled="!formData.name || formData.fields.length === 0 || saving"
          class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <SaveIcon class="w-4 h-4 mr-2" />
          {{ saving ? 'Saving...' : isEditing ? 'Update Form' : 'Save Form' }}
        </button>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
      <div class="flex items-center">
        <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-red-700">{{ error }}</p>
        <button @click="error = null" class="ml-auto text-red-500 hover:text-red-700">
          <XIcon class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="card text-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p class="text-gray-600">Loading form...</p>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Form Configuration -->
      <div class="lg:col-span-2">
        <div class="card mb-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Form Settings</h2>

          <!-- Form Title -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2"> Form Title * </label>
            <input
              v-model="formData.name"
              type="text"
              placeholder="Enter form title"
              class="form-input"
              required
            />
          </div>

          <!-- Form Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2"> Description </label>
            <textarea
              v-model="formData.description"
              rows="3"
              placeholder="Optional description for your form"
              class="form-input"
            ></textarea>
          </div>
        </div>

        <!-- Add Field Section -->
        <div class="card mb-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Add Fields</h2>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button
              @click="addField('text')"
              class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors"
            >
              <TypeIcon class="w-6 h-6 text-gray-400 mb-2" />
              <span class="text-sm font-medium">Text Input</span>
            </button>

            <button
              @click="addField('textarea')"
              class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors"
            >
              <AlignLeftIcon class="w-6 h-6 text-gray-400 mb-2" />
              <span class="text-sm font-medium">Text Area</span>
            </button>

            <button
              @click="addField('radio')"
              class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors"
            >
              <CircleDotIcon class="w-6 h-6 text-gray-400 mb-2" />
              <span class="text-sm font-medium">Radio Button</span>
            </button>

            <button
              @click="addField('checkbox')"
              class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors"
            >
              <CheckSquareIcon class="w-6 h-6 text-gray-400 mb-2" />
              <span class="text-sm font-medium">Checkbox</span>
            </button>
          </div>
        </div>

        <!-- Form Fields List -->
        <div class="card">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Form Fields</h2>
            <span class="text-sm text-gray-500">{{ formData.fields.length }} fields</span>
          </div>

          <div v-if="formData.fields.length === 0" class="text-center py-8 text-gray-500">
            <LayersIcon class="w-12 h-12 mx-auto mb-4 opacity-50" />
            <p>No fields added yet</p>
            <p class="text-sm">Use the buttons above to add fields to your form</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="(field, index) in formData.fields"
              :key="field.tempId || field.id"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="flex justify-between items-start mb-4">
                <div class="flex items-center space-x-2">
                  <span class="text-sm bg-gray-100 px-2 py-1 rounded">{{ field.type }}</span>
                  <span class="text-xs text-gray-500">#{{ index + 1 }}</span>
                </div>

                <div class="flex items-center space-x-2">
                  <!-- Move Up/Down -->
                  <button
                    @click="moveField(index, 'up')"
                    :disabled="index === 0"
                    class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-50"
                  >
                    <ChevronUpIcon class="w-4 h-4" />
                  </button>

                  <button
                    @click="moveField(index, 'down')"
                    :disabled="index === formData.fields.length - 1"
                    class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-50"
                  >
                    <ChevronDownIcon class="w-4 h-4" />
                  </button>

                  <!-- Delete Field -->
                  <button @click="removeField(index)" class="p-1 text-gray-400 hover:text-red-600">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </div>

              <!-- Field Configuration -->
              <div class="space-y-4">
                <!-- Field Label -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                  <input
                    v-model="field.label"
                    type="text"
                    placeholder="Field label"
                    class="form-input"
                  />
                </div>

                <!-- Field Options (for checkbox/radio) -->
                <div v-if="field.type === 'checkbox' || field.type === 'radio'">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                  <div class="space-y-2">
                    <div
                      v-for="(option, optionIndex) in field.options"
                      :key="optionIndex"
                      class="flex items-center space-x-2"
                    >
                      <input
                        v-model="field.options[optionIndex]"
                        type="text"
                        placeholder="Option text"
                        class="form-input flex-1"
                      />
                      <button
                        @click="removeOption(field, optionIndex)"
                        class="p-2 text-gray-400 hover:text-red-600"
                      >
                        <XIcon class="w-4 h-4" />
                      </button>
                    </div>

                    <button
                      @click="addOption(field)"
                      class="text-blue-600 hover:text-blue-700 text-sm font-medium"
                    >
                      + Add Option
                    </button>
                  </div>
                </div>

                <!-- Required Field Checkbox -->
                <div class="flex items-center">
                  <input
                    v-model="field.required"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  />
                  <label class="ml-2 text-sm text-gray-700">Required field</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Preview Panel -->
      <div class="lg:col-span-1">
        <div class="card sticky top-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview</h3>

          <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 min-h-96">
            <div v-if="!formData.name" class="text-center text-gray-500 py-8">
              <FileTextIcon class="w-12 h-12 mx-auto mb-4 opacity-50" />
              <p>Form preview will appear here</p>
            </div>

            <div v-else class="bg-white rounded-md p-6">
              <h4 class="text-xl font-semibold text-gray-900 mb-2">{{ formData.name }}</h4>
              <p v-if="formData.description" class="text-gray-600 mb-6">
                {{ formData.description }}
              </p>

              <div class="space-y-4">
                <div v-for="field in formData.fields" :key="field.tempId || field.id">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ field.label }}
                    <span v-if="field.required" class="text-red-500">*</span>
                  </label>

                  <!-- Text Input Preview -->
                  <input
                    v-if="field.type === 'text'"
                    type="text"
                    disabled
                    placeholder="Text input"
                    class="form-input bg-gray-50"
                  />

                  <!-- Textarea Preview -->
                  <textarea
                    v-else-if="field.type === 'textarea'"
                    disabled
                    placeholder="Text area"
                    rows="3"
                    class="form-input bg-gray-50"
                  ></textarea>

                  <!-- Radio Preview -->
                  <div v-else-if="field.type === 'radio'" class="space-y-2">
                    <div v-for="option in field.options" :key="option" class="flex items-center">
                      <input type="radio" disabled class="mr-2" />
                      <span class="text-sm">{{ option }}</span>
                    </div>
                  </div>

                  <!-- Checkbox Preview -->
                  <div v-else-if="field.type === 'checkbox'" class="space-y-2">
                    <div v-for="option in field.options" :key="option" class="flex items-center">
                      <input type="checkbox" disabled class="mr-2" />
                      <span class="text-sm">{{ option }}</span>
                    </div>
                  </div>
                </div>

                <button disabled class="btn-primary w-full opacity-50">Submit Form</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormBuilderView'
}
</script>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { formService } from '../services/formService'
import {
  SaveIcon,
  TypeIcon,
  AlignLeftIcon,
  CircleDotIcon,
  CheckSquareIcon,
  LayersIcon,
  ChevronUpIcon,
  ChevronDownIcon,
  TrashIcon,
  XIcon,
  FileTextIcon,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const saving = ref(false)
const isEditing = ref(false)
const loading = ref(false)
const error = ref(null)

const formData = ref({
  name: '',
  description: '',
  fields: [],
})

let fieldIdCounter = 0

const addField = (type) => {
  const newField = {
    tempId: ++fieldIdCounter,
    label: `${type.charAt(0).toUpperCase() + type.slice(1)} Field`,
    type: type,
    required: false,
    options: type === 'checkbox' || type === 'radio' ? ['Option 1', 'Option 2'] : null,
  }

  formData.value.fields.push(newField)
}

const removeField = (index) => {
  formData.value.fields.splice(index, 1)
}

const moveField = (index, direction) => {
  const newIndex = direction === 'up' ? index - 1 : index + 1

  if (newIndex >= 0 && newIndex < formData.value.fields.length) {
    const field = formData.value.fields.splice(index, 1)[0]
    formData.value.fields.splice(newIndex, 0, field)
  }
}

const addOption = (field) => {
  if (!field.options) {
    field.options = []
  }
  field.options.push(`Option ${field.options.length + 1}`)
}

const removeOption = (field, optionIndex) => {
  field.options.splice(optionIndex, 1)
}

const saveForm = async () => {
  try {
    saving.value = true
    error.value = null

    // Validate form data
    if (!formData.value.name) {
      error.value = 'Please enter a form title'
      return
    }

    if (formData.value.fields.length === 0) {
      error.value = 'Please add at least one field'
      return
    }

    // Clean up field data for API
    const fieldsData = formData.value.fields.map((field) => ({
      label: field.label,
      type: field.type,
      required: field.required,
      options: field.options,
    }))

    const payload = {
      name: formData.value.name,
      description: formData.value.description,
      fields: fieldsData,
    }

    if (isEditing.value) {
      await formService.updateForm(route.params.id, payload)
    } else {
      await formService.createForm(payload)
    }

    router.push('/forms')
  } catch (err) {
    console.error('Failed to save form:', err)
    error.value = 'Failed to save form. Please try again.'
  } finally {
    saving.value = false
  }
}

const loadForm = async () => {
  if (route.params.id) {
    try {
      loading.value = true
      isEditing.value = true
      const form = await formService.getForm(route.params.id)

      formData.value = {
        name: form.name,
        description: form.description || '',
        fields: form.fields.map((field) => ({
          ...field,
          tempId: ++fieldIdCounter,
        })),
      }
    } catch (err) {
      console.error('Failed to load form:', err)
      error.value = 'Failed to load form'
      // router.push('/forms')
    } finally {
      loading.value = false
    }
  }
}

onMounted(() => {
  loadForm()
})
</script>
