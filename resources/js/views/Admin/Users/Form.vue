<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">{{ isEditing ? 'Editar Usuario' : 'Crear Usuario' }}</h1>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form @submit.prevent="handleSubmit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre</label>
                    <input v-model="form.nombre" type="text" id="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="apellido">Apellido</label>
                    <input v-model="form.apellido" type="text" id="apellido" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input v-model="form.email" type="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Contraseña {{ isEditing ? '(Dejar en blanco para mantener)' : '' }}</label>
                    <input v-model="form.password" type="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :required="!isEditing">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="role">Rol</label>
                    <select v-model="form.role_id" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.nombre_corto }}</option>
                    </select>
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
                    <router-link to="/users" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
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
    apellido: '',
    email: '',
    password: '',
    role_id: '',
    estado: 'activo'
});

const roles = ref([]);
const isEditing = computed(() => route.params.id !== undefined);

onMounted(async () => {
    try {
        const rolesResponse = await axios.get('/api/admin/roles');
        roles.value = rolesResponse.data;

        if (isEditing.value) {
            const userResponse = await axios.get(`/api/admin/users/${route.params.id}`);
            form.value = { ...userResponse.data, password: '' };
        }
    } catch (e) {
        console.error(e);
    }
});

const handleSubmit = async () => {
    try {
        const payload = { ...form.value };
        if (!payload.password) {
            delete payload.password;
        }

        if (isEditing.value) {
            await axios.put(`/api/admin/users/${route.params.id}`, payload);
        } else {
            await axios.post('/api/admin/users', payload);
        }
        router.push('/users');
    } catch (e) {
        console.error(e);
        if (e.response && e.response.status === 422) {
            const errors = e.response.data.errors;
            let errorMessage = 'Error de validación:\n';
            for (const key in errors) {
                errorMessage += `- ${errors[key][0]}\n`;
            }
            alert(errorMessage);
        } else {
            alert('Error al guardar el usuario: ' + (e.response?.data?.message || e.message));
        }
    }
};
</script>
