<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Mi Perfil</h1>
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
                
                <div class="mb-4 border-t pt-4 mt-4">
                    <h3 class="text-lg font-semibold mb-2">Cambiar Contraseña (Opcional)</h3>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Nueva Contraseña</label>
                        <input v-model="form.password" type="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">Confirmar Contraseña</label>
                        <input v-model="form.password_confirmation" type="password" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Rol</label>
                    <p class="text-gray-900 bg-gray-100 p-2 rounded">{{ roleName }}</p>
                    <p class="text-xs text-gray-500 mt-1">No puedes cambiar tu propio rol.</p>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Actualizar Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const form = ref({
    nombre: '',
    apellido: '',
    email: '',
    password: '',
    password_confirmation: ''
});
const roleName = ref('');

onMounted(async () => {
    try {
        const response = await axios.get('/api/profile');
        const user = response.data;
        form.value.nombre = user.nombre;
        form.value.apellido = user.apellido;
        form.value.email = user.email;
        roleName.value = user.role?.nombre_corto || 'Sin Rol';
    } catch (e) {
        console.error(e);
    }
});

const handleSubmit = async () => {
    try {
        const payload = { ...form.value };
        if (!payload.password) {
            delete payload.password;
            delete payload.password_confirmation;
        }

        await axios.put('/api/profile', payload);
        alert('Perfil actualizado correctamente');
        // Update auth store with new data
        await auth.fetchUser();
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
            alert('Error al actualizar el perfil');
        }
    }
};
</script>
