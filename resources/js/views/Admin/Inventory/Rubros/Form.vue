<template>
    <div class="p-2 md:p-6">
        <h1 class="text-2xl font-bold mb-4">{{ isEditing ? 'Editar Rubro' : 'Crear Rubro' }}</h1>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form @submit.prevent="handleSubmit">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Categoría</label>
                        <select v-model="form.category_id" id="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="" disabled>Seleccione una categoría</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre del Rubro</label>
                        <input v-model="form.nombre" type="text" id="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
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

                <!-- Dynamic Fields Builder -->
                <div class="mb-6 border-t pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Campos Personalizados</h2>
                        <button type="button" @click="addField" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                            <i class="fa-solid fa-plus"></i> Agregar Campo
                        </button>
                    </div>

                    <div v-if="form.fields.length === 0" class="text-gray-500 italic text-sm mb-4">
                        No hay campos definidos para este rubro.
                    </div>

                    <div v-for="(field, index) in form.fields" :key="index" class="bg-gray-50 p-4 rounded mb-2 border relative">
                        <button type="button" @click="removeField(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Etiqueta (Label)</label>
                                <input v-model="field.label" @input="generateName(field)" type="text" class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: Memoria RAM" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Tipo de Dato</label>
                                <select v-model="field.tipo" class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="text">Texto</option>
                                    <option value="number">Número</option>
                                    <option value="boolean">Si/No</option>
                                    <option value="select">Selección (Lista)</option>
                                </select>
                            </div>
                            <div class="flex items-center mt-6">
                                <input v-model="field.requerido" type="checkbox" class="mr-2">
                                <label class="text-sm text-gray-700">Requerido</label>
                            </div>
                        </div>

                        <!-- Options for Select Type -->
                        <div v-if="field.tipo === 'select'" class="mt-2">
                            <label class="block text-gray-700 text-xs font-bold mb-1">Opciones (separadas por coma)</label>
                            <input v-model="field.optionsString" type="text" class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: 4GB, 8GB, 16GB">
                        </div>
                    </div>
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

const categories = ref([]);
const form = ref({
    category_id: '',
    nombre: '',
    descripcion: '',
    estado: 'activo',
    fields: []
});

const isEditing = computed(() => route.params.id !== undefined);

onMounted(async () => {
    try {
        // Fetch Categories
        const catResponse = await axios.get('/api/admin/inventory/categories?all=true');
        categories.value = catResponse.data;

        if (isEditing.value) {
            const response = await axios.get(`/api/admin/inventory/rubros/${route.params.id}`);
            const data = response.data;
            
            // Transform fields for UI
            if (data.fields) {
                data.fields = data.fields.map(f => ({
                    ...f,
                    optionsString: f.opciones ? f.opciones.join(', ') : '',
                    requerido: !!f.requerido // Ensure boolean
                }));
            }
            
            form.value = data;
        }
    } catch (e) {
        console.error(e);
    }
});

const addField = () => {
    form.value.fields.push({
        label: '',
        nombre: '',
        tipo: 'text',
        requerido: false,
        optionsString: ''
    });
};

const removeField = (index) => {
    form.value.fields.splice(index, 1);
};

const generateName = (field) => {
    // Auto-generate internal name from label (e.g., "Memoria RAM" -> "memoria_ram")
    field.nombre = field.label.toLowerCase().replace(/[^a-z0-9]/g, '_');
};

const handleSubmit = async () => {
    try {
        // Prepare payload
        const payload = { ...form.value };
        payload.fields = payload.fields.map(f => {
            const fieldData = {
                id: f.id, // Keep ID if exists (for updates)
                label: f.label,
                nombre: f.nombre,
                tipo: f.tipo,
                requerido: f.requerido,
                opciones: null
            };

            if (f.tipo === 'select' && f.optionsString) {
                fieldData.opciones = f.optionsString.split(',').map(s => s.trim()).filter(s => s);
            }

            return fieldData;
        });

        if (isEditing.value) {
            await axios.put(`/api/admin/inventory/rubros/${route.params.id}`, payload);
        } else {
            await axios.post('/api/admin/inventory/rubros', payload);
        }
        router.push('/inventory');
    } catch (e) {
        console.error(e);
        alert('Error al guardar el rubro');
    }
};
</script>
