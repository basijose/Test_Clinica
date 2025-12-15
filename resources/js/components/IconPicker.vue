<template>
    <div class="relative">
        <button type="button" class="w-full flex items-center justify-between border rounded px-3 py-2 cursor-pointer bg-white focus:outline-none focus:ring-2 focus:ring-blue-500" @click="toggleDropdown">
            <div class="flex items-center">
                <i v-if="modelValue" :class="[modelValue, 'text-xl mr-3 text-gray-600']"></i>
                <span v-else class="text-gray-400">Seleccionar icono...</span>
            </div>
            <span class="text-gray-400">
                <i class="fa-solid fa-chevron-down"></i>
            </span>
        </button>

        <div v-if="isOpen" class="absolute z-50 mt-1 w-full bg-white border rounded shadow-lg max-h-80 flex flex-col">
            <!-- Search Input -->
            <div class="p-2 border-b sticky top-0 bg-white z-10">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </span>
                    <input 
                        ref="searchInput"
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Buscar icono..." 
                        class="w-full pl-8 pr-2 py-1 border rounded text-sm focus:outline-none focus:border-blue-500"
                        @click.stop
                    >
                </div>
            </div>

            <!-- Icon Grid -->
            <div class="overflow-y-auto p-2 grid grid-cols-6 gap-2">
                <div v-for="icon in filteredIcons" :key="icon" 
                    @click.stop="selectIcon(icon)"
                    class="p-2 text-center hover:bg-blue-100 rounded cursor-pointer transition-colors flex items-center justify-center h-10 group relative"
                    :class="{ 'bg-blue-200 text-blue-600': modelValue === icon }"
                    :title="icon">
                    <i :class="[icon, 'text-lg']"></i>
                </div>
                <div v-if="filteredIcons.length === 0" class="col-span-6 text-center text-gray-500 py-4 text-sm">
                    No se encontraron iconos
                </div>
            </div>
        </div>
        
        <!-- Backdrop to close -->
        <div v-if="isOpen" class="fixed inset-0 z-40" @click="isOpen = false"></div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';

const props = defineProps({
    modelValue: String
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const searchInput = ref(null);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        nextTick(() => {
            searchInput.value?.focus();
        });
    } else {
        searchQuery.value = '';
    }
};

const selectIcon = (icon) => {
    emit('update:modelValue', icon);
    isOpen.value = false;
    searchQuery.value = '';
};

const filteredIcons = computed(() => {
    if (!searchQuery.value) return icons;
    const query = searchQuery.value.toLowerCase();
    return icons.filter(icon => icon.includes(query));
});

// Expanded list of FontAwesome 6 Free icons
const icons = [
    // Medical & Health
    'fa-solid fa-user-doctor', 'fa-solid fa-user-nurse', 'fa-solid fa-hospital', 'fa-solid fa-bed-pulse',
    'fa-solid fa-stethoscope', 'fa-solid fa-syringe', 'fa-solid fa-pills', 'fa-solid fa-tablets',
    'fa-solid fa-prescription-bottle-medical', 'fa-solid fa-file-medical', 'fa-solid fa-heart-pulse',
    'fa-solid fa-lungs', 'fa-solid fa-brain', 'fa-solid fa-tooth', 'fa-solid fa-virus',
    'fa-solid fa-bacteria', 'fa-solid fa-hand-holding-medical', 'fa-solid fa-kit-medical',
    'fa-solid fa-truck-medical', 'fa-solid fa-crutch', 'fa-solid fa-wheelchair',

    // General UI
    'fa-solid fa-house', 'fa-solid fa-user', 'fa-solid fa-users', 'fa-solid fa-gear',
    'fa-solid fa-magnifying-glass', 'fa-solid fa-bell', 'fa-solid fa-envelope', 'fa-solid fa-bars',
    'fa-solid fa-xmark', 'fa-solid fa-check', 'fa-solid fa-trash', 'fa-solid fa-pen',
    'fa-solid fa-plus', 'fa-solid fa-minus', 'fa-solid fa-download', 'fa-solid fa-upload',
    'fa-solid fa-share-nodes', 'fa-solid fa-filter', 'fa-solid fa-sort', 'fa-solid fa-list',
    'fa-solid fa-thumbtack', 'fa-solid fa-star', 'fa-solid fa-flag', 'fa-solid fa-circle-info',
    'fa-solid fa-circle-question', 'fa-solid fa-triangle-exclamation', 'fa-solid fa-ban',

    // Admin & Dashboard
    'fa-solid fa-chart-line', 'fa-solid fa-chart-pie', 'fa-solid fa-chart-bar', 'fa-solid fa-gauge',
    'fa-solid fa-calendar-days', 'fa-solid fa-clock', 'fa-solid fa-clipboard-list', 'fa-solid fa-folder',
    'fa-solid fa-folder-open', 'fa-solid fa-file', 'fa-solid fa-file-pdf', 'fa-solid fa-file-excel',
    'fa-solid fa-database', 'fa-solid fa-server', 'fa-solid fa-network-wired', 'fa-solid fa-sitemap',
    'fa-solid fa-shield-halved', 'fa-solid fa-lock', 'fa-solid fa-key', 'fa-solid fa-unlock',
    'fa-solid fa-user-shield', 'fa-solid fa-fingerprint',

    // Finance & Commerce
    'fa-solid fa-money-bill-wave', 'fa-solid fa-credit-card', 'fa-solid fa-wallet', 'fa-solid fa-coins',
    'fa-solid fa-receipt', 'fa-solid fa-cart-shopping', 'fa-solid fa-shop', 'fa-solid fa-tag',
    'fa-solid fa-tags', 'fa-solid fa-percent', 'fa-solid fa-hand-holding-dollar',

    // Communication & Social
    'fa-solid fa-comment', 'fa-solid fa-comments', 'fa-solid fa-phone', 'fa-solid fa-mobile-screen',
    'fa-solid fa-video', 'fa-solid fa-paper-plane', 'fa-solid fa-address-book', 'fa-solid fa-bullhorn',

    // Arrows & Navigation
    'fa-solid fa-arrow-right', 'fa-solid fa-arrow-left', 'fa-solid fa-chevron-right', 'fa-solid fa-chevron-left',
    'fa-solid fa-chevron-down', 'fa-solid fa-chevron-up', 'fa-solid fa-angles-right', 'fa-solid fa-angles-left',
    'fa-solid fa-rotate', 'fa-solid fa-arrow-rotate-right', 'fa-solid fa-location-dot', 'fa-solid fa-map',

    // Misc
    'fa-solid fa-lightbulb', 'fa-solid fa-bolt', 'fa-solid fa-fire', 'fa-solid fa-droplet',
    'fa-solid fa-snowflake', 'fa-solid fa-sun', 'fa-solid fa-moon', 'fa-solid fa-cloud',
    'fa-solid fa-umbrella', 'fa-solid fa-plane', 'fa-solid fa-car', 'fa-solid fa-bus',
    'fa-solid fa-gift', 'fa-solid fa-trophy', 'fa-solid fa-medal', 'fa-solid fa-crown'
];
</script>
