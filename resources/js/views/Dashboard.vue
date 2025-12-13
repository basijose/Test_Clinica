<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="item in menuItems" :key="item.id" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">{{ item.descripcion }}</h3>
                <span class="text-blue-500 bg-blue-100 p-2 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </span>
            </div>
            <p class="text-gray-600 mb-4">Acceder al módulo de {{ item.descripcion }}</p>
            <router-link :to="item.destino" class="text-blue-500 hover:text-blue-700 font-semibold flex items-center">
                Ir a {{ item.descripcion }}
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const menuItems = ref([]);

onMounted(async () => {
    try {
        const response = await axios.get('/api/dashboard');
        menuItems.value = response.data;
    } catch (e) {
        console.error('Error fetching menu', e);
    }
});
</script>
