import { defineStore } from 'pinia';
import axios from 'axios';

// 设置 axios 响应拦截器，全局处理 401
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // Token 过期或无效，清除本地存储并跳转到登录页
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
            // 如果当前不在登录页，则跳转
            if (window.location.pathname !== '/admin/login') {
                window.location.href = '/admin/login';
            }
        }
        return Promise.reject(error);
    }
);

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token'),
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        async login(email, password) {
            try {
                const response = await axios.post('/api/admin/login', {
                    email,
                    password,
                });
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('token', this.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                return true;
            } catch (error) {
                return false;
            }
        },
        async logout() {
            try {
                await axios.post('/api/admin/logout');
            } catch (error) {
                // 忽略退出请求失败，仍然清除本地状态
            }
            this.token = null;
            this.user = null;
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
        },
        async checkAuth() {
            if (!this.token) return;
            axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
            try {
                const response = await axios.get('/api/admin/user');
                this.user = response.data;
            } catch (error) {
                // 401 会被拦截器处理，这里不需要额外操作
                this.logout();
            }
        },
    },
});