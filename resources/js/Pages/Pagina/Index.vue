<script setup>
import CentralAdminLayout from '@/Layouts/CentralAdminLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Pencil, Trash2, Plus, Search, X, Building2, ShieldAlert, Globe, Database } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';
import Button from '@/Components/ui/button/Button.vue';
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue';

// ✅ ALTERADO: tenant → tenants (recebe objeto paginado do Laravel)
const props = defineProps({
    tenants: {
        type: Object,
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
        default: () => ({ search: '' })
    }
});

const page = usePage();
const auth = computed(() => page.props.authUser);
const can = computed(() => page.props.authUser?.can?.paginas || {});
const canManage = computed(() => page.props.authUser?.can?.manage || false);
const flashMessage = computed(() => page.props.flash?.message);
const flashType = computed(() => page.props.flash?.type);

const search = ref(props.filters.search || '');
const searchInput = ref(null);
let searchTimer = null;

const deleteModal = ref({
    show: false,
    tenant: null,
    isProcessing: false
});

// ✅ ALTERADO: props.tenant → props.tenants
const tenantList = computed(() => props.tenants?.data || []);

const paginationLinks = computed(() => {
    // ✅ ALTERADO: props.tenant → props.tenants
    return (props.tenants?.links || []).map(link => ({
        ...link,
        label: link.label.replace('&laquo;', '‹').replace('&raquo;', '›')
    }));
});

const hasTenants = computed(() => tenantList.value.length > 0);
const hasSearch = computed(() => search.value.length > 0);
const hasActiveFilters = computed(() => hasSearch.value);

const performSearch = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        const params = {};
        if (search.value.trim()) params.search = search.value.trim();
        router.get(
            route('pagina.index'),  // ✅ ALTERADO: route correta
            params,
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['tenants', 'filters']  // ✅ ALTERADO: tenants
            }
        );
    }, 300);
};

watch(search, (newVal, oldVal) => {
    if (newVal !== oldVal) performSearch();
});

const clearSearch = () => {
    search.value = '';
    searchInput.value?.focus();
    performSearch();
};

const openDeleteModal = (item) => {
    if (!can.value.delete) {
        router.visit(route('unauthorized'));
        return;
    }
    deleteModal.value = {
        show: true,
        tenant: item,
        isProcessing: false
    };
};

const closeDeleteModal = () => {
    deleteModal.value.show = false;
    setTimeout(() => {
        deleteModal.value.tenant = null;
        deleteModal.value.isProcessing = false;
    }, 200);
};

const confirmDelete = () => {
    if (!deleteModal.value.tenant) return;
    deleteModal.value.isProcessing = true;
    // ✅ ALTERADO: route correta
    router.delete(route('pagina.destroy', deleteModal.value.tenant.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
        },
        onError: (errors) => {
            const errorMessage = Object.values(errors).flat()[0] || 'Erro ao excluir tenant';
            alert(errorMessage);
        },
        onFinish: () => {
            deleteModal.value.isProcessing = false;
        }
    });
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(new Date(date));
};

const formatDateTime = (date) => {
    if (!date) return '-';
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(date));
};

const getTenantDomain = (domains) => {
    if (!domains || !domains.length) return 'Nenhum domínio';
    return domains[0]?.domain || domains[0] || 'Nenhum domínio';
};

const getTenantStatus = (item) => {
    return item.status || 'ativo';
};

