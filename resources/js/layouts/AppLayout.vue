<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md hidden md:block">
            <div class="p-6">
                <router-link to="/dashboard" class="text-xl font-bold text-gray-800 hover:text-blue-600">Test Clinica</router-link>
            </div>
            <nav class="mt-6">
                <router-link v-for="item in menuItems" :key="item.id" :to="item.destino"
                    class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-800"
                    :class="{ 'bg-gray-100 text-gray-800 border-r-4 border-blue-500': $route.path.startsWith(item.destino) }">
                    <span class="mx-3">{{ item.descripcion }}</span>
                </router-link>
                <div v-if="auth.user" class="px-6 py-3 border-t border-gray-200 mt-auto">
                    <router-link to="/profile" class="flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <div class="flex-shrink-0">
                            <span class="inline-block h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-white font-bold">
                                {{ auth.user.nombre.charAt(0) }}
                            </span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ auth.user.nombre }} {{ auth.user.apellido }}</p>
                            <p class="text-xs text-gray-500">{{ auth.user.role?.nombre_corto }}</p>
                        </div>
                    </router-link>
                </div>

                <button v-if="auth.user" @click="handleLogout" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-800 w-full text-left mt-2">
                    <span class="mx-3">Cerrar Sesión</span>
                </button>
                <router-link v-else to="/login" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-800">
                    <span class="mx-3">Iniciar Sesión</span>
                </router-link>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center p-6 bg-white shadow-sm md:hidden">
                <router-link to="/dashboard" class="text-xl font-bold text-gray-800">Test Clinica</router-link>
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </header>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden bg-white shadow-md">
                <nav class="px-2 pt-2 pb-4 space-y-1">
                    <router-link v-for="item in menuItems" :key="item.id" :to="item.destino"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                        {{ item.descripcion }}
                    </router-link>
                    <button v-if="auth.user" @click="handleLogout" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                        Cerrar Sesión
                    </button>
                    <router-link v-else to="/login" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                        Iniciar Sesión
                    </router-link>
                </nav>
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">{{ currentTitle }}</h2>
                    <router-link v-if="$route.path !== '/dashboard'" to="/dashboard" class="flex items-center text-gray-600 hover:text-blue-600">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Volver al Inicio
                    </router-link>
                </div>
                
                <router-view></router-view>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const menuItems = ref([]);
const mobileMenuOpen = ref(false);

const currentTitle = computed(() => {
    const item = menuItems.value.find(i => route.path.startsWith(i.destino));
    return item ? item.descripcion : 'Dashboard';
});

onMounted(async () => {
    try {
        const response = await axios.get('/api/dashboard');
        menuItems.value = response.data;
    } catch (e) {
        console.error('Error fetching menu', e);
    }
});

const handleLogout = async () => {
    await auth.logout();
};
</script>
