<template>
    <div class="p-1 md:p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Intervenciones</h1>
            <router-link to="/interventions/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Registrar Intervención
            </router-link>
        </div>

        <div class="bg-white shadow-md rounded my-6">
            <!-- Search Filter -->
            <div class="p-4 border-b border-gray-200">
                <input v-model="search" @input="debouncedSearch" type="text" placeholder="Buscar intervenciones..."
                    class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <DataTable :headers="headers" :items="interventions" :pagination="pagination" @update:options="fetchInterventions">
                <template #row="{ item }">
                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ item.id }}</td>
                    <td class="py-3 px-6 text-left font-bold">{{ item.equipment?.nombre || 'Equipo Eliminado' }}</td>
                    <td class="py-3 px-6 text-left">{{ item.fecha_intervencion }}</td>
                    <td class="py-3 px-6 text-left">{{ item.duracion }}</td>
                    <td class="py-3 px-6 text-left truncate max-w-xs" :title="item.detalle">{{ item.detalle }}</td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center">
                            <router-link :to="`/interventions/${item.id}/edit`"
                                class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </router-link>
                            <button @click="deleteIntervention(item.id)"
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
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from '../../../components/DataTable.vue';
import { debounce } from 'lodash';

const interventions = ref([]);
const pagination = ref({});
const search = ref('');

const headers = [
    { key: 'id', label: 'ID' },
    { key: 'equipment.nombre', label: 'Equipo' },
    { key: 'fecha_intervencion', label: 'Fecha' },
    { key: 'duracion', label: 'Duración' },
    { key: 'detalle', label: 'Detalle' },
    { key: 'actions', label: 'Acciones', align: 'center' },
];

const currentOptions = ref({
    page: 1,
    per_page: 25,
    sort_by: 'id',
    sort_order: 'desc'
});

onMounted(async () => {
    await fetchInterventions(currentOptions.value);
});

const fetchInterventions = async (options) => {
    currentOptions.value = { ...currentOptions.value, ...options };
    try {
        const response = await axios.get('/api/admin/interventions', { 
            params: { ...currentOptions.value, search: search.value } 
        });
        interventions.value = response.data.data;
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

const debouncedSearch = debounce(() => {
    currentOptions.value.page = 1;
    fetchInterventions(currentOptions.value);
}, 300);

const deleteIntervention = async (id) => {
    if (!confirm('¿Estás seguro de eliminar esta intervención?')) return;
    try {
        await axios.delete(`/api/admin/interventions/${id}`);
        fetchInterventions(currentOptions.value);
    } catch (e) {
        console.error(e);
        alert('Error al eliminar la intervención');
    }
};
</script>
