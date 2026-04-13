<!-- resources/js/Components/ColorPicker.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Vue3ColorPicker } from '@cyhnkckali/vue3-color-picker';
import '@cyhnkckali/vue3-color-picker/dist/style.css';
import { Palette, Check } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: String,
        default: '#22d3ee'
    },
    label: {
        type: String,
        default: 'Cor'
    },
    description: {
        type: String,
        default: ''
    },
    mode: {
        type: String,
        default: 'solid' // 'solid' | 'gradient'
    },
    showAlpha: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue']);

const showPicker = ref(false);
const pickerRef = ref(null);

const pickerColor = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value.toUpperCase())
});

const handleClickOutside = (event) => {
    if (pickerRef.value && !pickerRef.value.contains(event.target)) {
        showPicker.value = false;
    }
};

const togglePicker = () => {
    showPicker.value = !showPicker.value;
    if (showPicker.value) {
        setTimeout(() => document.addEventListener('click', handleClickOutside, { once: true }), 100);
    }
};

// const presets = [
//     { name: 'Ciano', value: '#22d3ee' },
//     { name: 'Azul', value: '#3B82F6' },
//     { name: 'Verde', value: '#10B981' },
//     { name: 'Roxo', value: '#8B5CF6' },
//     { name: 'Rosa', value: '#EC4899' },
//     { name: 'Laranja', value: '#F97316' },
//     { name: 'Vermelho', value: '#EF4444' },
//     { name: 'Amarelo', value: '#EAB308' },
//     { name: 'Cinza', value: '#6B7280' },
//     { name: 'Preto', value: '#111827' },
// ];
const presets = [];
const applyPreset = (color) => {
    pickerColor.value = color;
};
</script>

<template>
    <div class="space-y-3 relative">
        <!-- Label -->
        <div class="flex items-center gap-2">
            <Palette class="w-4 h-4 text-gray-500" />
            <span class="text-gray-700 font-medium text-sm">{{ label }}</span>
            <span v-if="description" class="text-xs text-gray-400">({{ description }})</span>
        </div>

        <!-- Input + Preview -->
        <div class="flex items-center gap-3">
            <button @click="togglePicker" type="button"
                class="w-12 h-10 rounded-lg border border-gray-300 shadow-sm hover:shadow-md transition-all relative overflow-hidden focus:outline-none focus:ring-2 focus:ring-cyan-500"
                :style="{ backgroundColor: modelValue }">
                <div class="absolute inset-0 bg-black/5 hover:bg-transparent transition-colors"></div>
            </button>

            <div class="flex-1 relative">
                <!-- <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-mono text-sm">#</span> -->
                <input type="text" v-model="pickerColor" placeholder="22d3ee" maxlength="7"
                    class="w-full pl-2 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 font-mono uppercase text-sm" />
            </div>
        </div>

        <!-- Popover do ColorPicker -->
        <div v-if="showPicker" ref="pickerRef"
            class="absolute z-50 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 p-3">
            <Vue3ColorPicker v-model="pickerColor" :mode="mode" :type="showAlpha ? 'RGBA' : 'HEX'"
                :showColorList="false" :showEyeDrop="false" :showAlpha="showAlpha" theme="light" />
        </div>

        <!-- Presets rápidos -->
        <div class="flex flex-wrap ">
            <button v-for="preset in presets" :key="preset.value" @click="applyPreset(preset.value)" type="button"
                class="w-6 h-6 rounded-full border-2 border-transparent hover:scale-110 transition-all focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-cyan-500"
                :class="{ 'ring-2 ring-offset-1 ring-cyan-500': modelValue.toUpperCase() === preset.value.toUpperCase() }"
                :style="{ backgroundColor: preset.value }" :title="preset.name">
                <Check v-if="modelValue.toUpperCase() === preset.value.toUpperCase()"
                    class="w-3 h-3 text-white absolute inset-0 m-auto drop-shadow-md" />
            </button>
        </div>
    </div>
</template>
