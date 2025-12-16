<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">{{ isEditing ? 'Editar Intervención' : 'Registrar Intervención' }}</h1>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form @submit.prevent="handleSubmit">
                
                <!-- Equipment Search -->
                <div class="mb-4 relative">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="equipment">Equipo</label>
                    <div class="relative">
                        <input type="text" v-model="searchQuery" @input="debouncedSearch" @focus="showResults = true"
                            placeholder="Buscar por nombre, serie, ubicación..."
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            :class="{'border-red-500': !form.equipment_id && showErrors}"
                        >
                        <div v-if="selectedEquipment" class="absolute right-2 top-2 text-green-600 font-bold">
                            <i class="fa-solid fa-check-circle"></i> Seleccionado
                        </div>
                    </div>
                    <div v-if="showResults && searchResults.length > 0" class="absolute z-10 bg-white border rounded w-full mt-1 max-h-60 overflow-y-auto shadow-lg">
                        <div v-for="item in searchResults" :key="item.id" @click="selectEquipment(item)"
                            class="p-2 hover:bg-gray-100 cursor-pointer border-b last:border-b-0">
                            <div class="font-bold">{{ item.nombre }}</div>
                            <div class="text-xs text-gray-500">
                                Serie: {{ item.serial || 'N/A' }} | Ubicación: {{ item.location?.nombre || 'Sin asignar' }}
                            </div>
                        </div>
                    </div>
                    <p v-if="!form.equipment_id && showErrors" class="text-red-500 text-xs italic">Debe seleccionar un equipo.</p>
                </div>

                <div class="flex gap-4 mb-4">
                    <div class="w-1/2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="fecha">Fecha</label>
                        <input v-model="form.fecha_intervencion" type="date" id="fecha" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="hora">Hora</label>
                        <input v-model="form.hora_intervencion" type="time" id="hora" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                </div>

                <div class="flex gap-4 mb-4">
                    <div class="w-1/2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="duracion">Duración</label>
                        <input v-model="form.duracion" type="text" id="duracion" placeholder="Ej: 2 horas" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="fecha_fin">Fecha Finalización (Opcional)</label>
                        <input v-model="form.fecha_finalizacion" type="date" id="fecha_fin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="detalle">Detalle</label>
                    <textarea v-model="form.detalle" id="detalle" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Guardar
                    </button>
                    <router-link to="/interventions" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
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
import { debounce } from 'lodash';

const route = useRoute();
const router = useRouter();

const form = ref({
    equipment_id: null,
    fecha_intervencion: new Date().toISOString().split('T')[0],
    hora_intervencion: new Date().toTimeString().split(' ')[0].slice(0, 5),
    duracion: '',
    fecha_finalizacion: '',
    detalle: ''
});

const searchQuery = ref('');
const searchResults = ref([]);
const showResults = ref(false);
const selectedEquipment = ref(null);
const showErrors = ref(false);

const isEditing = computed(() => route.params.id !== undefined);

onMounted(async () => {
    if (isEditing.value) {
        try {
            const response = await axios.get(`/api/admin/interventions/${route.params.id}`);
            form.value = response.data;
            if (response.data.equipment) {
                selectEquipment(response.data.equipment);
            }
        } catch (e) {
            console.error(e);
        }
    }
});

const debouncedSearch = debounce(async () => {
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }
    try {
        const response = await axios.get('/api/admin/inventory/equipment', { params: { search: searchQuery.value } });
        searchResults.value = response.data.data;
        showResults.value = true;
    } catch (e) {
        console.error(e);
    }
}, 300);

const selectEquipment = (item) => {
    form.value.equipment_id = item.id;
    selectedEquipment.value = item;
    searchQuery.value = item.nombre;
    showResults.value = false;
};

const handleSubmit = async () => {
    if (!form.value.equipment_id) {
        showErrors.value = true;
        return;
    }

    try {
        if (isEditing.value) {
            await axios.put(`/api/admin/interventions/${route.params.id}`, form.value);
        } else {
            await axios.post('/api/admin/interventions', form.value);
        }
        router.push('/interventions');
    } catch (e) {
        console.error(e);
        alert('Error al guardar la intervención');
    }
};
</script>
