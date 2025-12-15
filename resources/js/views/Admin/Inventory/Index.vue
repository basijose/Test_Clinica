<template>
    <div class="p-2 md:p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
            <h1 class="text-2xl font-bold">Inventario</h1>
            
            <div class="flex gap-2 w-full md:w-auto">
                <!-- Search Bar -->
                <div class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input 
                        v-model="searchQuery" 
                        @input="handleSearch"
                        type="text" 
                        placeholder="Buscar equipo..." 
                        class="w-full pl-8 pr-4 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Manage Dropdown -->
                <div class="relative">
                    <button @click="showManageMenu = !showManageMenu" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-3 rounded flex items-center" title="Gestionar">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    
                    <div v-if="showManageMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 py-1 border">
                        <router-link to="/inventory/categories" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" @click="showManageMenu = false">
                            <i class="fa-solid fa-tags mr-2"></i> Categorías
                        </router-link>
                        <router-link to="/inventory/rubros" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" @click="showManageMenu = false">
                            <i class="fa-solid fa-layer-group mr-2"></i> Rubros
                        </router-link>
                        <router-link to="/inventory/locations" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" @click="showManageMenu = false">
                            <i class="fa-solid fa-location-dot mr-2"></i> Ubicaciones
                        </router-link>
                    </div>
                    <!-- Backdrop -->
                    <div v-if="showManageMenu" class="fixed inset-0 z-40" @click="showManageMenu = false"></div>
                </div>

                <!-- Create Button (Direct to Equipment) -->
                <router-link to="/inventory/equipment/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center whitespace-nowrap">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo Equipo
                </router-link>
            </div>
        </div>

        <DataTable :headers="headers" :items="equipment" :pagination="pagination" @update:options="fetchEquipment">
            <template #row="{ item }">
                <td class="py-3 px-6 text-left whitespace-nowrap">{{ item.id }}</td>
                <td class="py-3 px-6 text-left font-bold">{{ item.nombre }}</td>
                <td class="py-3 px-6 text-left">
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-500">
                        {{ item.rubro?.nombre }}
                    </span>
                </td>
                <td class="py-3 px-6 text-left text-sm text-gray-500">{{ item.rubro?.category?.nombre }}</td>
                <td class="py-3 px-6 text-left">{{ item.location?.nombre || '-' }}</td>
                <td class="py-3 px-6 text-left">
                    <span :class="getStatusClass(item.estado)" class="px-2 py-1 rounded-full text-xs font-semibold">
                        {{ item.estado }}
                    </span>
                </td>
                <td class="py-3 px-6 text-center">
                    <div class="flex item-center justify-center">
                        <router-link :to="`/inventory/equipment/${item.id}/edit`" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="Editar">
                            <i class="fa-solid fa-pen"></i>
                        </router-link>
                        <button @click="deleteEquipment(item.id)" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
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

// Custom debounce function to avoid lodash dependency
const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const equipment = ref([]);
const pagination = ref({});
const searchQuery = ref('');
const showManageMenu = ref(false);

const headers = [
    { key: 'id', label: 'ID' },
    { key: 'nombre', label: 'Nombre' },
    { key: 'rubro_id', label: 'Rubro' },
    { key: 'category', label: 'Categoría' },
    { key: 'location_id', label: 'Ubicación' },
    { key: 'estado', label: 'Estado' },
];

const currentOptions = ref({
    page: 1,
    per_page: 25,
    sort_by: 'id',
    sort_order: 'desc'
});

onMounted(async () => {
    await fetchEquipment(currentOptions.value);
});

const fetchEquipment = async (options) => {
    currentOptions.value = { ...currentOptions.value, ...options };
    try {
        const params = { ...currentOptions.value, search: searchQuery.value };
        const response = await axios.get('/api/admin/inventory/equipment', { params });
        equipment.value = response.data.data;
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

const handleSearch = debounce(() => {
    currentOptions.value.page = 1;
    fetchEquipment(currentOptions.value);
}, 300);

const deleteEquipment = async (id) => {
    if (!confirm('¿Estás seguro de eliminar este equipo?')) return;
    try {
        await axios.delete(`/api/admin/inventory/equipment/${id}`);
        fetchEquipment(currentOptions.value);
    } catch (e) {
        console.error(e);
        alert('Error al eliminar el equipo');
    }
};

const getStatusClass = (status) => {
    switch (status) {
        case 'operativo': return 'bg-green-100 text-green-800';
        case 'reparacion': return 'bg-yellow-100 text-yellow-800';
        case 'baja': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>
