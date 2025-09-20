import api from './api'

export const formService = {
  // Get all forms
  getForms() {
    return api.get('/forms')
  },

  // Get single form with fields
  getForm(id) {
    return api.get(`/forms/${id}`)
  },

  // Create new form
  createForm(formData) {
    return api.post('/forms', formData)
  },

  // Update existing form
  updateForm(id, formData) {
    return api.put(`/forms/${id}`, formData)
  },

  // Delete form
  deleteForm(id) {
    return api.delete(`/forms/${id}`)
  },

  // Submit form response
  submitForm(formId, formData) {
    return api.post(`/forms/${formId}/submit`, formData)
  },

  // Get form submissions
  getSubmissions(formId) {
    return api.get(`/forms/${formId}/submissions`)
  },

  // Get single submission
  getSubmission(formId, submissionId) {
    return api.get(`/forms/${formId}/submissions/${submissionId}`)
  },
}
