import {createRouter, createWebHistory} from 'vue-router';
import HomeView from "@/views/HomeView.vue";
import MainView from "@/views/MainView.vue";
import AdminView from "@/views/admin/adminView.vue";
import AdminLoginView from "@/views/admin/adminLoginView.vue";
import adminUsersView from "@/views/admin/adminUsersView.vue";
import adminPostsView from "@/views/admin/adminPostsView.vue";
import adminServicesView from "@/views/admin/adminServicesView.vue";
import adminEventsView from "@/views/admin/adminEventsView.vue";
import adminEventsModerateView from "@/views/admin/adminEventsModerateView.vue";

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
    {
        path: "/admin/users",
        component: adminUsersView,
        meta: { title: 'Верное чутьё | Пользователи', h: 'Пользователи' },
        name: 'adminUsers'
    },
    {
        path: "/admin/posts",
        component: adminPostsView,
        meta: { title: 'Верное чутьё | Объявления', h: 'Объявления' },
        name: 'adminPosts'
    },
    {
        path: "/admin/services",
        component: adminServicesView,
        meta: { title: 'Верное чутьё | Услуги', h: 'Услуги' },
        name: 'adminServices'
    },
    {
        path: "/admin/events",
        component: adminEventsView,
        meta: { title: 'Верное чутьё | Мероприятия', h: 'Мероприятия' },
        name: 'adminEvents'
    },
    {
        path: "/admin/moderate",
        component: adminEventsModerateView,
        meta: { title: 'Верное чутьё | Модерация', h: 'Модерация' },
        name: 'adminModerate'
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router;