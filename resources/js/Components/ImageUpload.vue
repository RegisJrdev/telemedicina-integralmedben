<script setup>
import { ref, computed, watch } from 'vue';
import {
    Upload,
    X,
    Image as ImageIcon,
    AlertCircle,
    AlignLeft,
    AlignCenter,
    AlignRight
} from 'lucide-vue-next';
import { Button } from '@/Components/ui/button';
const props = defineProps({
    modelValue: {
        type: [String, File, null],
        default: null
    },
    posicao: {
        type: String,
        default: 'centro'
    },
    label: {
        type: String,
        default: 'Imagem/Logo'
    },
    description: {
        type: String,
        default: 'Arraste uma imagem ou clique para selecionar'
    },
    accept: {
        type: String,
        default: 'image/*'
    },
    maxSize: {
        type: Number,
        default: 2 * 1024 * 1024
    },
    previewUrl: {
        type: String,
        default: null
    },
    showPosicaoSelector: {
        type: Boolean,
        default: true
    }
});
const emit = defineEmits(['update:modelValue', 'update:posicao', 'error']);
const inputRef = ref(null);
const isDragging = ref(false);
const error = ref(null);
const objectUrl = ref(null);
const preview = computed(() => {
    if (props.modelValue instanceof File) {
        if (objectUrl.value) {
            URL.revokeObjectURL(objectUrl.value);
        }
        objectUrl.value = URL.createObjectURL(props.modelValue);
        return objectUrl.value;
    }
    if (props.previewUrl) {
        return props.previewUrl;
    }
    if (typeof props.modelValue === 'string') {
        return props.modelValue;
    }
    return null;
});
const hasImage = computed(() => !!preview.value);
const posicoes = [
    { value: 'esquerda', label: 'Esquerda', icon: AlignLeft },
    { value: 'centro', label: 'Centro', icon: AlignCenter },
    { value: 'direita', label: 'Direita', icon: AlignRight },
];
const handleFileSelect = (file) => {
    error.value = null;
    if (!file) return;
    if (!file.type.startsWith('image/')) {
        error.value = 'O arquivo deve ser uma imagem';
        emit('error', error.value);
        return;
    }
    if (file.size > props.maxSize) {
        const maxSizeMB = (props.maxSize / 1024 / 1024).toFixed(1);
        error.value = `Imagem muito grande. Máximo: ${maxSizeMB}MB`;
        emit('error', error.value);
        return;
    }
    emit('update:modelValue', file);
};
const onFileChange = (event) => {
    const file = event.target.files?.[0];
    handleFileSelect(file);
    event.target.value = '';
};
const onDrop = (event) => {
    isDragging.value = false;
    const file = event.dataTransfer.files?.[0];
    handleFileSelect(file);
};
const onDragOver = (event) => {
    event.preventDefault();
    isDragging.value = true;
};
const onDragLeave = () => {
    isDragging.value = false;
};
const removeImage = () => {
    emit('update:modelValue', null);
    error.value = null;
    if (objectUrl.value) {
        URL.revokeObjectURL(objectUrl.value);
        objectUrl.value = null;
    }
};
const triggerFileInput = () => {
    inputRef.value?.click();
};
const selecionarPosicao = (novaPosicao) => {
    emit('update:posicao', novaPosicao);
};
watch(() => props.modelValue, (newVal, oldVal) => {
    if (oldVal instanceof File && !(newVal instanceof File)) {
        if (objectUrl.value) {
            URL.revokeObjectURL(objectUrl.value);
            objectUrl.value = null;
        }
    }
});
</script>
<template>
    <div class="space-y-4">
        <!-- Upload Area -->
        <div class="space-y-2">

            <label class="text-sm font-medium text-gray-700">
                {{ label }}
                <span class="text-gray-400 font-normal">(opcional)</span>
            </label>
            <!-- Sem imagem - Drop zone -->
            <div v-if="!hasImage" @click="triggerFileInput" @drop.prevent="onDrop" @dragover.prevent="onDragOver"
                @dragleave="onDragLeave" :class="[
                    'relative border-2 border-dashed rounded-lg p-6 cursor-pointer transition-all',
                    isDragging
                        ? 'border-cyan-500 bg-cyan-50'
                        : 'border-gray-300 hover:border-gray-400 hover:bg-gray-50'
                ]">
                <input ref="inputRef" type="file" :accept="accept" class="hidden" @change="onFileChange" />
                <div class="flex flex-col items-center text-center space-y-2">
                    <div :class="[
                        'p-3 rounded-full transition-colors',
                        isDragging ? 'bg-cyan-100 text-cyan-600' : 'bg-gray-100 text-gray-400'
                    ]">
                        <Upload class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">
                            {{ description }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            PNG, JPG, GIF até {{ (maxSize / 1024 / 1024).toFixed(0) }}MB
                        </p>
                    </div>
                </div>
            </div>
            <!-- Com imagem - Preview -->
            <div v-else class="relative group">
                <div class="relative rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                    <img :src="preview" alt="Preview" class="w-full h-48 object-contain" />
                    <div
                        class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <Button type="button" variant="secondary" size="sm" @click="triggerFileInput" class="gap-2">
                            <ImageIcon class="w-4 h-4" />
                            Alterar
                        </Button>
                        <Button type="button" variant="destructive" size="sm" @click="removeImage" class="gap-2">
                            <X class="w-4 h-4" />
                            Remover
                        </Button>
                    </div>
                </div>
                <input ref="inputRef" type="file" :accept="accept" class="hidden" @change="onFileChange" />
            </div>
            <!-- Erro -->
            <div v-if="error" class="flex items-center gap-2 text-sm text-red-600">
                <AlertCircle class="w-4 h-4" />
                <span>{{ error }}</span>
            </div>
        </div>
        <!-- Seletor de Posição (aparece quando tem imagem) -->
        <div v-if="hasImage && showPosicaoSelector" class="space-y-2">
            <label class="text-sm font-medium text-gray-700 flex items-center gap-2">
                <AlignCenter class="w-4 h-4" />
                Posição do Logo
            </label>
            <div class="grid grid-cols-3 gap-2">
                <button v-for="pos in posicoes" :key="pos.value" type="button" @click="selecionarPosicao(pos.value)"
                    :class="[
                        'py-2 px-3 rounded-md border text-sm font-medium transition-all flex items-center justify-center gap-2',
                        posicao === pos.value
                            ? 'border-cyan-500 bg-cyan-50 text-cyan-700 shadow-sm'
                            : 'border-gray-300 hover:border-gray-400 text-gray-700 hover:bg-gray-50'
                    ]">
                    <component :is="pos.icon" class="w-4 h-4" />
                    {{ pos.label }}
                </button>
            </div>
        </div>
    </div>
</template>
