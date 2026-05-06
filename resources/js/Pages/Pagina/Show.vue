<script setup>
import CentralAdminLayout from '@/Layouts/CentralAdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import {
    ArrowLeft,
    Building2,
    Globe,
    User,
    Calendar,
    ShieldCheck,
    Database,
    Pencil,
    ExternalLink,
    Copy,
    FileText,
    Clock,
} from 'lucide-vue-next'

import { computed } from 'vue'
import Button from '@/Components/ui/button/Button.vue'
import PomponeteLink from '@/Components/PomponeteLink.vue'

const props = defineProps({
    tenant: {
        type: Object,
        required: true,
    },
})

const detail = computed(() => props.tenant?.details?.[0] || null)
const user = computed(() => detail.value?.user || null)
const forms = computed(() => props.tenant?.forms || [])
const domains = computed(() => props.tenant?.domains || [])

const tenantName = computed(() => {
    return detail.value?.descricao || detail.value?.slug || props.tenant?.id || 'Tenant'
})

const tenantSlug = computed(() => {
    return detail.value?.slug || props.tenant?.id || '-'
})

const formatDateTime = (date) => {
    if (!date) return '-'

    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(date))
}

const copyToClipboard = async (text) => {
    if (!text) return

    await navigator.clipboard.writeText(text)
    alert('Link copiado com sucesso!')
}
</script>

