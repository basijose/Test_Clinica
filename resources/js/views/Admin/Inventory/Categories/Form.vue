<template>
    <div class="p-2 md:p-6">
        <h1 class="text-2xl font-bold mb-4">{{ isEditing ? 'Editar Categoría' : 'Crear Categoría' }}</h1>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form @submit.prevent="handleSubmit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre</label>
                    <input v-model="form.nombre" type="text" id="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="descripcion">Descripción</label>
                    <textarea v-model="form.descripcion" id="descripcion" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="estado">Estado</label>
                    <select v-model="form.estado" id="estado" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Guardar
                    </button>
                    <router-link to="/inventory" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Cancelar
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const form = ref({
    nombre: '',
    descripcion: '',
    estado: 'activo'
});

const isEditing = computed(() => route.params.id !== undefined);

onMounted(async () => {
    try {
        if (isEditing.value) {
            const response = await axios.get(`/api/admin/inventory/categories/${route.params.id}`);
            form.value = response.data;
        }
    } catch (e) {
        console.error(e);
    }
});

const handleSubmit = async () => {
    try {
        if (isEditing.value) {
            await axios.put(`/api/admin/inventory/categories/${route.params.id}`, form.value);
        } else {
            await axios.post('/api/admin/inventory/categories', form.value);
        }
        router.push('/inventory');
    } catch (e) {
        console.error(e);
        alert('Error al guardar la categoría');
    }
};
</script>
