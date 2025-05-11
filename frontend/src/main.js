import { createApp } from 'vue'
import './assets/main.css'
import App from './App.vue'
import router from "./router.js";
import store from './storage.js';
// import Datepicker from "vue3-datepicker"
// import 'vue3-datepicker/dist/vue3-datepicker.css'

createApp(App).use(store).use(router).mount('#app');