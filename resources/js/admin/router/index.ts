import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/admin/login',
        name: 'login',
        component: () => import('../views/Login.vue'),
        meta: { guest: true },
    },
    {
        path: '/admin',
        component: () => import('../layouts/MainLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('../views/Dashboard.vue'),
            },
            {
                path: 'pages',
                name: 'pages',
                component: () => import('../views/Pages.vue'),
            },
            {
                path: 'pages/:id/edit',
                name: 'page-editor',
                component: () => import('../views/PageEditor.vue'),
            },
            {
                path: 'site',
                name: 'site-settings',
                component: () => import('../views/SiteSettings.vue'),
            },
            
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;

    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/admin/login');
    } else if (to.meta.guest && isAuthenticated) {
        next('/admin');
    } else {
        next();
    }
});

export default router;