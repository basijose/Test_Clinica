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

            <div class="flex justify-end gap-4">
                <button @click="openTestModal = true" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Probar Email
                </button>
                <button @click="saveSettings" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fa-solid fa-save mr-2"></i> Guardar Cambios
                </button>
            </div>
        </div>

        <!-- Test Email Modal -->
        <Teleport to="body">
            <div v-if="openTestModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="openTestModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Enviar Email de Prueba</h3>
                                    <div class="mt-2">
                                        <input type="email" v-model="testEmailAddress" placeholder="Ingresa un email para recibir la prueba" class="w-full border rounded p-2" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="sendTestEmail" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Enviar
                            </button>
                            <button type="button" @click="openTestModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const settings = ref({});
const openTestModal = ref(false);
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
    if (!testEmailAddress.value) return alert('Ingresa un email');
    
    try {
        await axios.post('/api/admin/settings/test-email', { email: testEmailAddress.value });
        alert('Email enviado correctamente');
        openTestModal.value = false;
    } catch (error) {
        alert('Error al enviar email: ' + (error.response?.data?.message || error.message));
    }
};

onMounted(() => {
    fetchSettings();
});
</script>