const getStatusClass = (status) => {
    const classes = {
        'ativo': 'bg-green-100 text-green-800 border-green-200',
        'inativo': 'bg-red-100 text-red-800 border-red-200',
        'pendente': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'suspenso': 'bg-orange-100 text-orange-800 border-orange-200'
    };
    return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const getStatusLabel = (status) => {
    const labels = {
        'ativo': 'Ativo',
        'inativo': 'Inativo',
        'pendente': 'Pendente',
        'suspenso': 'Suspenso'
    };
    return labels[status] || status;
};

const getInitials = (name) => {
    if (!name) return 'T';
    return name.charAt(0).toUpperCase();
};

const navigateTo = (routeName, params = {}) => {
    router.visit(route(routeName, params));
};
</script>

<template>
    <CentralAdminLayout>
        <div class="flex items-center justify-between w-full pl-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 uppercase tracking-wide">
                    Gerenciar Páginas
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    <!-- ✅ ALTERADO: props.tenant → props.tenants -->
                    {{ hasTenants ? `${props.tenants.total} tenant(s) cadastrado(s)` : 'Nenhum tenant cadastrado' }}
                </p>
            </div>
            <div v-if="canManage"
                class="flex items-center gap-2 text-xs text-cyan-600 bg-cyan-50 px-3 py-1 rounded-full">
                <ShieldAlert class="w-4 h-4" />
                <span>Modo Administrador</span>
            </div>
        </div>

        <div class="py-6">
            <!-- Flash Messages -->
            <div v-if="flashMessage" :class="[
                'mb-4 p-4 rounded-lg text-sm font-medium',
                flashType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'
            ]">
                {{ flashMessage }}
            </div>

            <div class="mx-auto sm:px-6 lg:px-4 space-y-4">
                <!-- Barra de ferramentas -->
                <div
                    class="flex flex-col lg:flex-row gap-3 justify-between items-start lg:items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <!-- Campo de busca -->
                        <div class="relative w-full sm:w-80">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Search class="h-5 w-5 text-gray-400" />
                            </div>
                            <input ref="searchInput" v-model="search" type="text"
                                placeholder="Buscar por ID ou domínio..."
                                class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm transition-shadow"
                                @keyup.esc="clearSearch" />
                            <button v-if="hasSearch" @click="clearSearch"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <!-- Badge de filtros ativos -->
                        <button v-if="hasActiveFilters" @click="clearSearch"
                            class="flex items-center gap-1 px-3 py-1 text-xs font-medium text-cyan-700 bg-cyan-100 rounded-full hover:bg-cyan-200 transition-colors">
                            <X class="w-3 h-3" />
                            Limpar filtros
                        </button>
                    </div>

                    <Button v-if="can.create"
                        class="flex items-center gap-2 rounded-xl bg-cyan-500 hover:bg-cyan-600 px-5 py-2.5 text-white font-semibold shadow-md transition-all hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]"
                        @click="navigateTo('pagina.create')">
                        <Plus class="w-4 h-4" />
                        Adicionar Página
                    </Button>
                    <div v-else
                        class="flex items-center gap-2 px-5 py-2.5 text-gray-400 text-sm bg-gray-100 rounded-xl cursor-not-allowed"
                        title="Você não tem permissão para criar páginas">
                        <ShieldAlert class="w-4 h-4" />
                        Sem permissão
                    </div>
                </div>

                <!-- Indicador de busca -->
                <div v-if="hasActiveFilters && hasTenants"
                    class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">
                    <Search class="w-4 h-4 text-cyan-600" />
                    <span>
                        <!-- ✅ ALTERADO: props.tenant → props.tenants -->
                        Mostrando {{ props.tenants.total }} resultado(s)
                        <template v-if="search">
                            para "<span class="font-semibold text-cyan-700">{{ search }}</span>"
                        </template>
                    </span>
                </div>

                <!-- Tabela -->
                <div class="border rounded-xl border-gray-200 bg-white shadow-sm overflow-hidden">
                    <!-- ✅ REMOVIDO: <pre>{{ tenantList }}</pre> -->

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500 tracking-wider">
                                        ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500 tracking-wider">
                                        Tenant</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500 tracking-wider">
                                        Domínio</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500 tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500 tracking-wider">
                                        Banco de Dados</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500 tracking-wider">
                                        Criado em</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500 tracking-wider">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in tenantList" :key="item.id"
                                    class="hover:bg-gray-50 transition-colors group">
                                    <!-- ID -->
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 font-mono">
                                        #{{ item.id }}
                                    </td>
                                    <!-- Tenant -->
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-sm font-bold">
                                                {{ getInitials(item.id) }}
                                            </div>
                                            <div>
                                                <div
                                                    class="font-medium text-gray-900 group-hover:text-cyan-600 transition-colors">
                                                    {{ item.id }}
                                                </div>
                                                <div v-if="item.data?.name"
                                                    class="text-xs text-gray-500 truncate max-w-xs mt-0.5">
                                                    {{ item.data.name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Domínio -->
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <div class="flex items-center gap-1.5">
                                            <Globe class="w-3.5 h-3.5 text-gray-400" />
                                            <span class="text-gray-600 font-mono text-xs">
                                                {{ getTenantDomain(item.domains) }}
                                            </span>
                                        </div>
                                    </td>
                                    <!-- Status -->
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span
                                            :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border', getStatusClass(getTenantStatus(item))]">
                                            {{ getStatusLabel(getTenantStatus(item)) }}
                                        </span>
                                    </td>
                                    <!-- Banco de Dados -->
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <div class="flex items-center gap-1.5">
                                            <Database class="w-3.5 h-3.5 text-gray-400" />
                                            <span class="text-gray-600 font-mono text-xs">
                                                {{ item.data?.database || 'tenant_' + item.id }}
                                            </span>
                                        </div>
                                    </td>
                                    <!-- Criado em -->
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        <time :title="formatDateTime(item.created_at)">
                                            {{ formatDate(item.created_at) }}
                                        </time>
                                    </td>
                                    <!-- Ações -->
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <div class="flex justify-end gap-1">
                                            <!-- Visualizar -->
                                            <button @click="navigateTo('pagina.show', item.id)"
                                                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all"
                                                title="Visualizar">
                                                <Building2 class="w-4 h-4" />
                                            </button>
                                            <!-- Editar -->
                                            <button v-if="can.edit" @click="navigateTo('pagina.edit', item.id)"
                                                class="p-2 text-cyan-600 hover:text-cyan-800 hover:bg-cyan-50 rounded-lg transition-all"
                                                title="Editar">
                                                <Pencil class="w-4 h-4" />
                                            </button>
                                            <span v-else class="p-2 text-gray-300 cursor-not-allowed"
                                                title="Sem permissão para editar">
                                                <Pencil class="w-4 h-4" />
                                            </span>
                                            <!-- Excluir -->
                                            <button v-if="can.delete" @click="openDeleteModal(item)"
                                                class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-all"
                                                title="Excluir">
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                            <span v-else class="p-2 text-gray-300 cursor-not-allowed"
                                                title="Sem permissão para excluir">
                                                <Trash2 class="w-4 h-4" />
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Estado vazio -->
                    <div v-if="!hasTenants" class="text-center py-16 text-gray-500">
                        <div v-if="hasActiveFilters" class="space-y-3">
                            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center">
                                <Search class="w-8 h-8 text-gray-400" />
                            </div>
                            <p class="text-lg font-medium text-gray-900">Nenhum resultado encontrado</p>
                            <p class="text-sm text-gray-500 max-w-sm mx-auto">
                                Não encontramos tenants para "<span class="font-medium">{{ search }}</span>"
                            </p>
                            <Button variant="outline" size="sm" @click="clearSearch" class="mt-2">
                                <X class="w-4 h-4 mr-1" />
                                Limpar filtros
                            </Button>
                        </div>
                        <div v-else class="space-y-3">
                            <div class="w-16 h-16 mx-auto bg-cyan-50 rounded-full flex items-center justify-center">
                                <Building2 class="w-8 h-8 text-cyan-500" />
                            </div>
                            <p class="text-lg font-medium text-gray-900">Nenhum tenant cadastrado</p>
                            <p class="text-sm text-gray-500">Comece adicionando o primeiro tenant ao sistema</p>
                        </div>
                    </div>

                    <!-- ✅ PAGINAÇÃO CORRIGIDA -->
                    <div v-if="hasTenants && paginationLinks.length > 3"
                        class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50 gap-4">
                        <div class="text-sm text-gray-500">
                            <!-- ✅ ALTERADO: props.tenant → props.tenants -->
                            Mostrando <span class="font-medium">{{ props.tenants.from || 0 }}</span> a
                            <span class="font-medium">{{ props.tenants.to || 0 }}</span> de
                            <span class="font-medium">{{ props.tenants.total || 0 }}</span> tenants
                        </div>
                        <div class="flex gap-1">
                            <template v-for="(link, index) in paginationLinks" :key="index">
                                <button v-if="link.url" @click="router.visit(link.url, { preserveScroll: true })"
                                    :class="[
                                        'px-3 py-1.5 rounded-lg text-sm font-medium transition-all',
                                        link.active ? 'bg-cyan-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                                    ]" v-html="link.label" :disabled="link.active" />
                                <span v-else
                                    class="px-3 py-1.5 rounded-lg text-sm text-gray-400 bg-gray-100 border border-gray-200 cursor-default"
                                    v-html="link.label" />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação -->
        <ConfirmDeleteModal :show="deleteModal.show" :item-name="deleteModal.tenant?.id" title="Excluir Tenant"
            message="Tem certeza que deseja excluir este tenant?"
            warning-message="Todos os dados associados serão permanentemente removidos. Esta ação não pode ser desfeita."
            confirm-text="Sim, Excluir" cancel-text="Cancelar" :is-processing="deleteModal.isProcessing"
            variant="danger" @close="closeDeleteModal" @confirm="confirmDelete" />
    </CentralAdminLayout>
</template>
