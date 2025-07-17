import { createRouter, createWebHistory } from 'vue-router'
import ContactForm from '@/components/ContactForm.vue'
import ValidateForm from '@/components/ValidateForm.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'contact',
      component: ContactForm,
    },
    {
      path: '/validate-form',
      name: 'validate-form',
      component: ValidateForm,
    },
  ],
})

export default router
