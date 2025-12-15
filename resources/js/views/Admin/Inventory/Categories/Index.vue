<template>
    <div class="p-2 md:p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Categorías</h1>
            <div class="flex gap-2">
                <router-link to="/inventory" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Volver
                </router-link>
                <router-link to="/inventory/categories/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fa-solid fa-plus mr-2"></i> Nueva Categoría
                </router-link>
            </div>
        </div>

        <DataTable :headers="headers" :items="categories" :pagination="pagination" @update:options="fetchCategories">
            <template #row="{ item }">
                <td class="py-3 px-6 text-left whitespace-nowrap">{{ item.id }}</td>
                <td class="py-3 px-6 text-left font-bold">{{ item.nombre }}</td>
                <td class="py-3 px-6 text-left">{{ item.descripcion }}</td>
                <td class="py-3 px-6 text-left">
                    <span :class="item.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded-full text-xs font-semibold">
                        {{ item.estado }}
                    </span>
                </td>
                <td class="py-3 px-6 text-center">
                    <div class="flex item-center justify-center">
                        <router-link :to="`/inventory/categories/${item.id}/edit`" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="Editar">
                            <i class="fa-solid fa-pen"></i>
                        </router-link>
                        <button @click="deleteCategory(item.id)" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110" title="Eliminar">
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
import DataTable from '../../../../components/DataTable.vue';

const categories = ref([]);
const pagination = ref({});

const headers = [
    { key: 'id', label: 'ID' },
    { key: 'nombre', label: 'Nombre' },
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
    await fetchCategories(currentOptions.value);
});

const fetchCategories = async (options) => {
    currentOptions.value = { ...currentOptions.value, ...options };
    try {
        const response = await axios.get('/api/admin/inventory/categories', { params: currentOptions.value });
        categories.value = response.data.data;
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

const deleteCategory = async (id) => {
    if (!confirm('¿Estás seguro de eliminar esta categoría?')) return;
    try {
        await axios.delete(`/api/admin/inventory/categories/${id}`);
        fetchCategories(currentOptions.value);
    } catch (e) {
        console.error(e);
        alert('Error al eliminar la categoría');
    }
};
</script>
