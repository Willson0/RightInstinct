import {createRouter, createWebHistory} from 'vue-router';
import HomeView from "@/views/HomeView.vue";
import MainView from "@/views/MainView.vue";

const routes = [
    {
        path: "/",
        component: MainView,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router;