<script setup>
import { Pencil, Trash2, Eye, Globe, Lock, FileText, Calendar, User } from "lucide-vue-next";
import { router } from "@inertiajs/vue3";
import Table from "./ui/table/Table.vue";
import TableHeader from "./ui/table/TableHeader.vue";
import TableRow from "./ui/table/TableRow.vue";
import TableHead from "./ui/table/TableHead.vue";
import TableBody from "./ui/table/TableBody.vue";
import TableCell from "./ui/table/TableCell.vue";

const props = defineProps({
    forms: {
        type: Object,
        required: true,
        default: () => ({ data: [] }),
    },
    can: {
        type: Object,
        default: () => ({ edit: false, delete: false }),
    },
});

const emit = defineEmits(["edit-form", "view-form"]);

const editForm = (form) => {
    emit("edit-form", form);
};

const viewForm = (form) => {
    emit("view-form", form);
};

const deleteForm = (id) => {
    if (confirm("Tem certeza que deseja excluir este formulário? Esta ação não pode ser desfeita.")) {
        router.delete(route("forms.destroy", id));
    }
};

// Formatar status com cores
const formatStatus = (status) => {
    const statuses = {
        rascunho: { label: "Rascunho", class: "bg-gray-100 text-gray-800" },
        ativo: { label: "Ativo", class: "bg-green-100 text-green-800" },
        pausado: { label: "Pausado", class: "bg-yellow-100 text-yellow-800" },
        encerrado: { label: "Encerrado", class: "bg-red-100 text-red-800" },
    };
    return statuses[status] || { label: status, class: "bg-gray-100 text-gray-800" };
};

// Formatar data
const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};

// Truncar texto
const truncate = (text, length = 50) => {
    if (!text) return "-";
    return text.length > length ? text.substring(0, length) + "..." : text;
};
</script>

<template>
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead class="w-[80px] text-center">Código</TableHead>
                <TableHead>Título</TableHead>
                <TableHead class="w-[100px] text-center">Status</TableHead>
                <TableHead class="w-[100px] text-center">Visibilidade</TableHead>
                <TableHead class="w-[120px] text-center">Autor</TableHead>
                <TableHead class="w-[100px] text-center">Respostas</TableHead>
                <TableHead class="w-[120px] text-center">Atualizado</TableHead>
                <TableHead class="w-[140px] text-center">Ações</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="form in forms.data" :key="form.id">
                <!-- Código -->
                <TableCell class="font-medium text-center text-xs text-gray-500">
                    {{ form.code ? form.code.substring(0, 8) + "..." : `#${form.id}` }}
                </TableCell>

                <!-- Título e Descrição -->
                <TableCell>
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-900">{{ truncate(form.title, 40) }}</span>
                        <span class="text-xs text-gray-500 mt-0.5">{{ truncate(form.description, 60) }}</span>
                    </div>
                </TableCell>

                <!-- Status -->
                <TableCell class="text-center">
                    <span :class="[
                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                        formatStatus(form.status).class
                    ]">
                        {{ formatStatus(form.status).label }}
                    </span>
                </TableCell>

                <!-- Visibilidade -->
                <TableCell class="text-center">
                    <div class="flex items-center justify-center gap-1">
                        <Globe v-if="form.is_public" class="w-4 h-4 text-green-600" title="Público" />
                        <Lock v-else class="w-4 h-4 text-gray-400" title="Privado" />
                        <span class="text-xs text-gray-600">
                            {{ form.is_public ? "Público" : "Privado" }}
                        </span>
                    </div>
                </TableCell>

                <!-- Autor -->
                <TableCell class="text-center">
                    <div v-if="form.user" class="flex items-center justify-center gap-1">
                        <User class="w-3 h-3 text-gray-400" />
                        <span class="text-sm text-gray-700">{{ form.user.name }}</span>
                    </div>
                    <span v-else class="text-sm text-gray-400">-</span>
                </TableCell>

                <!-- Respostas -->
                <TableCell class="text-center">
                    <div class="flex items-center justify-center gap-1">
                        <FileText class="w-3 h-3 text-gray-400" />
                        <span class="text-sm font-medium text-gray-700">
                            {{ form.responses_count || 0 }}
                        </span>
                    </div>
                </TableCell>

                <!-- Data de Atualização -->
                <TableCell class="text-center">
                    <div class="flex items-center justify-center gap-1">
                        <Calendar class="w-3 h-3 text-gray-400" />
                        <span class="text-xs text-gray-600">
                            {{ formatDate(form.updated_at) }}
                        </span>
                    </div>
                </TableCell>

                <!-- Ações -->
                <TableCell class="text-center">
                    <div class="flex justify-center items-center gap-2">
                        <!-- Visualizar -->
                        <button @click="viewForm(form)"
                            class="p-1.5 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors"
                            title="Visualizar">
                            <Eye class="w-4 h-4" />
                        </button>

                        <!-- Editar -->
                        <button v-if="can.edit" @click="editForm(form)"
                            class="p-1.5 rounded-md text-cyan-600 hover:bg-cyan-50 hover:text-cyan-900 transition-colors"
                            title="Editar">
                            <Pencil class="w-4 h-4" />
                        </button>

                        <!-- Excluir -->
                        <button v-if="can.delete" @click="deleteForm(form.id)"
                            class="p-1.5 rounded-md text-red-600 hover:bg-red-50 hover:text-red-900 transition-colors"
                            title="Excluir">
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                </TableCell>
            </TableRow>

            <!-- Estado vazio -->
            <TableRow v-if="!forms.data || forms.data.length === 0">
                <TableCell colspan="8" class="text-center py-8 text-gray-500">
                    Nenhum formulário encontrado.
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
