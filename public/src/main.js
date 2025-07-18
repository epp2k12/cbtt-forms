import './main.css'
import { createApp } from 'vue'
import App from '@/PublicApp2.vue'
import router from '@/router'

const app = createApp(App)

app.use(router)
app.mount('#vue-contact-form')
// console.log('Mounting Vue app to:', document.getElementById('vue-contact-form'));