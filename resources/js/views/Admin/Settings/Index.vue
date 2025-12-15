<template>
    <div class="container mx-auto px-6 py-8">
        <h3 class="text-gray-700 text-3xl font-medium">Preferencias</h3>

        <div class="mt-8">
            <!-- Debug Info -->
            <div v-if="!settings.mail" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                <p class="font-bold">No se encontraron configuraciones de correo.</p>
                <p>Si acabas de instalar esto, asegúrate de ejecutar el seeder:</p>
                <code class="bg-yellow-200 p-1 rounded">php artisan db:seed --class=SettingSeeder</code>
                <div class="mt-4">
                    <p class="text-sm font-semibold">Estructura de datos recibida (Debug):</p>
                    <pre class="text-xs bg-gray-100 p-2 rounded mt-1 overflow-auto max-h-40">{{ settings }}</pre>
                </div>
            </div>

            <div v-if="settings.mail" class="bg-white shadow rounded-lg mb-6 p-6">
                <h4 class="text-lg font-semibold text-gray-700 capitalize mb-4">Datos Servidor de Mail</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="setting in settings.mail" :key="setting.id" class="col-span-1" v-show="setting.key !== 'mail_mailer'">
                        <label class="block text-sm font-medium text-gray-700">{{ setting.description }}</label>
                        
                        <!-- Text Inputs (Default) -->
                        <input v-if="setting.type === 'text' && setting.key !== 'mail_encryption'" type="text" v-model="setting.value"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                        
                        <!-- Password Input with Toggle -->
                        <div v-else-if="setting.type === 'password'" class="relative">
                            <input :type="showPasswords[setting.key] ? 'text' : 'password'" v-model="setting.value"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2 pr-10">
                            <button type="button" @click="togglePassword(setting.key)"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-gray-700 focus:outline-none mt-1">
                                <i :class="showPasswords[setting.key] ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                            </button>
                        </div>

                        <!-- Encryption Select -->
                        <select v-else-if="setting.key === 'mail_encryption'" v-model="setting.value"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                            <option value="">Ninguna</option>
                        </select>
                        
                        <!-- Boolean Select -->
                        <select v-else-if="setting.type === 'boolean'" v-model="setting.value" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                        
                        <!-- Number Input -->
                        <input v-else-if="setting.type === 'number'" type="number" v-model="setting.value"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-6">
                <h5 class="text-md font-semibold text-gray-700 mb-2">Prueba de Envío</h5>
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Email de Destino para Prueba</label>
                        <input type="email" v-model="testEmailAddress" placeholder="ejemplo@dominio.com" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    </div>
                    <button @click="sendTestEmail" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded h-10 mb-0.5">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Enviar Prueba
                    </button>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <button @click="saveSettings" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fa-solid fa-save mr-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const settings = ref({});
const testEmailAddress = ref('');
const showPasswords = ref({});

const togglePassword = (key) => {
    showPasswords.value[key] = !showPasswords.value[key];
};

const fetchSettings = async () => {
    try {
        const response = await axios.get('/api/admin/settings');
        settings.value = response.data;
    } catch (error) {
        console.error('Error fetching settings', error);
    }
};

const saveSettings = async () => {
    try {
        const flatSettings = [];
        for (const group in settings.value) {
            settings.value[group].forEach(setting => {
                flatSettings.push({ key: setting.key, value: setting.value });
            });
        }

        await axios.post('/api/admin/settings', { settings: flatSettings });
        alert('Configuración guardada correctamente');
    } catch (error) {
        alert('Error al guardar la configuración');
    }
};

const sendTestEmail = async () => {
    if (!testEmailAddress.value) return alert('Ingresa un email de destino para la prueba');
    
    try {
        await axios.post('/api/admin/settings/test-email', { email: testEmailAddress.value });
        alert('Email enviado correctamente');
    } catch (error) {
        alert('Error al enviar email: ' + (error.response?.data?.message || error.message));
    }
};

onMounted(() => {
    fetchSettings();
});
</script>
