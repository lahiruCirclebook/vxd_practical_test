import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import FormsList from '../views/FormsList.vue'
import FormBuilder from '../views/FormBuilder.vue'
import FormPreview from '../views/FormPreview.vue'
import FormSubmissions from '../views/FormSubmissions.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/dashboard'
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: Dashboard
    },
    {
      path: '/forms',
      name: 'forms',
      component: FormsList
    },
    {
      path: '/forms/create',
      name: 'form-create',
      component: FormBuilder
    },
    {
      path: '/forms/:id/edit',
      name: 'form-edit',
      component: FormBuilder,
      props: true
    },
    {
      path: '/forms/:id/preview',
      name: 'form-preview',
      component: FormPreview,
      props: true
    },
    {
      path: '/forms/:id/submissions',
      name: 'form-submissions',
      component: FormSubmissions,
      props: true
    }
  ]
})

export default router