<template>
    <CentralAdminLayout>
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-cyan-100 flex items-center justify-center">
                            <Building2 class="w-8 h-8 text-cyan-600" />
                        </div>

                        <div>


                            <h1 class="text-3xl font-bold text-gray-900">
                                {{ tenantName }}
                            </h1>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ tenant.tenant_domain || 'Sem domínio vinculado' }}
                            </p>

                            <div class="flex flex-wrap gap-2 mt-4">
                                <div class="badge badge-success badge-outline gap-1 px-3 py-3">
                                    <ShieldCheck class="w-3 h-3" />
                                    Ativo
                                </div>

                                <div class="badge badge-info badge-outline gap-1 px-3 py-3">
                                    <Database class="w-3 h-3" />
                                    ID: {{ tenant.id }}
                                </div>

                                <div class="badge badge-outline gap-1 px-3 py-3">
                                    SMS: {{ tenant.sms_quota ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <Button class="bg-cyan-500 hover:bg-cyan-600 border-none text-white"
                        @click="router.visit(route('pagina.edit', tenant.id))">
                        <Pencil class="w-4 h-4 mr-2" />
                        Editar
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Principal -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Informações do Tenant -->
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-semibold flex items-center gap-2">
                                <Building2 class="w-5 h-5 text-cyan-500" />
                                Informações do Tenant
                            </h2>
                        </div>

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    Nome/Descrição
                                </label>
                                <div class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50">
                                    {{ tenantName }}
                                </div>
                            </div>

                            <div>
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    Slug
                                </label>
                                <div class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50">
                                    {{ tenantSlug }}
                                </div>
                            </div>

                            <div>
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    Banco do Tenant
                                </label>
                                <div class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50">
                                    {{ tenant.tenancy_db_name || '-' }}
                                </div>
                            </div>

                            <div>
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    Domínio Principal
                                </label>
                                <div class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50">
                                    {{ tenant.tenant_domain || '-' }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    URL
                                </label>

                                <div
                                    class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-3">
                                    <PomponeteLink :url="tenant.url" :label="tenant.url" />

                                    <div class="flex gap-2">
                                        <button class="btn btn-sm btn-ghost" @click="copyToClipboard(tenant.url)">
                                            <Copy class="w-4 h-4" />
                                        </button>

                                        <a :href="tenant.url" target="_blank" class="btn btn-sm btn-ghost">
                                            <ExternalLink class="w-4 h-4" />
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    Criado em
                                </label>
                                <div
                                    class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50 flex items-center gap-2">
                                    <Calendar class="w-4 h-4 text-cyan-500" />
                                    {{ formatDateTime(tenant.created_at) }}
                                </div>
                            </div>

                            <div>
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    Atualizado em
                                </label>
                                <div
                                    class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50 flex items-center gap-2">
                                    <Calendar class="w-4 h-4 text-cyan-500" />
                                    {{ formatDateTime(tenant.updated_at) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dados Complementares -->
                    <div v-if="detail" class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-semibold flex items-center gap-2">
                                <Database class="w-5 h-5 text-cyan-500" />
                                Dados Complementares
                            </h2>
                        </div>

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    Código
                                </label>
                                <div class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50">
                                    {{ detail.code || '-' }}
                                </div>
                            </div>

                            <div>
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    Sigla
                                </label>
                                <div class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50">
                                    {{ detail.sigla || '-' }}
                                </div>
                            </div>

                            <div>
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    Path Arquivos
                                </label>
                                <div class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50">
                                    {{ detail.path_arquivos || '-' }}
                                </div>
                            </div>

                            <div>
                                <label class="text-xs uppercase tracking-wide text-gray-500">
                                    Cores
                                </label>
                                <div class="mt-1 p-3 rounded-xl border border-gray-200 bg-gray-50">
                                    Primária: {{ detail.cor_primaria || '-' }} /
                                    Secundária: {{ detail.cor_secundaria || '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulários vinculados -->
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="text-lg font-semibold flex items-center gap-2">
                                <FileText class="w-5 h-5 text-cyan-500" />
                                Formulários Vinculados
                            </h2>

                            <span class="badge badge-info badge-outline">
                                {{ forms.length }} formulário(s)
                            </span>
                        </div>

                        <div class="p-6">
                            <div v-if="forms.length" class="space-y-4">
                                <div v-for="form in forms" :key="form.id"
                                    class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition">
                                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">
                                                {{ form.title }}
                                            </h3>

                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ form.description || 'Sem descrição' }}
                                            </p>

                                            <div class="flex flex-wrap gap-2 mt-3">
                                                <span class="badge badge-success badge-outline">
                                                    {{ form.status }}
                                                </span>

                                                <span class="badge badge-outline">
                                                    Respostas: {{ form.responses_count ?? 0 }}
                                                </span>

                                                <span class="badge badge-outline">
                                                    Público: {{ form.is_public ? 'Sim' : 'Não' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="text-sm text-gray-500 md:text-right">
                                            <div class="flex items-center md:justify-end gap-1">
                                                <Clock class="w-4 h-4" />
                                                Expira: {{ form.expires_at_br || formatDateTime(form.expires_at) }}
                                            </div>

                                            <div class="mt-1">
                                                Criado: {{ form.created_at_br || formatDateTime(form.created_at) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-10 text-gray-500">
                                Nenhum formulário vinculado.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">

                    <!-- Responsável -->
                    <div v-if="user" class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-semibold flex items-center gap-2">
                                <User class="w-5 h-5 text-cyan-500" />
                                Responsável
                            </h2>
                        </div>

                        <div class="p-6 text-center">
                            <div class="avatar placeholder mb-4">
                                <div class="bg-cyan-500 text-white rounded-full w-20">
                                    <span class="text-2xl font-bold">
                                        {{ user.name?.charAt(0) }}
                                    </span>
                                </div>
                            </div>

                            <h3 class="font-bold text-lg text-gray-900">
                                {{ user.name }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ user.email }}
                            </p>
                        </div>
                    </div>

                    <!-- Domínios -->
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-semibold flex items-center gap-2">
                                <Globe class="w-5 h-5 text-cyan-500" />
                                Domínios
                            </h2>
                        </div>

                        <div class="p-6 space-y-3">
                            <div v-for="domain in domains" :key="domain.id"
                                class="p-3 rounded-xl bg-gray-50 border border-gray-200">
                                {{ domain.domain }}
                            </div>

                            <div v-if="!domains.length" class="text-sm text-gray-500">
                                Nenhum domínio cadastrado.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CentralAdminLayout>
</template>
