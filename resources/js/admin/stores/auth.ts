import { defineStore } from 'pinia';
import axios from 'axios';

interface User {
    id: number;
    name: string;
    email: string;
}

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null as User | null,
        token: localStorage.getItem('token'),
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        async login(email: string, password: string) {
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
            } catch {
                this.logout();
            }
        },
    },
});