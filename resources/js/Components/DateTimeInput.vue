<script setup>
import { ref, watch, computed } from 'vue'
import { Calendar, Clock } from 'lucide-vue-next'
import { Label } from '@/Components/ui/label'

const props = defineProps({
    modelValue: { type: String, default: '' },
    label: { type: String, default: 'Data e Hora' },
    id: { type: String, default: 'datetime' },
    required: { type: Boolean, default: false },
    className: {
        type: String,
        default:
            'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500',
    },
})

const emit = defineEmits(['update:modelValue'])

const localValue = ref('')

const normalizeDateTime = (value) => {
    if (!value) return ''

    let normalized = String(value)
        .trim()
        .replace(' ', 'T')
        .slice(0, 16)

    if (/^\d{4}-\d{2}-\d{2}$/.test(normalized)) {
        return `${normalized}T00:00`
    }

    if (/^\d{4}-\d{2}-\d{2}T$/.test(normalized)) {
        return `${normalized}00:00`
    }

    return normalized
}

watch(
    () => props.modelValue,
    (newVal) => {
        localValue.value = normalizeDateTime(newVal)
    },
    { immediate: true }
)

const isComplete = computed(() => {
    return /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(localValue.value)
})

const formattedDate = computed(() => {
    if (!isComplete.value) return ''

    const [date, time] = localValue.value.split('T')
    const [year, month, day] = date.split('-')

    return `${day}/${month}/${year} ${time}`
})

const handleChange = (event) => {
    const value = normalizeDateTime(event.target.value)

    localValue.value = value

    emit('update:modelValue', value)
}
</script>

<template>
    <div>
        <Label
            :for="id"
            class="text-gray-700 flex items-center gap-2 mb-1"
        >
            <Calendar class="w-4 h-4" />

            {{ label }}

            <span
                v-if="required"
                class="text-red-500"
            >
                *
            </span>
        </Label>

        <div class="relative">
            <input
                :id="id"
                type="datetime-local"
                v-model="localValue"
                :class="[
                    className,
                    isComplete
                        ? 'border-cyan-500 focus:ring-cyan-500'
                        : ''
                ]"
                @input="handleChange"
                @change="handleChange"
            />
        </div>

        <p
            v-if="isComplete"
            class="mt-1 text-sm text-cyan-600 flex items-center gap-1"
        >
            <Clock class="w-3 h-3" />

            {{ formattedDate }}
        </p>
    </div>
</template>
