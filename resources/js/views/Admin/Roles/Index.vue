<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Roles</h1>
            <router-link to="/roles/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Crear Rol
            </router-link>
        </div>
        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Nombre Corto</th>
                        <th class="py-3 px-6 text-left">Descripción</th>
                        <th class="py-3 px-6 text-left">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="role in roles" :key="role.id" class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">{{ role.id }}</td>
                        <td class="py-3 px-6 text-left">{{ role.nombre_corto }}</td>
                        <td class="py-3 px-6 text-left">{{ role.descripcion }}</td>
                        <td class="py-3 px-6 text-left">
                            <span :class="role.estado === 'activo' ? 'bg-green-200 text-green-600' : 'bg-red-200 text-red-600'" class="py-1 px-3 rounded-full text-xs">
                                {{ role.estado }}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center">
                                <router-link :to="`/roles/${role.id}/edit`" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </router-link>
                                <button @click="deleteRole(role.id)" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const roles = ref([]);

const fetchRoles = async () => {
    try {
        const response = await axios.get('/api/admin/roles');
        roles.value = response.data;
    } catch (e) {
        console.error(e);
    }
};

const deleteRole = async (id) => {
    if (!confirm('¿Estás seguro de eliminar este rol?')) return;
    try {
        await axios.delete(`/api/admin/roles/${id}`);
        fetchRoles();
    } catch (e) {
        console.error(e);
        alert('Error al eliminar el rol');
    }
};

onMounted(fetchRoles);
</script>
