<script setup>
import { computed, ref, watch } from 'vue'
import { Search, X, Check, CheckSquare, Square, SquareCheck, Trash2 } from 'lucide-vue-next'

const props = defineProps({
    forms: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: Array,
        default: () => [],
    },
    open: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Selecionar formulários'
    },
    description: {
        type: String,
        default: 'Escolha um ou mais formulários para vincular.'
    },
    searchPlaceholder: {
        type: String,
        default: 'Buscar por título ou descrição...'
    },
    emptyMessage: {
        type: String,
        default: 'Nenhum formulário encontrado.'
    },
    confirmText: {
        type: String,
        default: 'Confirmar seleção'
    },
    cancelText: {
        type: String,
        default: 'Cancelar'
    }
})

const emit = defineEmits([
    'update:modelValue',
    'update:open',
    'confirm',
])

const search = ref('')
const selected = ref([])

// Sincroniza com v-model externo
watch(
    () => props.modelValue,
    (value) => {
        selected.value = [...value]
    },
    { immediate: true }
)

// Busca em título E descrição
const filteredForms = computed(() => {
    const term = search.value.toLowerCase().trim()

    if (!term) return props.forms

    return props.forms.filter((form) => {
        const titleMatch = form.title?.toLowerCase().includes(term)
        const descMatch = form.description?.toLowerCase().includes(term)
        return titleMatch || descMatch
    })
})

const isSelected = (formId) => {
    return selected.value.includes(formId)
}

const toggleForm = (formId) => {
    if (isSelected(formId)) {
        selected.value = selected.value.filter((id) => id !== formId)
    } else {
        selected.value.push(formId)
    }
}

// Selecionar todos os visíveis
const selectAllVisible = () => {
    const visibleIds = filteredForms.value.map(f => f.id)
    const newSelection = [...new Set([...selected.value, ...visibleIds])]
    selected.value = newSelection
}

// Deselecionar todos os visíveis
const deselectAllVisible = () => {
    const visibleIds = filteredForms.value.map(f => f.id)
    selected.value = selected.value.filter(id => !visibleIds.includes(id))
}

// Verifica se todos os visíveis estão selecionados
const allVisibleSelected = computed(() => {
    if (filteredForms.value.length === 0) return false
    return filteredForms.value.every(f => isSelected(f.id))
})

// Toggle selecionar/deselecionar todos visíveis
const toggleSelectAll = () => {
    if (allVisibleSelected.value) {
        deselectAllVisible()
    } else {
        selectAllVisible()
    }
}

// Limpar toda a seleção
const clearAll = () => {
    selected.value = []
}

const close = () => {
    // Restaura seleção original ao cancelar
    selected.value = [...props.modelValue]
    emit('update:open', false)
}

const confirm = () => {
    emit('update:modelValue', selected.value)
    emit('confirm', selected.value)
    close()
}

// Fechar ao clicar no backdrop
const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget) {
        close()
    }
}

// Tecla ESC
const handleKeydown = (e) => {
    if (e.key === 'Escape') {
        close()
    }
}
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="open"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                @click="handleBackdropClick" @keydown="handleKeydown">
                <Transition enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4">
                    <div v-if="open" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col"
                        @click.stop>
                        <!-- Header -->
                        <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-100">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    {{ title }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ description }}
                                </p>
                            </div>

                            <button type="button" @click="close"
                                class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <X class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Barra de busca + Ações -->
                        <div class="p-6 pb-0 space-y-3">
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                <input v-model="search" type="text" :placeholder="searchPlaceholder"
                                    class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent text-sm"
                                    autocomplete="off" />
                                <button v-if="search" @click="search = ''"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <X class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="toggleSelectAll"
                                        class="inline-flex items-center gap-1.5 text-sm text-cyan-600 hover:text-cyan-700 font-medium transition-colors">
                                        <SquareCheck v-if="allVisibleSelected" class="w-4 h-4" />
                                        <Square v-else class="w-4 h-4" />
                                        {{ allVisibleSelected ? 'Desmarcar todos' : 'Selecionar todos' }}
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button v-if="selected.length > 0" type="button" @click="clearAll"
                                        class="inline-flex items-center gap-1.5 text-sm text-red-500 hover:text-red-600 font-medium transition-colors">
                                        <Trash2 class="w-4 h-4" />
                                        Limpar
                                    </button>
                                </div>
                                <span class="text-sm text-gray-500 font-medium">
                                    {{ selected.length }} selecionado{{ selected.length !== 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>

                        <!-- Lista de formulários -->
                        <div class="flex-1 overflow-y-auto px-6 py-2">
                            <div v-if="filteredForms.length === 0" class="py-12 text-center">
                                <Search class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                <p class="text-sm text-gray-500 font-medium">
                                    {{ emptyMessage }}
                                </p>
                                <p v-if="search" class="text-xs text-gray-400 mt-1">
                                    Tente buscar com outros termos
                                </p>
                            </div>

                            <div v-else class="space-y-1">
                                <button v-for="form in filteredForms" :key="form.id" type="button"
                                    @click="toggleForm(form.id)" :class="[
                                        'w-full flex items-start gap-3 p-3 rounded-xl text-left transition-all duration-150 border-2',
                                        isSelected(form.id)
                                            ? 'border-cyan-500 bg-cyan-50/50 shadow-sm'
                                            : 'border-transparent hover:bg-gray-50 hover:border-gray-200'
                                    ]">
                                    <!-- Checkbox visual -->
                                    <div :class="[
                                        'mt-0.5 flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition-colors',
                                        isSelected(form.id)
                                            ? 'bg-cyan-500 border-cyan-500'
                                            : 'border-gray-300 bg-white'
                                    ]">
                                        <Check v-if="isSelected(form.id)" class="w-3.5 h-3.5 text-white" />
                                    </div>

                                    <!-- Conteúdo -->
                                    <div class="flex-1 min-w-0">
                                        <p :class="[
                                            'font-medium text-sm',
                                            isSelected(form.id) ? 'text-cyan-900' : 'text-gray-900'
                                        ]">
                                            {{ form.title }}
                                        </p>
                                        <p v-if="form.description" :class="[
                                            'text-xs mt-0.5 line-clamp-2',
                                            isSelected(form.id) ? 'text-cyan-700/70' : 'text-gray-500'
                                        ]">
                                            {{ form.description }}
                                        </p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    <span class="font-semibold text-gray-700">{{ selected.length }}</span>
                                    de {{ props.forms.length }} formulário{{ props.forms.length !== 1 ? 's' : '' }}
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" @click="close"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                        {{ cancelText }}
                                    </button>
                                    <button type="button" @click="confirm" :disabled="selected.length === 0" :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2',
                                        selected.length > 0
                                            ? 'bg-cyan-600 text-white hover:bg-cyan-700 shadow-sm'
                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ]">
                                        <Check class="w-4 h-4" />
                                        {{ confirmText }}
                                        <span v-if="selected.length > 0"
                                            class="bg-cyan-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                                            {{ selected.length }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
