<script setup>
import { computed, nextTick, ref } from 'vue'
import { Search, X } from 'lucide-vue-next'

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Buscar...',
    },
    width: {
        type: String,
        default: 'w-full sm:w-80',
    },
    debounceMs: {
        type: Number,
        default: 300,
    },
    focusColor: {
        type: String,
        default: 'cyan',
    },
    size: {
        type: String,
        default: 'md',
        validator: value => ['sm', 'md', 'lg'].includes(value),
    },
})

const emit = defineEmits(['update:modelValue', 'search', 'clear', 'escape'])

const inputRef = ref(null)
let searchTimer = null

const hasValue = computed(() => props.modelValue.length > 0)

const sizeClasses = computed(() => ({
    sm: 'py-1.5 pl-9 pr-9 text-xs',
    md: 'py-2.5 pl-10 pr-10 text-sm',
    lg: 'py-3 pl-11 pr-11 text-base',
}[props.size]))

const iconSizes = computed(() => ({
    sm: 'h-4 w-4',
    md: 'h-5 w-5',
    lg: 'h-6 w-6',
}[props.size]))

const clearSizes = computed(() => ({
    sm: 'h-3 w-3',
    md: 'h-4 w-4',
    lg: 'h-5 w-5',
}[props.size]))

const focusClasses = computed(() => ({
    cyan: 'focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500',
    blue: 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
    green: 'focus:ring-2 focus:ring-green-500 focus:border-green-500',
    purple: 'focus:ring-2 focus:ring-purple-500 focus:border-purple-500',
    red: 'focus:ring-2 focus:ring-red-500 focus:border-red-500',
    gray: 'focus:ring-2 focus:ring-gray-500 focus:border-gray-500',
}[props.focusColor] || 'focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500'))

const iconPadding = computed(() => ({
    sm: 'pl-2.5',
    md: 'pl-3',
    lg: 'pl-3.5',
}[props.size]))

const clearPadding = computed(() => ({
    sm: 'pr-2.5',
    md: 'pr-3',
    lg: 'pr-3.5',
}[props.size]))

const onInput = event => {
    const value = event.target.value

    emit('update:modelValue', value)

    clearTimeout(searchTimer)

    searchTimer = setTimeout(() => {
        emit('search', value)
    }, props.debounceMs)
}

const onClear = () => {
    clearTimeout(searchTimer)

    emit('update:modelValue', '')
    emit('clear')
    emit('search', '')

    nextTick(() => {
        inputRef.value?.focus()
    })
}

const onEscape = () => {
    emit('escape')
    onClear()
}

const focus = () => {
    inputRef.value?.focus()
}

defineExpose({
    focus,
    inputRef,
})
</script>

<template>
    <div :class="['relative', width]">
        <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none" :class="iconPadding">
            <Search :class="[iconSizes, 'text-gray-400']" />
        </div>

        <input ref="inputRef" :value="modelValue" type="text" :placeholder="placeholder" :class="[
            'block w-full border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none transition-shadow',
            sizeClasses,
            focusClasses,
        ]" @input="onInput" @keyup.esc="onEscape" />

        <button v-if="hasValue" type="button" @click="onClear"
            class="absolute inset-y-0 right-0 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
            :class="clearPadding">
            <X :class="clearSizes" />
        </button>
    </div>
</template>
