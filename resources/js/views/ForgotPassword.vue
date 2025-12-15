<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Recuperar Contraseña
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Ingresa tu email y te enviaremos un enlace para resetear tu contraseña.
                </p>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email-address" class="sr-only">Email</label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required
                            class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                            placeholder="Email" v-model="email">
                    </div>
                </div>

                <div v-if="message" class="text-green-600 text-sm text-center">
                    {{ message }}
                </div>
                <div v-if="error" class="text-red-600 text-sm text-center">
                    {{ error }}
                </div>

                <div class="flex items-center justify-between">
                    <router-link to="/login" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Volver al Login
                    </router-link>
                    <button type="submit"
                        class="group relative flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enviar Enlace
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const email = ref('');
const message = ref('');
const error = ref('');

const handleSubmit = async () => {
    message.value = '';
    error.value = '';
    try {
        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.post('/api/forgot-password', { email: email.value });
        message.value = response.data.status;
    } catch (e) {
        if (e.response && e.response.data) {
            error.value = e.response.data.message || 'Error al enviar el correo.';
        } else {
            error.value = 'Ocurrió un error inesperado.';
        }
    }
};
</script>
