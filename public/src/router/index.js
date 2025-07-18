import { createRouter, createMemoryHistory } from 'vue-router'
import ContactForm from '@/components/ContactForm.vue'
import ValidateForm from '@/components/ValidateForm.vue'
import FastContactForm from '@/components/FastContactForm.vue'

// Export a function that creates a router with initial route
export const createAppRouter = (initialRoute = '/') => {
  return createRouter({
    history: createMemoryHistory(initialRoute),
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
      {
        path: '/fast-contact-form',
        name: 'fast-contact-form',
        component: FastContactForm,
      },
    ],
  });
};

// Default export for backward compatibility
const router = createAppRouter();
export default router;
