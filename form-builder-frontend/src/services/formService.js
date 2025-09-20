import api from './api'

export const formService = {
  // Get all forms
  async getForms() {
    try {
      const data = await api.get('/forms')
      return data || []
    } catch (error) {
      console.error('Error fetching forms:', error)
      throw error
    }
  },

  // Get single form with fields
  async getForm(id) {
    try {
      const data = await api.get(`/forms/${id}`)
      return data
    } catch (error) {
      console.error('Error fetching form:', error)
      throw error
    }
  },

  // Create new form
  async createForm(formData) {
    try {
      const data = await api.post('/forms', formData)
      return data
    } catch (error) {
      console.error('Error creating form:', error)
      throw error
    }
  },

  // Update existing form
  async updateForm(id, formData) {
    try {
      const data = await api.put(`/forms/${id}`, formData)
      return data
    } catch (error) {
      console.error('Error updating form:', error)
      throw error
    }
  },

  // Delete form
  async deleteForm(id) {
    try {
      const data = await api.delete(`/forms/${id}`)
      return data
    } catch (error) {
      console.error('Error deleting form:', error)
      throw error
    }
  },

  // Submit form response
  async submitForm(formId, formData) {
    try {
      const data = await api.post(`/forms/${formId}/submit`, formData)
      return data
    } catch (error) {
      console.error('Error submitting form:', error)
      throw error
    }
  },

  // Get form submissions
  async getSubmissions(formId) {
    try {
      const data = await api.get(`/forms/${formId}/submissions`)
      return data || []
    } catch (error) {
      console.error('Error fetching submissions:', error)
      throw error
    }
  },

  // Get single submission
  async getSubmission(formId, submissionId) {
    try {
      const data = await api.get(`/forms/${formId}/submissions/${submissionId}`)
      return data
    } catch (error) {
      console.error('Error fetching submission:', error)
      throw error
    }
  },
}
