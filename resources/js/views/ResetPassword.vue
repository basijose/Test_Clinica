<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Restablecer Contraseña
                </h2>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
                <input type="hidden" name="token" v-model="token">
                
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label for="email-address" class="sr-only">Email</label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required
                            class="appearance-none rounded-t-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                            placeholder="Email" v-model="email">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="sr-only">Nueva Contraseña</label>
                        <input id="password" name="password" type="password" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                            placeholder="Nueva Contraseña" v-model="password">
                    </div>
                    <div>
                        <label for="password_confirmation" class="sr-only">Confirmar Contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="appearance-none rounded-b-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                            placeholder="Confirmar Contraseña" v-model="password_confirmation">
                    </div>
                </div>

                <div v-if="message" class="text-green-600 text-sm text-center">
                    {{ message }}
                </div>
                <div v-if="error" class="text-red-600 text-sm text-center">
                    {{ error }}
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Restablecer Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const token = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const message = ref('');
const error = ref('');

onMounted(() => {
    token.value = route.params.token;
    email.value = route.query.email || '';
});

const handleSubmit = async () => {
    message.value = '';
    error.value = '';
    try {
        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.post('/api/reset-password', {
            token: token.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value
        });
        message.value = response.data.status;
        setTimeout(() => {
            router.push('/login');
        }, 2000);
    } catch (e) {
        if (e.response && e.response.data) {
            error.value = e.response.data.message || 'Error al restablecer la contraseña.';
        } else {
            error.value = 'Ocurrió un error inesperado.';
        }
    }
};
</script>
