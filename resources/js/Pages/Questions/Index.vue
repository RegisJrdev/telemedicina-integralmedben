<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import QuestionDialog from '@/Components/QuestionDialog.vue';
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Trash2, Plus, Eye } from 'lucide-vue-next';
import { ref } from 'vue';
import Button from '@/Components/ui/button/Button.vue';

const props = defineProps({
    questions: {
        type: Object,
        required: true
    }
});

const openDialog = ref(false);
const selectedQuestion = ref(null);

const openCreateDialog = () => {
    selectedQuestion.value = null;
    openDialog.value = true;
};

const openEditDialog = (question) => {
    selectedQuestion.value = question;
    openDialog.value = true;
};

const deleteQuestion = (id) => {
    if (confirm('Tem certeza que deseja deletar esta pergunta?')) {
        router.delete(route('questions.destroy', id));
    }
};

const formatType = (type) => {
    const types = {
        'text': 'Texto',
        'email': 'Email',
        'number': 'Número',
        'tel': 'Telefone',
        'date': 'Data'
    };
    return types[type] || type;
};
</script>

<template>
    <Head title="Perguntas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 uppercase">
                Gerenciar Perguntas
            </h2>
        </template>

        <div class="">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-end">
                    <Button
                        class="flex items-center gap-2 mb-4 rounded-xl bg-cyan-500 hover:bg-cyan-600 px-5 py-2.5 text-white font-semibold shadow-md transition-colors duration-300 ease-in-out"
                        @click="openCreateDialog"
                    >
                        <Plus class="w-4 h-4" />
                        Nova Pergunta
                    </Button>
                </div>

                <QuestionDialog
                    v-model:open="openDialog"
                    :question="selectedQuestion"
                />

                <div class="border rounded-xl border-gray-200 max-h-[800px] overflow-auto">
                    <div class="bg-white">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Título
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Tipo
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="question in questions.data" :key="question.id" class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            #{{ question.id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ question.title }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">
                                                {{ formatType(question.type) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <button
                                                    @click="router.visit(route('questions.show', question.id))"
                                                    class="text-gray-600 hover:text-gray-900 transition-colors"
                                                    title="Visualizar"
                                                >
                                                    <Eye class="w-4 h-4" />
                                                </button>
                                                <button
                                                    @click="openEditDialog(question)"
                                                    class="text-gray-900 hover:text-cyan-900 transition-colors"
                                                    title="Editar"
                                                >
                                                    <Pencil class="w-4 h-4" />
                                                </button>
                                                <button
                                                    @click="deleteQuestion(question.id)"
                                                    class="text-red-600 hover:text-red-900 transition-colors"
                                                    title="Deletar"
                                                >
                                                    <Trash2 class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="questions.data.length === 0" class="text-center py-12 text-gray-500">
                            Nenhuma pergunta cadastrada ainda.
                        </div>

                        <div v-if="questions.links && questions.data.length > 0" class="flex justify-center gap-2 p-4 border-t border-gray-200">
                            <template v-for="link in questions.links" :key="link.label">
                                <button
                                    v-if="link.url"
                                    @click="router.visit(link.url)"
                                    :class="[
                                        'rounded px-3 py-1 text-sm font-medium transition-colors',
                                        link.active
                                            ? 'bg-cyan-600 text-white'
                                            : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
                                    ]"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="rounded bg-gray-100 px-3 py-1 text-sm text-gray-400"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
