import {createRouter, createWebHistory} from 'vue-router';
import HomeView from "@/views/HomeView.vue";
import MainView from "@/views/MainView.vue";
import AdminView from "@/views/admin/adminView.vue";
import AdminLoginView from "@/views/admin/adminLoginView.vue";

const routes = [
    {
        path: "/",
        component: MainView,
    },
    {
        path: "/admin/login",
        component: AdminLoginView,
        meta: { title: 'Верное чутьё | Admin\'s Authorization' },
        name: 'adminlogin'
    },
    {
        path: "/admin",
        component: AdminView,
        meta: { title: 'Верное чутьё | Admin', h: 'Dashboard' },
        name: 'admin'
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router;