<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle, XCircle } from 'lucide-vue-next';

const props = defineProps({
    question: {
        type: Object,
        required: true
    }
});

const formatType = (type) => {
    const types = {
        'text': 'Texto',
        'email': 'Email',
        'number': 'Número',
        'tel': 'Telefone',
        'date': 'Data',
        'option': 'Seleção (Dropdown)'
    };
    return types[type] || type;
};
</script>

<template>
    <Head title="Detalhes da Pergunta" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('dashboard')"
                    class="text-gray-600 hover:text-gray-900"
                >
                    <ArrowLeft class="w-5 h-5" />
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detalhes da Pergunta
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dl class="space-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">#{{ question.id }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Título</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">{{ question.title }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo de Campo</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1.5 text-sm font-semibold text-blue-800">
                                        {{ formatType(question.type) }}
                                    </span>
                                </dd>
                            </div>

                            <div v-if="question.type === 'option' && question.options && question.options.length > 0">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Opções</dt>
                                <dd class="mt-2">
                                    <div class="grid gap-2">
                                        <div
                                            v-for="(option, index) in question.options"
                                            :key="option.id"
                                            class="flex items-center gap-3 rounded-md border border-gray-200 bg-gray-50 px-4 py-3 hover:bg-gray-100 transition-colors"
                                        >
                                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-cyan-100 text-cyan-700 text-xs font-bold">
                                                {{ index + 1 }}
                                            </span>
                                            <span class="text-gray-900 font-medium">{{ option.label }}</span>
                                            <span class="ml-auto text-xs text-gray-500 font-mono bg-gray-200 px-2.5 py-1 rounded">
                                                {{ option.value }}
                                            </span>
                                        </div>
                                    </div>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span :class="[
                                        'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold',
                                        question.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    ]">
                                        <component :is="question.is_active ? CheckCircle : XCircle" class="w-4 h-4" />
                                        {{ question.is_active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </dd>
                            </div>

                            <div v-if="question.tenants && question.tenants.length > 0">
                                <dt class="text-sm font-medium text-gray-500">Vinculada aos Tenants</dt>
                                <dd class="mt-2">
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="tenant in question.tenants"
                                            :key="tenant.id"
                                            class="inline-flex items-center rounded-md bg-cyan-100 px-3 py-1.5 text-sm font-medium text-cyan-800"
                                        >
                                            {{ tenant.name || tenant.id }}
                                        </span>
                                    </div>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Criada em</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ new Date(question.created_at).toLocaleString('pt-BR') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última atualização</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ new Date(question.updated_at).toLocaleString('pt-BR') }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6">
                            <Link
                                :href="route('dashboard')"
                                class="inline-flex items-center gap-2 rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition-colors"
                            >
                                <ArrowLeft class="w-4 h-4" />
                                Voltar ao Dashboard
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
