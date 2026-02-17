<script setup>
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { watch, computed } from 'vue'
import { Plus, Trash2 } from 'lucide-vue-next'
import Dialog from './ui/dialog/Dialog.vue'
import DialogContent from './ui/dialog/DialogContent.vue'
import DialogHeader from './ui/dialog/DialogHeader.vue'
import DialogTitle from './ui/dialog/DialogTitle.vue'
import DialogFooter from './ui/dialog/DialogFooter.vue'
import DialogClose from './ui/dialog/DialogClose.vue'
import Label from './ui/label/Label.vue'
import Input from './ui/input/Input.vue'
import { Button } from './ui/button'

const props = defineProps({
  open: { type: Boolean, required: true },
  question: { type: Object, default: null }
})

const emit = defineEmits(['update:open'])

const form = useForm({
  title: '',
  type: 'text',
  options: [],
  is_required: false,
  is_unique: false,
})

const isOptionType = computed(() => form.type === 'option')

// Atualiza o form quando uma questão for passada para edição
watch(() => props.question, (newQuestion) => {
  if (newQuestion) {
    form.title = newQuestion.title
    form.type = newQuestion.type
    form.options = newQuestion.options?.map(opt => opt.label) || []
    form.is_required = newQuestion.is_required || false
    form.is_unique = newQuestion.is_unique || false
  } else {
    form.reset()
    form.options = []
  }
}, { immediate: true })

// Adiciona campos de opção vazios quando muda para tipo option
watch(() => form.type, (newType) => {
  if (newType === 'option' && form.options.length === 0) {
    addOption()
    addOption()
  }
})

const addOption = () => {
  form.options.push('')
}

const removeOption = (index) => {
  form.options.splice(index, 1)
}

const submit = () => {
  const route_name = props.question
    ? route('questions.update', props.question.id)
    : route('questions.store')

  const method = props.question ? 'put' : 'post'

  // Prepara os dados para envio
  const data = {
    title: form.title,
    type: form.type,
    is_required: form.is_required,
    is_unique: form.is_unique,
  }

  // Só inclui options se o tipo for 'option'
  if (form.type === 'option') {
    data.options = form.options
  }

  form.transform(() => data)[method](route_name, {
    onSuccess: () => {
      form.reset()
      form.options = []
      emit('update:open', false)
      router.reload({ only: ['questions'] })
    }
  })
}

const typeOptions = [
  { value: 'text', label: 'Texto' },
  { value: 'email', label: 'Email' },
  { value: 'number', label: 'Número' },
  { value: 'tel', label: 'Telefone' },
  { value: 'date', label: 'Data' },
  { value: 'option', label: 'Seleção (Dropdown)' }
]
</script>

<template>
  <Dialog :open="props.open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-[625px] max-h-[90vh] overflow-y-auto">
      <form @submit.prevent="submit">
        <DialogHeader>
          <DialogTitle>{{ props.question ? 'Editar Pergunta' : 'Nova Pergunta' }}</DialogTitle>
        </DialogHeader>

        <div class="grid gap-4 py-4">
          <div class="grid gap-3">
            <Label for="title">Título da Pergunta</Label>
            <Input
              id="title"
              name="title"
              v-model="form.title"
              placeholder="Digite o título da pergunta"
              required
            />
            <span v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</span>
          </div>

          <div class="grid gap-3">
            <Label for="type">Tipo de Campo</Label>
            <select
              id="type"
              v-model="form.type"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              required
            >
              <option v-for="option in typeOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
            <span v-if="form.errors.type" class="text-sm text-red-600">{{ form.errors.type }}</span>
          </div>

          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <input
                id="is_required"
                type="checkbox"
                v-model="form.is_required"
                class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500"
              />
              <Label for="is_required" class="cursor-pointer">Campo obrigatório</Label>
            </div>

            <div class="flex items-center gap-2">
              <input
                id="is_unique"
                type="checkbox"
                v-model="form.is_unique"
                class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500"
              />
              <Label for="is_unique" class="cursor-pointer">Campo único</Label>
            </div>
          </div>

          <div v-if="isOptionType" class="grid gap-3 border-t pt-4">
            <div class="flex items-center justify-between">
              <Label>Opções</Label>
              <Button
                type="button"
                @click="addOption"
                variant="primary"
                size="sm"
              >
                <Plus class="w-4 h-4 mr-1" />
                Adicionar Opção
              </Button>
            </div>

            <div
              v-for="(option, index) in form.options"
              :key="index"
              class="flex gap-2 items-start"
            >
              <div class="flex-1 grid gap-2">
                <Input
                  :id="`option-${index}`"
                  v-model="form.options[index]"
                  placeholder="Ex: Opção 1"
                  required
                />
                <span v-if="form.errors[`options.${index}`]" class="text-xs text-red-600">
                  {{ form.errors[`options.${index}`] }}
                </span>
              </div>

              <Button
                type="button"
                @click="removeOption(index)"
                variant="ghost"
                size="icon-sm"
                class="mt-1 text-red-600 hover:text-red-900"
                :disabled="form.options.length <= 1"
              >
                <Trash2 class="w-4 h-4" />
              </Button>
            </div>

            <span v-if="form.errors.options" class="text-sm text-red-600">
              {{ form.errors.options }}
            </span>
          </div>
        </div>

        <DialogFooter>
          <DialogClose as-child>
            <Button
              type="button"
              variant="outline"
            >
              Cancelar
            </Button>
          </DialogClose>

          <Button
            variant="primary"
            type="submit"
            :disabled="form.processing"
          >
            {{ props.question ? 'Atualizar' : 'Salvar' }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
