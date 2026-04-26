<!-- components/PasswordInput.vue -->
<script setup>
import { ref } from 'vue'
import { Lock, Eye, EyeOff } from 'lucide-vue-next'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    id: {
        type: String,
        default: 'password'
    },
    label: {
        type: String,
        default: 'Senha'
    },
    placeholder: {
        type: String,
        default: 'Digite sua senha'
    },
    error: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    inputClass: {
        type: String,
        default: 'w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors'
    },
    inputErrorClass: {
        type: String,
        default: 'w-full rounded-lg border border-red-300 bg-white px-4 py-2.5 text-gray-900 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-colors'
    }
})

const emit = defineEmits(['update:modelValue'])

const showPassword = ref(false)

const updateValue = (event) => {
    emit('update:modelValue', event.target.value)
}
</script>

<template>
    <div>
        <Label :for="id" class="text-gray-700 pb-2 font-medium flex items-center gap-2">
            <Lock class="w-4 h-4" />
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </Label>
        <div class="relative">
            <input :id="id" :value="modelValue" @input="updateValue" :type="showPassword ? 'text' : 'password'"
                :placeholder="placeholder" :class="[error ? inputErrorClass : inputClass, 'pr-10']"
                :disabled="disabled" />
            <button type="button" @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                <Eye v-if="!showPassword" class="w-5 h-5" />
                <EyeOff v-else class="w-5 h-5" />
            </button>
        </div>
        <p v-if="error" class="mt-2 text-sm text-red-600">
            {{ error }}
        </p>
    </div>
</template>
