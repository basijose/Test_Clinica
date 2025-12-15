<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Incidencias</h1>
            <router-link to="/incidents/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nueva Incidencia
            </router-link>
        </div>

        <DataTable :headers="headers" :items="incidents" :pagination="pagination" @update:options="fetchIncidents">
            <template #row="{ item }">
                <td class="py-3 px-6 text-left whitespace-nowrap">{{ item.id }}</td>
                <td class="py-3 px-6 text-left">{{ item.titulo }}</td>
                <td class="py-3 px-6 text-left">
                    <span :class="getStatusClass(item.estado)" class="py-1 px-3 rounded-full text-xs">
                        {{ item.estado }}
                    </span>
                </td>
                <td class="py-3 px-6 text-left">
                    <span :class="getPriorityClass(item.prioridad)" class="py-1 px-3 rounded-full text-xs">
                        {{ item.prioridad }}
                    </span>
                </td>
                <td class="py-3 px-6 text-left">{{ item.creator?.nombre }} {{ item.creator?.apellido }}</td>
                <td class="py-3 px-6 text-left">{{ item.assignee ? item.assignee.nombre + ' ' + item.assignee.apellido : 'Sin asignar' }}</td>
                <td class="py-3 px-6 text-left">{{ formatDate(item.created_at) }}</td>
                <td class="py-3 px-6 text-center">
                    <div class="flex item-center justify-center">
                        <router-link :to="`/incidents/${item.id}/edit`"
                            class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </router-link>
                        <button @click="deleteIncident(item.id)"
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

const incidents = ref([]);
const pagination = ref({});
const headers = [
    { key: 'id', label: 'ID' },
    { key: 'titulo', label: 'Título' },
    { key: 'estado', label: 'Estado' },
    { key: 'prioridad', label: 'Prioridad' },
    { key: 'user_id', label: 'Creado Por' },
    { key: 'assigned_to', label: 'Asignado A' },
    { key: 'created_at', label: 'Fecha' },
];

const currentOptions = ref({
    page: 1,
    per_page: 25,
    sort_by: 'created_at',
    sort_order: 'desc'
});

onMounted(async () => {
    await fetchIncidents(currentOptions.value);
});

const fetchIncidents = async (options) => {
    currentOptions.value = { ...currentOptions.value, ...options };
    try {
        const response = await axios.get('/api/admin/incidents', { params: currentOptions.value });
        incidents.value = response.data.data;
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

const deleteIncident = async (id) => {
    if (!confirm('¿Estás seguro de eliminar esta incidencia?')) return;
    try {
        await axios.delete(`/api/admin/incidents/${id}`);
        fetchIncidents(currentOptions.value);
    } catch (e) {
        console.error(e);
        alert('Error al eliminar la incidencia');
    }
};

const getStatusClass = (status) => {
    switch (status) {
        case 'abierto': return 'bg-blue-200 text-blue-600';
        case 'en_progreso': return 'bg-yellow-200 text-yellow-600';
        case 'resuelto': return 'bg-green-200 text-green-600';
        case 'cerrado': return 'bg-gray-200 text-gray-600';
        default: return 'bg-gray-200 text-gray-600';
    }
};

const getPriorityClass = (priority) => {
    switch (priority) {
        case 'baja': return 'bg-green-100 text-green-800';
        case 'media': return 'bg-blue-100 text-blue-800';
        case 'alta': return 'bg-orange-100 text-orange-800';
        case 'critica': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString();
};
</script>
