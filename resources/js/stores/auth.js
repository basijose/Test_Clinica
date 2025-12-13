import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
    }),
    actions: {
        async login(credentials) {
            await axios.get('/sanctum/csrf-cookie');
            const response = await axios.post('/api/login', credentials);
            this.user = response.data.user;
        },
        async logout() {
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error('Logout error', error);
            } finally {
                this.user = null;
                // Force a hard reload to clear any application state
                window.location.href = '/';
            }
        },
        async fetchUser() {
            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
            } catch (error) {
                this.user = null;
                throw error;
            }
        },
    },
});
