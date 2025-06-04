import { createApp } from 'vue'
import App from './App.vue'
import './styles.css'
import router from './router/route'

createApp(App).use(router).mount('#app')
