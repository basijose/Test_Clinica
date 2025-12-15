<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="bg-white shadow-md hidden md:flex flex-col transition-all duration-300" :class="isSidebarCollapsed ? 'w-20' : 'w-64'">
            <div class="p-6 flex items-center justify-between">
                <router-link to="/dashboard" class="text-xl font-bold text-gray-800 hover:text-blue-600 overflow-hidden whitespace-nowrap" v-if="!isSidebarCollapsed">
                    Panel CNF
                </router-link>
                <span v-else class="text-xl font-bold text-gray-800 mx-auto">CNF</span>
                
                <button @click="toggleSidebar" class="text-gray-500 hover:text-gray-700 focus:outline-none" :class="{ 'mx-auto': isSidebarCollapsed }">
                    <svg v-if="isSidebarCollapsed" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                </button>
            </div>
            <nav class="mt-6 flex-1 overflow-y-auto">
                <template v-for="item in menuItems" :key="item.id">
                    <a v-if="isExternal(item.destino)" :href="item.destino" target="_blank"
                        class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-800"
                        :title="isSidebarCollapsed ? item.descripcion : ''">
                        <i v-if="item.icono" :class="[item.icono, 'text-lg w-6 text-center']" :title="isSidebarCollapsed ? item.descripcion : ''"></i>
                        <svg v-else class="w-5 h-5" :class="{ 'mx-auto': isSidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        
                        <span v-if="!isSidebarCollapsed" class="mx-3">{{ item.descripcion }}</span>
                        <svg v-if="!isSidebarCollapsed" class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                    <router-link v-else :to="item.destino"
                        class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-800"
                        :class="{ 'bg-gray-100 text-gray-800 border-r-4 border-blue-500': $route.path.startsWith(item.destino) }"
                        :title="isSidebarCollapsed ? item.descripcion : ''">
                        
                        <!-- Icon Logic: Use item.icono if available, else generic icon -->
                        <i v-if="item.icono" :class="[item.icono, 'text-lg w-6 text-center']" :title="isSidebarCollapsed ? item.descripcion : ''"></i>
                        <svg v-else class="w-5 h-5" :class="{ 'mx-auto': isSidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>

                        <span v-if="!isSidebarCollapsed" class="mx-3">{{ item.descripcion }}</span>
                    </router-link>
                </template>
                
                <div v-if="auth.user" class="px-6 py-3 border-t border-gray-200 mt-auto">
                    <router-link to="/profile" class="flex items-center text-sm font-medium text-gray-700 hover:text-blue-600" :class="{ 'justify-center': isSidebarCollapsed }">
                        <div class="flex-shrink-0">
                            <span class="inline-block h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-white font-bold">
                                {{ auth.user.nombre.charAt(0) }}
                            </span>
                        </div>
                        <div v-if="!isSidebarCollapsed" class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ auth.user.nombre }}</p>
                        </div>
                    </router-link>
                </div>

                <button v-if="auth.user" @click="handleLogout" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-800 w-full text-left mt-2" :title="isSidebarCollapsed ? 'Cerrar Sesión' : ''">
                    <svg class="w-5 h-5" :class="{ 'mx-auto': isSidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span v-if="!isSidebarCollapsed" class="mx-3">Cerrar Sesión</span>
                </button>
                <router-link v-else to="/login" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-800" :title="isSidebarCollapsed ? 'Iniciar Sesión' : ''">
                    <svg class="w-5 h-5" :class="{ 'mx-auto': isSidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    <span v-if="!isSidebarCollapsed" class="mx-3">Iniciar Sesión</span>
                </router-link>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center p-6 bg-white shadow-sm md:hidden">
                <router-link to="/dashboard" class="text-xl font-bold text-gray-800">Panel CNF</router-link>
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </header>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden bg-white shadow-md">
                <nav class="px-2 pt-2 pb-4 space-y-1">
                    <template v-for="item in menuItems" :key="item.id">
                        <a v-if="isExternal(item.destino)" :href="item.destino" target="_blank"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 flex justify-between items-center">
                            {{ item.descripcion }}
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                        <router-link v-else :to="item.destino"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ item.descripcion }}
                        </router-link>
                    </template>
                    <button v-if="auth.user" @click="handleLogout" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                        Cerrar Sesión
                    </button>
                    <router-link v-else to="/login" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                        Iniciar Sesión
                    </router-link>
                </nav>
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-1 md:p-6">
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
const isSidebarCollapsed = ref(true);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

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

const isExternal = (url) => {
    return url.startsWith('http://') || url.startsWith('https://');
};
</script>
