<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">{{ isEditing ? 'Editar Rol' : 'Crear Rol' }}</h1>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form @submit.prevent="handleSubmit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre_corto">Nombre Corto</label>
                    <input v-model="form.nombre_corto" type="text" id="nombre_corto" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="descripcion">Descripción</label>
                    <input v-model="form.descripcion" type="text" id="descripcion" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="estado">Estado</label>
                    <select v-model="form.estado" id="estado" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Accesos</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="access in accesses" :key="access.id" class="flex items-center">
                            <input type="checkbox" :id="`access-${access.id}`" :value="access.id" v-model="form.accesses" class="mr-2 leading-tight">
                            <label :for="`access-${access.id}`" class="text-sm text-gray-700">
                                {{ access.descripcion }} ({{ access.destino }})
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Guardar
                    </button>
                    <router-link to="/roles" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
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
    nombre_corto: '',
    descripcion: '',
    estado: 'activo',
    accesses: []
});

const accesses = ref([]);
const isEditing = computed(() => route.params.id !== undefined);

onMounted(async () => {
    try {
        const accessesResponse = await axios.get('/api/admin/accesses');
        accesses.value = accessesResponse.data;

        if (isEditing.value) {
            const response = await axios.get(`/api/admin/roles/${route.params.id}`);
            form.value = {
                ...response.data,
                accesses: response.data.accesses.map(a => a.id)
            };
        }
    } catch (e) {
        console.error(e);
    }
});

const handleSubmit = async () => {
    try {
        if (isEditing.value) {
            await axios.put(`/api/admin/roles/${route.params.id}`, form.value);
        } else {
            await axios.post('/api/admin/roles', form.value);
        }
        router.push('/roles');
    } catch (e) {
        console.error(e);
        alert('Error al guardar el rol');
    }
};
</script>
