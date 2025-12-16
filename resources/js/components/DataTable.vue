<template>
    <div class="bg-white shadow-md rounded my-6">
        <!-- Top Controls -->
        <div class="p-4 flex justify-between items-center border-b border-gray-200">
            <div class="flex items-center">
                <span class="mr-2 text-gray-600">Mostrar</span>
                <select v-model="internalPerPage" @change="updateOptions"
                    class="appearance-none border rounded py-1 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                    <option :value="100">100</option>
                </select>
                <span class="ml-2 text-gray-600">registros</span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th v-for="header in headers" :key="header.key" @click="sortBy(header.key)"
                            :class="['py-3 px-6 cursor-pointer hover:bg-gray-300 transition-colors duration-200', header.align ? `text-${header.align}` : 'text-left']">
                            <div class="flex items-center">
                                {{ header.label }}
                                <span v-if="internalSortBy === header.key" class="ml-1">
                                    {{ internalSortOrder === 'asc' ? '↑' : '↓' }}
                                </span>
                            </div>
                        </th>
                        <th v-if="actions" class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="item in items" :key="item.id" class="border-b border-gray-200 hover:bg-gray-100">
                        <slot name="row" :item="item"></slot>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td :colspan="headers.length + (actions ? 1 : 0)" class="py-3 px-6 text-center">
                            No hay datos disponibles
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 flex justify-between items-center border-t border-gray-200" v-if="pagination.total > 0">
            <div class="text-sm text-gray-600">
                Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }} registros
            </div>
            <div class="flex">
                <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                    class="px-3 py-1 rounded-l border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-50">
                    Anterior
                </button>
                <button v-for="page in visiblePages" :key="page" @click="changePage(page)"
                    :class="['px-3 py-1 border-t border-b border-gray-300', page === pagination.current_page ? 'bg-blue-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100']">
                    {{ page }}
                </button>
                <button @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="px-3 py-1 rounded-r border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-50">
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    headers: {
        type: Array,
        required: true
    },
    items: {
        type: Array,
        required: true
    },
    pagination: {
        type: Object,
        required: true
    },
    actions: {
        type: Boolean,
        default: true
    },
    defaultSortBy: {
        type: String,
        default: 'id'
    },
    defaultSortOrder: {
        type: String,
        default: 'desc'
    },
    defaultPerPage: {
        type: Number,
        default: 25
    }
});

const emit = defineEmits(['update:options']);

const internalSortBy = ref(props.defaultSortBy);
const internalSortOrder = ref(props.defaultSortOrder);
const internalPerPage = ref(props.defaultPerPage);

const sortBy = (key) => {
    if (internalSortBy.value === key) {
        internalSortOrder.value = internalSortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        internalSortBy.value = key;
        internalSortOrder.value = 'asc';
    }
    updateOptions();
};

const changePage = (page) => {
    if (page >= 1 && page <= props.pagination.last_page) {
        emit('update:options', {
            page: page,
            per_page: internalPerPage.value,
            sort_by: internalSortBy.value,
            sort_order: internalSortOrder.value
        });
    }
};

const updateOptions = () => {
    emit('update:options', {
        page: 1, // Reset to page 1 on sort/per_page change
        per_page: internalPerPage.value,
        sort_by: internalSortBy.value,
        sort_order: internalSortOrder.value
    });
};

const visiblePages = computed(() => {
    const pages = [];
    const total = props.pagination.last_page;
    const current = props.pagination.current_page;
    const delta = 2;

    for (let i = 1; i <= total; i++) {
        if (i === 1 || i === total || (i >= current - delta && i <= current + delta)) {
            pages.push(i);
        } else if (pages[pages.length - 1] !== '...') {
            pages.push('...');
        }
    }
    return pages.filter(p => p !== '...'); // Simplified for now, can add ellipsis logic later if needed
});
</script>
