<template>
    <div class="p-2 md:p-6">
        <h1 class="text-2xl font-bold mb-4">{{ isEditing ? 'Editar Equipo' : 'Crear Equipo' }}</h1>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form @submit.prevent="handleSubmit">
                
                <!-- Classification -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Categoría</label>
                        <select v-model="selectedCategory" @change="fetchRubros" id="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled>Seleccione una categoría</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="rubro">Rubro</label>
                        <select v-model="form.rubro_id" @change="handleRubroChange" id="rubro" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :disabled="!selectedCategory" required>
                            <option value="" disabled>Seleccione un rubro</option>
                            <option v-for="rub in rubros" :key="rub.id" :value="rub.id">{{ rub.nombre }}</option>
                        </select>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre / Identificador</label>
                        <input v-model="form.nombre" type="text" id="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="serial">Número de Serie</label>
                        <input v-model="form.serial" type="text" id="serial" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="location">Ubicación</label>
                        <select v-model="form.location_id" id="location" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled>Seleccione una ubicación</option>
                            <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.nombre }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="estado">Estado</label>
                        <select v-model="form.estado" id="estado" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="operativo">Operativo</option>
                            <option value="reparacion">En Reparación</option>
                            <option value="baja">De Baja</option>
                        </select>
                    </div>
                </div>

                <!-- Dynamic Attributes -->
                <div v-if="currentRubroFields.length > 0" class="mb-6 border-t pt-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Detalles Técnicos</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="field in currentRubroFields" :key="field.id">
                            <label class="block text-gray-700 text-sm font-bold mb-2">{{ field.label }}</label>
                            
                            <!-- Text/Number Input -->
                            <input v-if="field.tipo === 'text' || field.tipo === 'number'" 
                                v-model="form.attributes[field.nombre]" 
                                :type="field.tipo" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                :required="field.requerido">

                            <!-- Select Input -->
                            <select v-if="field.tipo === 'select'" 
                                v-model="form.attributes[field.nombre]" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                :required="field.requerido">
                                <option value="">Seleccione...</option>
                                <option v-for="opt in field.opciones" :key="opt" :value="opt">{{ opt }}</option>
                            </select>

                            <!-- Boolean Input -->
                            <div v-if="field.tipo === 'boolean'" class="flex items-center mt-2">
                                <input v-model="form.attributes[field.nombre]" type="checkbox" class="mr-2 h-5 w-5">
                                <span class="text-gray-700">{{ field.label }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
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
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const categories = ref([]);
const rubros = ref([]);
const locations = ref([]);
const currentRubroFields = ref([]);

const selectedCategory = ref('');

const form = ref({
    rubro_id: '',
    location_id: '',
    nombre: '',
    serial: '',
    estado: 'operativo',
    attributes: {}
});

const isEditing = computed(() => route.params.id !== undefined);

onMounted(async () => {
    try {
        // Load dependencies
        const [catRes, locRes] = await Promise.all([
            axios.get('/api/admin/inventory/categories?all=true'),
            axios.get('/api/admin/inventory/locations?all=true')
        ]);
        categories.value = catRes.data;
        locations.value = locRes.data;

        if (isEditing.value) {
            const response = await axios.get(`/api/admin/inventory/equipment/${route.params.id}`);
            const data = response.data;
            
            form.value = {
                rubro_id: data.rubro_id,
                location_id: data.location_id,
                nombre: data.nombre,
                serial: data.serial,
                estado: data.estado,
                attributes: data.attributes || {}
            };

            // Pre-fill category and rubros
            if (data.rubro) {
                selectedCategory.value = data.rubro.category_id;
                await fetchRubros(); // Load rubros for this category
                
                // Load fields for this rubro
                currentRubroFields.value = data.rubro.fields || [];
            }
        }
    } catch (e) {
        console.error(e);
    }
});

const fetchRubros = async () => {
    if (!selectedCategory.value) {
        rubros.value = [];
        return;
    }
    try {
        const response = await axios.get(`/api/admin/inventory/rubros?category_id=${selectedCategory.value}`);
        rubros.value = response.data;
    } catch (e) {
        console.error(e);
    }
};

const handleRubroChange = async () => {
    if (!form.value.rubro_id) {
        currentRubroFields.value = [];
        return;
    }
    
    // Find selected rubro object to get its fields
    // If not loaded in list (should be), fetch it
    const rubro = rubros.value.find(r => r.id === form.value.rubro_id);
    if (rubro) {
        // We need to fetch the full rubro with fields if not already present
        // But wait, the list endpoint might not return fields. Let's fetch individual rubro to be safe.
        try {
            const response = await axios.get(`/api/admin/inventory/rubros/${form.value.rubro_id}`);
            currentRubroFields.value = response.data.fields || [];
            
            // Initialize attributes if empty
            if (!isEditing.value) {
                form.value.attributes = {};
            }
        } catch (e) {
            console.error(e);
        }
    }
};

const handleSubmit = async () => {
    try {
        if (isEditing.value) {
            await axios.put(`/api/admin/inventory/equipment/${route.params.id}`, form.value);
        } else {
            await axios.post('/api/admin/inventory/equipment', form.value);
        }
        router.push('/inventory');
    } catch (e) {
        console.error(e);
        alert('Error al guardar el equipo');
    }
};
</script>
