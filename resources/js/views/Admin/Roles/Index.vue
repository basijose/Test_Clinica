<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Roles</h1>
            <router-link to="/roles/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Crear Rol
            </router-link>
        </div>

        <DataTable :headers="headers" :items="roles" :pagination="pagination" @update:options="fetchRoles">
            <template #row="{ item }">
                <td class="py-3 px-6 text-left whitespace-nowrap">{{ item.id }}</td>
                <td class="py-3 px-6 text-left">{{ item.nombre_corto }}</td>
                <td class="py-3 px-6 text-left">{{ item.descripcion }}</td>
                <td class="py-3 px-6 text-left">
                    <span :class="item.estado === 'activo' ? 'bg-green-200 text-green-600' : 'bg-red-200 text-red-600'"
                        class="py-1 px-3 rounded-full text-xs">
                        {{ item.estado }}
                    </span>
                </td>
                <td class="py-3 px-6 text-center">
                    <div class="flex item-center justify-center">
                        <router-link :to="`/roles/${item.id}/edit`"
                            class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </router-link>
                        <button @click="deleteRole(item.id)"
                            class="w-4 mr-2 transform hover:text-red-500 hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </td>
            </template>
        </DataTable>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from '../../../components/DataTable.vue';

const roles = ref([]);
const pagination = ref({});
const headers = [
    { key: 'id', label: 'ID' },
    { key: 'nombre_corto', label: 'Nombre Corto' },
    { key: 'descripcion', label: 'Descripción' },
    { key: 'estado', label: 'Estado' },
];

const currentOptions = ref({
    page: 1,
    per_page: 25,
    sort_by: 'id',
    sort_order: 'desc'
});

onMounted(async () => {
    await fetchRoles(currentOptions.value);
});

const fetchRoles = async (options) => {
    currentOptions.value = { ...currentOptions.value, ...options };
    try {
        const response = await axios.get('/api/admin/roles', { params: currentOptions.value });
        roles.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            from: response.data.from,
            to: response.data.to,
            total: response.data.total
        };
    } catch (e) {
        console.error(e);
    }
};

const deleteRole = async (id) => {
    if (!confirm('¿Estás seguro de eliminar este rol?')) return;
    try {
        await axios.delete(`/api/admin/roles/${id}`);
        fetchRoles(currentOptions.value);
    } catch (e) {
        console.error(e);
        alert('Error al eliminar el rol');
    }
};
</script>
