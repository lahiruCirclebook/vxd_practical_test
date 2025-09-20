<template>
  <div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
      <div class="flex items-center space-x-4">
        <button
          @click="$router.go(-1)"
          class="p-2 text-gray-400 hover:text-gray-600 rounded-md transition-colors"
        >
          <ArrowLeftIcon class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Form Submissions</h1>
          <p class="text-gray-600 mt-1">
            {{ form.name || 'Loading...' }} • {{ submissions.length }} submissions
          </p>
        </div>
      </div>

      <div class="flex space-x-3">
        <button class="btn-secondary inline-flex items-center">
          <DownloadIcon class="w-4 h-4 mr-2" />
          Export CSV
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="card text-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p class="text-gray-600">Loading submissions...</p>
    </div>

    <div v-else>
      <!-- Submissions Overview Card -->
      <div class="card mb-6">
        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">Submissions Overview</h2>
            <p class="text-sm text-gray-600 mt-1">Total submissions received for this form</p>
          </div>
          <div class="text-right">
            <div class="text-3xl font-bold text-blue-600">{{ submissions.length }}</div>
            <div class="text-sm text-gray-500">Total Submissions</div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Submissions List -->
        <div class="lg:col-span-1">
          <div class="card h-fit">
            <div
              class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 space-y-2 sm:space-y-0"
            >
              <!-- <h2 class="text-xl font-semibold text-gray-900">Submissions</h2> -->
              <div
                class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-2"
              >
                <!-- Search -->
                <div class="relative">
                  <SearchIcon
                    class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
                  />
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search submissions..."
                    class="pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 w-full sm:w-auto"
                  />
                </div>

                <!-- Filter -->
                <select
                  v-model="timeFilter"
                  class="text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 py-2 px-3 w-full sm:w-auto min-w-0 sm:min-w-[140px]"
                >
                  <option value="all">All Time</option>
                  <option value="today">Today</option>
                  <option value="week">This Week</option>
                  <option value="month">This Month</option>
                </select>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredSubmissions.length === 0" class="text-center py-8 text-gray-500">
              <InboxIcon class="w-12 h-12 mx-auto mb-4 opacity-50" />
              <p v-if="submissions.length === 0">No submissions yet</p>
              <p v-else>No submissions match your search</p>
            </div>

            <!-- Submissions List -->
            <div v-else class="space-y-3 max-h-96 overflow-y-auto">
              <div
                v-for="submission in filteredSubmissions"
                :key="submission.id"
                @click="selectedSubmission = submission"
                :class="[
                  'p-4 rounded-lg border-2 cursor-pointer transition-all duration-200',
                  selectedSubmission?.id === submission.id
                    ? 'border-blue-500 bg-blue-50 shadow-sm'
                    : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50',
                ]"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      Submission #{{ submission.id }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                      {{ formatDate(submission.submitted_at) }}
                    </p>
                  </div>
                  <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 rounded-full bg-green-500" title="Completed"></div>
                    <ChevronRightIcon class="w-4 h-4 text-gray-400" />
                  </div>
                </div>

                <!-- Quick preview of answers -->
                <div class="mt-3 space-y-1">
                  <div
                    v-for="answer in submission.answers.slice(0, 2)"
                    :key="answer.field_label"
                    class="text-sm"
                  >
                    <span class="text-gray-600">{{ answer.field_label }}:</span>
                    <span class="ml-1 text-gray-900">{{ formatAnswerValue(answer.value) }}</span>
                  </div>

                  <p v-if="submission.answers.length > 2" class="text-xs text-gray-500 mt-1">
                    {{ submission.answers.length - 2 }} more field{{
                      submission.answers.length - 2 === 1 ? '' : 's'
                    }}...
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submission Details -->
        <div class="lg:col-span-2">
          <div class="card h-fit">
            <div v-if="!selectedSubmission" class="text-center py-16 text-gray-500">
              <MousePointerClickIcon class="w-16 h-16 mx-auto mb-4 text-gray-300" />
              <h3 class="text-lg font-medium text-gray-900 mb-2">No submission selected</h3>
              <p class="text-sm">Select a submission from the list to view its details</p>
            </div>

            <div v-else class="space-y-6">
              <!-- Submission Header -->
              <div class="border-b border-gray-200 pb-6">
                <div
                  class="flex flex-col sm:flex-row sm:items-start sm:justify-between space-y-3 sm:space-y-0"
                >
                  <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">
                      Submission #{{ selectedSubmission.id }}
                    </h3>
                    <div
                      class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-6 text-sm text-gray-600"
                    >
                      <div class="flex items-center">
                        <CalendarIcon class="w-4 h-4 mr-2 text-gray-400" />
                        {{ formatDateTime(selectedSubmission.submitted_at) }}
                      </div>
                      <div class="flex items-center">
                        <GlobeIcon class="w-4 h-4 mr-2 text-gray-400" />
                        IP: {{ selectedSubmission.ip_address || 'Unknown' }}
                      </div>
                    </div>
                  </div>
                  <div class="flex flex-col sm:items-end">
                    <span
                      class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium"
                    >
                      <CheckCircleIcon class="w-4 h-4 mr-1" />
                      Completed
                    </span>
                  </div>
                </div>
              </div>

              <!-- Form Data -->
              <div>
                <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                  <FileTextIcon class="w-5 h-5 mr-2 text-gray-500" />
                  Form Responses
                </h4>
                <div class="space-y-8">
                  <div
                    v-for="answer in selectedSubmission.answers"
                    :key="answer.field_label"
                    class="bg-gray-50 rounded-lg p-6 border-l-4 border-blue-500"
                  >
                    <div class="flex items-center justify-between mb-3">
                      <span class="text-lg font-medium text-gray-800">{{
                        answer.field_label
                      }}</span>
                      <span
                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-200 text-gray-700 rounded-full"
                      >
                        {{ answer.field_type }}
                      </span>
                    </div>

                    <div class="text-gray-900">
                      <!-- Text/Textarea Response -->
                      <div v-if="answer.field_type === 'text' || answer.field_type === 'textarea'">
                        <div class="bg-white rounded-md p-4 border">
                          <p class="whitespace-pre-wrap text-gray-800 leading-relaxed">
                            {{ answer.value[0] }}
                          </p>
                        </div>
                      </div>

                      <!-- Radio Response -->
                      <div v-else-if="answer.field_type === 'radio'">
                        <div class="bg-white rounded-md p-4 border">
                          <span
                            class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium"
                          >
                            <CircleDotIcon class="w-4 h-4 mr-2" />
                            {{ answer.value[0] }}
                          </span>
                        </div>
                      </div>

                      <!-- Checkbox Response -->
                      <div v-else-if="answer.field_type === 'checkbox'">
                        <div class="bg-white rounded-md p-4 border">
                          <div v-if="answer.value.length > 0" class="flex flex-wrap gap-2">
                            <span
                              v-for="option in answer.value"
                              :key="option"
                              class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium"
                            >
                              <CheckSquareIcon class="w-4 h-4 mr-2" />
                              {{ option }}
                            </span>
                          </div>
                          <p v-else class="text-gray-500 italic text-center py-2">
                            No options selected
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
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
  name: 'FormSubmissionsView',
}
</script>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { formService } from '../services/formService'
import {
  ArrowLeftIcon,
  DownloadIcon,
  SearchIcon,
  InboxIcon,
  MousePointerClickIcon,
  CalendarIcon,
  GlobeIcon,
  CircleDotIcon,
  CheckSquareIcon,
  CheckCircleIcon,
  FileTextIcon,
  ChevronRightIcon,
} from 'lucide-vue-next'

