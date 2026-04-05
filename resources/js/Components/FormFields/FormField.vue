<script setup>
import { computed } from 'vue';
import TextField from './TextField.vue';
import TextareaField from './TextareaField.vue';
import SelectField from './SelectField.vue';
import CheckboxField from './CheckboxField.vue';
import RadioField from './RadioField.vue';
import DateField from './DateField.vue';

const props = defineProps({
    field: {
        type: Object,
        required: true
    },
    modelValue: {
        type: [String, Number, Boolean, Array, Object],
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue']);

const fieldComponents = {
    text: TextField,
    email: TextField,
    number: TextField,
    tel: TextField,
    textarea: TextareaField,
    select: SelectField,
    checkbox: CheckboxField,
    radio: RadioField,
    date: DateField,
};

// Sem focus:ring aqui — controlado pelo <style scoped> abaixo
const inputClass = "form-field-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none disabled:bg-gray-100 disabled:text-gray-400 transition-colors duration-150";

const updateValue = (value) => {
    emit('update:modelValue', value);
};
</script>

<template>
    <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-900">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
        </label>

        <p v-if="field.help_text" class="text-xs text-gray-500">
            {{ field.help_text }}
        </p>

        <component :is="fieldComponents[field.type]" :field="field" :model-value="modelValue" :disabled="disabled"
            :class="inputClass" @update:model-value="updateValue" />

        <slot name="error" />
    </div>
</template>

<style scoped>
/*
  CSS var --form-primary é herdada do PublicForm.vue via cascade.
  Scoped não bloqueia herança de CSS vars, então funciona normalmente.
  O :deep() garante que o seletor alcance inputs dentro dos sub-componentes
  (TextField, SelectField, etc.) mesmo com scoped.
*/
:deep(.form-field-input:focus),
:deep(.form-field-input:focus-within) {
    border-color: var(--form-primary);
    box-shadow: 0 0 0 2px color-mix(in srgb, var(--form-primary) 25%, transparent);
}

/* accentColor para checkbox e radio nativos */
:deep(.form-field-input input[type="checkbox"]),
:deep(.form-field-input input[type="radio"]) {
    accent-color: var(--form-primary);
}
</style>
