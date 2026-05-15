import { createApp } from 'vue'
import App from './App.vue'
import './styles.css'
import Alpine from 'alpinejs'

window.Alpine = Alpine
Alpine.start()

createApp(App).mount('#app')