const props = defineProps(['id'])
const route = useRoute()

const loading = ref(true)
const form = ref({})
const submissions = ref([])
const selectedSubmission = ref(null)
const searchQuery = ref('')
const timeFilter = ref('all')

const filteredSubmissions = computed(() => {
  let filtered = submissions.value

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter((submission) =>
      submission.answers.some(
        (answer) =>
          answer.field_label.toLowerCase().includes(query) ||
          answer.value.some((val) => val.toLowerCase().includes(query)),
      ),
    )
  }

  // Apply time filter
  if (timeFilter.value !== 'all') {
    const now = new Date()
    const filterDate = new Date()

    switch (timeFilter.value) {
      case 'today':
        filterDate.setHours(0, 0, 0, 0)
        break
      case 'week':
        filterDate.setDate(now.getDate() - 7)
        break
      case 'month':
        filterDate.setMonth(now.getMonth() - 1)
        break
    }

    filtered = filtered.filter((submission) => new Date(submission.submitted_at) >= filterDate)
  }

  return filtered
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatAnswerValue = (value) => {
  if (Array.isArray(value)) {
    if (value.length === 0) return 'No answer'
    if (value.length === 1) return value[0]
    return `${value[0]} (+${value.length - 1} more)`
  }
  return value || 'No answer'
}

const loadSubmissions = async () => {
  try {
    loading.value = true
    const formId = props.id || route.params.id
    const data = await formService.getSubmissions(formId)

    form.value = data.form
    submissions.value = data.submissions

    // Auto-select first submission if available
    if (submissions.value.length > 0) {
      selectedSubmission.value = submissions.value[0]
    }
  } catch (error) {
    console.error('Failed to load submissions:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadSubmissions()
})
</script>
