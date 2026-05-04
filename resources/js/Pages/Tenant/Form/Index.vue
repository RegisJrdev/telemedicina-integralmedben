<script setup>
import { ref, watch, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import TenantLayout from "@/Layouts/TenantAdminLayout.vue";
import PaginationSimple from "@/Components/PaginationSimple.vue";
import { showToast } from '@/Utils/toast';
import CountdownBadge from "@/Components/CountdownBadge.vue";
const props = defineProps({
    tenant: {
        type: Object,
        required: true,
        default: () => ({ id: '', name: '' })
    },
    tenantDetails: {
        type: Object,
        default: null
    },
    tenantForms: {
        type: Object,
        required: true,
        default: () => ({
            data: [],
            links: [],
            from: 0,
            to: 0,
            total: 0
        })
    },
    filters: {
        type: Object,
        default: () => ({ status: '', search: '' })
    },
    statusOptions: {
        type: Array,
        default: () => []
    },
});
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
let searchTimeout = null;
const STATUS_CONFIG = {
    ativo: {
        label: 'ativo',
        bgColor: 'bg-green-100',
        textColor: 'text-green-700',
        canRespond: true
    },
    rascunho: {
        label: 'em edição',
        bgColor: 'bg-gray-100',
        textColor: 'text-gray-600',
        canRespond: false
    },
    pausado: {
        label: 'pausado',
        bgColor: 'bg-yellow-100',
        textColor: 'text-yellow-600',
        canRespond: false
    },
    encerrado: {
        label: 'encerrado',
        bgColor: 'bg-red-100',
        textColor: 'text-red-600',
        canRespond: false
    }
};
const getStatusConfig = (status) => STATUS_CONFIG[status] || {
    label: status,
    bgColor: 'bg-gray-100',
    textColor: 'text-gray-600',
    canRespond: false
};
const inactiveCount = computed(() => {
    return props.tenantForms.data?.filter(tf => {
        const formStatus = tf.form?.status || 'rascunho';
        return !getStatusConfig(formStatus).canRespond;
    }).length || 0;
});
const inactiveByStatus = computed(() => {
    const counts = {};
    props.tenantForms.data?.forEach(tf => {
        const formStatus = tf.form?.status || 'rascunho';
        if (!getStatusConfig(formStatus).canRespond) {
            counts[formStatus] = (counts[formStatus] || 0) + 1;
        }
    });
    return counts;
});
const filteredForms = computed(() => {
    let forms = props.tenantForms.data || [];
    if (search.value) {
        const term = search.value.toLowerCase();
        forms = forms.filter(tf =>
            tf.form?.title?.toLowerCase().includes(term) ||
            tf.form?.description?.toLowerCase().includes(term)
        );
    }
    if (status.value) {
        forms = forms.filter(tf => tf.form?.status === status.value);
    }
    return forms;
});
const accessForm = (tenantForm) => {
    const form = tenantForm.form;
    if (!form) {
        showToast('Formulário não encontrado', 'error');
        return;
    }

    const url = form.status === 'ativo'
        ? route("forms.public.show", form.slug)
        : route("forms.show", form.id);

    window.open(url, '_blank', 'noopener,noreferrer');
};
const applyFilters = () => {
    router.get(
        route('meus-formularios.index'),
        {
            search: search.value,
            status: status.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
};
watch([search, status], ([newSearch, newStatus], [oldSearch, oldStatus]) => {
    clearTimeout(searchTimeout);
    if (newSearch !== oldSearch) {
        searchTimeout = setTimeout(applyFilters, 300);
    } else {
        applyFilters();
    }
});
const clearFilters = () => {
    search.value = '';
    status.value = '';
    applyFilters();
};
const tenantPrimaryColor = computed(() => props.tenantDetails?.cor_primaria || '#0891b2');
const tenantLogo = computed(() => props.tenantDetails?.logo || null);
</script>
<template>
    <TenantLayout :tenant-details="tenantDetails">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold" :style="{ color: tenantPrimaryColor }">
                        Meus Formulários
                    </h1>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-800">
                        <span class="font-bold">Importante:</span>
                        Apenas formulários
                        <span class="font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded">ATIVOS</span>
                        podem ser preenchidos.
                    </p>
                </div>
            </div>
        </div>
        <div v-if="inactiveCount > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <div class="flex items-start gap-3">
                <svg class="h-6 w-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-yellow-800">
                        {{ inactiveCount }} formulário{{ inactiveCount === 1 ? '' : 's' }}
                        {{ inactiveCount === 1 ? 'não está ativo' : 'não estão ativos' }}
                    </p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span v-for="(count, statusKey) in inactiveByStatus" :key="statusKey"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="[getStatusConfig(statusKey).bgColor, getStatusConfig(statusKey).textColor]">
                            {{ getStatusConfig(statusKey).label }}: {{ count }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filtros -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm mb-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input v-model="search" type="text" placeholder="Buscar por título..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
                        :style="{ '--tw-ring-color': tenantPrimaryColor }" />
                </div>
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select v-model="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 bg-white">
                        <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                </div>
                <Button v-if="search || status" variant="outline" @click="clearFilters" class="h-10">
                    Limpar
                </Button>
            </div>
        </div>
        <!-- Lista de Forms -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Formulário</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dias Restantes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Data de Expiração
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Origem</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ação</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="tenantForm in filteredForms" :key="tenantForm.id"
                        class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div v-if="tenantForm.form?.status === 'ativo'"
                                        class="h-2 w-2 rounded-full bg-green-500" title="Pode preencher"></div>
                                    <div v-else class="h-2 w-2 rounded-full bg-gray-300" title="Apenas visualizar">
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ tenantForm.form?.title || 'Sem título' }}
                                    </div>
                                    <div v-if="tenantForm.principal"
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                        Principal
                                    </div>
                                    <div v-if="tenantForm.form?.description"
                                        class="text-xs text-gray-500 mt-1 line-clamp-1">
                                        {{ tenantForm.form.description }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <CountdownBadge :current-date="tenantForm?.form.atual_at_br"
                                :expire-date="tenantForm?.form.expires_at_br" title="Tempo restante:" />
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <dev class="whitespace-pre-wrap">

                                {{ tenantForm?.form.expires_at_br || 'Sem data de expiração' }}
                            </dev>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="[
                                    getStatusConfig(tenantForm.form?.status).bgColor,
                                    getStatusConfig(tenantForm.form?.status).textColor
                                ]">
                                {{ getStatusConfig(tenantForm.form?.status).label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ tenantForm.origem }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm"
                                    @click="router.visit(route('meus-formularios.show', tenantForm.form.id))">
                                    Visualizar
                                </Button>
                                <Button variant="primary" size="sm" @click="accessForm(tenantForm)" :style="{
                                    backgroundColor: tenantForm.form?.status === 'ativo'
                                        ? tenantPrimaryColor
                                        : '#9ca3af'
                                }">
                                    {{ tenantForm.form?.status === 'ativo' ? 'Preencher' : 'Visualizar' }}
                                </Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredForms.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p>Nenhum formulário encontrado.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <PaginationSimple :data="tenantForms" :links="tenantForms.links" :has-data="tenantForms.data?.length > 0"
                label="formulários" />
        </div>
    </TenantLayout>
</template>
