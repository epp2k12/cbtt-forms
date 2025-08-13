import { createRouter, createMemoryHistory } from 'vue-router'
import MainAdminView from '@/components/MainAdminView.vue'

// Export a function that creates a router with initial route
export const createAppRouter = (initialRoute = '/') => {
  return createRouter({
    history: createMemoryHistory(initialRoute),
    routes: [
      {
        path: '/',
        name: 'main-admin-view',
        component: MainAdminView,
      }
    ],
  });
};

// Default export for backward compatibility
const router = createAppRouter();
export default router;
