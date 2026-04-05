<!-- ============================================================
     ConfirmActionModal.vue - VERSÃO OTIMIZADA
     ============================================================

     Melhorias:
     1. Teleport para renderizar no body (evita z-index issues)
     2. Componentes SVG (Lucide) ao invés de strings HTML
     3. Slots para conteúdo personalizado
     4. Gerenciamento de foco e ESC key
     5. Animações suaves com Transition
     ============================================================ -->

<script setup>
import { ref, watch, nextTick } from 'vue';
import {
    HelpCircle,
    CheckCircle,
    AlertTriangle,
    Info,
    X,
    Loader2
} from 'lucide-vue-next';

// ============================================================
// PROPS
// ============================================================

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Confirmar Ação',
    },
    description: {
        type: String,
        default: '',
    },
    confirmText: {
        type: String,
        default: 'Confirmar',
    },
    cancelText: {
        type: String,
        default: 'Cancelar',
    },
    confirmVariant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'success', 'danger', 'warning'].includes(value),
    },
    icon: {
        type: String,
        default: 'question',
        validator: (value) => ['question', 'check', 'warning', 'info'].includes(value),
    },
    isProcessing: {
        type: Boolean,
        default: false,
    },
    processingText: {
        type: String,
        default: 'Processando...',
    },
    closeOnEsc: {
        type: Boolean,
        default: true,
    },
    closeOnBackdrop: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close', 'confirm']);

// ============================================================
// STATE
// ============================================================

const confirmButtonRef = ref(null);
let lastFocusedElement = null;

// ============================================================
// CONFIGURAÇÕES
// ============================================================

const VARIANT_CLASSES = {
    primary: 'bg-cyan-600 hover:bg-cyan-700 focus:ring-cyan-500 text-white',
    success: 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white',
    danger: 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white',
    warning: 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 text-white',
};

const ICON_COMPONENTS = {
    question: HelpCircle,
    check: CheckCircle,
    warning: AlertTriangle,
    info: Info,
};

const ICON_COLORS = {
    question: 'text-cyan-600',
    check: 'text-green-600',
    warning: 'text-yellow-600',
    info: 'text-blue-600',
};

const ICON_BG_COLORS = {
    question: 'bg-cyan-100',
    check: 'bg-green-100',
    warning: 'bg-yellow-100',
    info: 'bg-blue-100',
};

// ============================================================
// HANDLERS
// ============================================================

const handleConfirm = () => {
    if (props.isProcessing) return;
    emit('confirm');
};

const handleClose = () => {
    if (props.isProcessing) return;
    emit('close');
};

const handleBackdropClick = () => {
    if (props.closeOnBackdrop) handleClose();
};

const handleKeydown = (e) => {
    if (e.key === 'Escape' && props.closeOnEsc && !props.isProcessing) {
        handleClose();
    }
};

// ============================================================
// FOCUS MANAGEMENT (Acessibilidade)
// ============================================================

watch(() => props.show, async (isOpen) => {
    if (isOpen) {
        // Salvar elemento focado anteriormente
        lastFocusedElement = document.activeElement;

        // Focar no botão de confirmar quando abrir
        await nextTick();
        confirmButtonRef.value?.focus();

        // Adicionar listener de teclado
        document.addEventListener('keydown', handleKeydown);
        // Bloquear scroll do body
        document.body.style.overflow = 'hidden';
    } else {
        // Remover listener
        document.removeEventListener('keydown', handleKeydown);
        // Restaurar scroll
        document.body.style.overflow = '';
        // Restaurar foco
        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
    }
});
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
                aria-labelledby="modal-title">
                <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                        @click="handleBackdropClick" />

                    <!-- Center trick -->
                    <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">
                        &#8203;
                    </span>

                    <!-- Modal panel -->
                    <Transition enter-active-class="ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-active-class="ease-in duration-200"
                        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <div
                            class="relative inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                            <!-- Botão X (fechar) -->
                            <button v-if="closeOnBackdrop" @click="handleClose" :disabled="isProcessing"
                                class="absolute top-4 right-4 rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                aria-label="Fechar modal">
                                <X class="h-5 w-5" />
                            </button>

                            <!-- Conteúdo -->
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <!-- Ícone -->
                                    <div :class="[
                                        'mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10',
                                        ICON_BG_COLORS[icon]
                                    ]">
                                        <component :is="ICON_COMPONENTS[icon]" :class="['h-6 w-6', ICON_COLORS[icon]]"
                                            aria-hidden="true" />
                                    </div>

                                    <!-- Texto -->
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 id="modal-title" class="text-lg font-medium leading-6 text-gray-900">
                                            <slot name="title">{{ title }}</slot>
                                        </h3>

                                        <div v-if="description || $slots.description" class="mt-2">
                                            <p class="text-sm text-gray-500 whitespace-pre-line">
                                                <slot name="description">{{ description }}</slot>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ações -->
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                                <button ref="confirmButtonRef" type="button" :class="[
                                    'inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:w-auto sm:text-sm transition-all duration-200',
                                    VARIANT_CLASSES[confirmVariant],
                                    isProcessing ? 'opacity-75 cursor-not-allowed' : ''
                                ]" :disabled="isProcessing" @click="handleConfirm">
                                    <Loader2 v-if="isProcessing" class="mr-2 h-4 w-4 animate-spin" />
                                    {{ isProcessing ? processingText : confirmText }}
                                </button>

                                <button type="button"
                                    class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm transition-colors duration-200"
                                    :disabled="isProcessing" @click="handleClose">
                                    {{ cancelText }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
