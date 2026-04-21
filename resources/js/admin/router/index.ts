import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/admin/login',
        name: 'login',
        component: () => import('../views/Login.vue'),
    },
    {
        path: '/admin',
        component: () => import('../layouts/MainLayout.vue'),
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('../views/Dashboard.vue'),
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;