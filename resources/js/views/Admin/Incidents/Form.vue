<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">{{ isEditing ? 'Editar Incidencia' : 'Nueva Incidencia' }}</h1>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form @submit.prevent="handleSubmit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="titulo">Título</label>
                    <input v-model="form.titulo" type="text" id="titulo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="descripcion">Descripción</label>
                    <textarea v-model="form.descripcion" id="descripcion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="prioridad">Prioridad</label>
                        <select v-model="form.prioridad" id="prioridad" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="baja">Baja</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="critica">Crítica</option>
                        </select>
                    </div>
                    
                    <div v-if="isEditing">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="estado">Estado</label>
                        <select v-model="form.estado" id="estado" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="abierto">Abierto</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="resuelto">Resuelto</option>
                            <option value="cerrado">Cerrado</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="assigned_to">Asignar a</label>
                    <select v-model="form.assigned_to" id="assigned_to" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Sin asignar</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.nombre }} {{ user.apellido }}</option>
                    </select>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Guardar
                    </button>
                    <router-link to="/incidents" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
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
    titulo: '',
    descripcion: '',
    prioridad: 'media',
    estado: 'abierto',
    assigned_to: ''
});

const users = ref([]);
const isEditing = computed(() => route.params.id !== undefined);

onMounted(async () => {
    try {
        // Fetch users for assignment (assuming we want all users, or maybe just admins/technicians)
        // Using a hypothetical 'all' param or just paginated for now, but ideally should be a specific endpoint
        const usersResponse = await axios.get('/api/admin/users?per_page=100'); 
        users.value = usersResponse.data.data;

        if (isEditing.value) {
            const response = await axios.get(`/api/admin/incidents/${route.params.id}`);
            form.value = response.data;
        }
    } catch (e) {
        console.error(e);
    }
});

const handleSubmit = async () => {
    try {
        if (isEditing.value) {
            await axios.put(`/api/admin/incidents/${route.params.id}`, form.value);
        } else {
            await axios.post('/api/admin/incidents', form.value);
        }
        router.push('/incidents');
    } catch (e) {
        console.error(e);
        alert('Error al guardar la incidencia');
    }
};
</script>
