import './main.css'
import { createApp } from 'vue'
import App from '@/PublicApp2.vue'
import router from '@/router'
import { createAppRouter } from '@/router'

// Function to create and mount Vue app
const createVueApp = (elementId) => {
  const element = document.getElementById(elementId);
  if (!element) return;

  const initialRoute = element.dataset.initialRoute || '/';
  const router = createAppRouter(initialRoute);
  console.log(`Creating Vue app with initial route: ${initialRoute}`);

  const app = createApp(App);
  app.use(router);

//   app.mount(`#${elementId}`);
//   console.log(`Vue app mounted to: #${elementId} with initial route: ${initialRoute}`);

  router.replace(initialRoute).then(() => {
    app.mount(`#${elementId}`);
    console.log(`Vue app mounted to: #${elementId} with initial route: ${initialRoute}`);
  });

};

// Mount to contact form
createVueApp('vue-contact-form');

// Mount to validate form
createVueApp('vue-fast-contact-form');