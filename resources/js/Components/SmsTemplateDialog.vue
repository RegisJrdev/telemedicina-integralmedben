<script setup>
import { computed, watch, ref, nextTick } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/Components/ui/dialog";

const props = defineProps({
  open: { type: Boolean, required: true },
  template: { type: Object, default: null },
  events: { type: Array, default: () => [] },
  variables: { type: Array, default: () => [] },
});

const emit = defineEmits(["update:open"]);

const messageRef = ref(null);

const isEditing = computed(() => !!props.template);

const form = useForm({
  name: "",
  message: "",
  channel: "sms",
  event: "",
  is_active: true,
});

watch(
  () => props.template,
  (template) => {
    if (template) {
      form.name = template.name;
      form.message = template.message;
      form.channel = template.channel;
      form.event = template.event;
      form.is_active = template.is_active;
    } else {
      form.reset();
      form.channel = "sms";
      form.is_active = true;
    }
  },
  { immediate: true }
);

const close = () => emit("update:open", false);

// Insere {key} na posição do cursor no textarea
const insertVariable = (key) => {
  const textarea = messageRef.value;
  if (!textarea) {
    form.message += `{${key}}`;
    return;
  }
  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  form.message = form.message.slice(0, start) + `{${key}}` + form.message.slice(end);
  nextTick(() => {
    textarea.selectionStart = textarea.selectionEnd = start + key.length + 2;
    textarea.focus();
  });
};

const submit = () => {
  if (isEditing.value) {
    form.put(route("sms-templates.update", props.template.id), {
      onSuccess: () => close(),
    });
  } else {
    form.post(route("sms-templates.store"), {
      onSuccess: () => close(),
    });
  }
};
</script>

<template>
  <Dialog :open="open" @update:open="close">
    <DialogContent class="sm:max-w-lg">
      <DialogHeader>
        <DialogTitle>
          {{ isEditing ? "Editar Template" : "Novo Template de SMS" }}
        </DialogTitle>
      </DialogHeader>

      <form @submit.prevent="submit" class="space-y-4 mt-2">
        <!-- Nome -->
        <div class="space-y-1">
          <Label for="name">Nome do Template</Label>
          <Input
            id="name"
            v-model="form.name"
            placeholder="Ex: Boas-vindas novo paciente"
          />
          <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
        </div>

        <!-- Evento -->
        <div class="space-y-1">
          <Label for="event">Evento</Label>
          <select
            id="event"
            v-model="form.event"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"
          >
            <option value="">Selecione um evento</option>
            <option v-for="ev in events" :key="ev.value" :value="ev.value">
              {{ ev.label }}
            </option>
          </select>
          <p v-if="form.errors.event" class="text-xs text-red-500">{{ form.errors.event }}</p>
        </div>

        <!-- Mensagem -->
        <div class="space-y-1">
          <Label for="message">Mensagem</Label>
          <textarea
            id="message"
            ref="messageRef"
            v-model="form.message"
            rows="4"
            placeholder="Ex: Olá, seu cadastro foi realizado com sucesso!"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 resize-none"
          />

          <!-- Variáveis clicáveis -->
          <div v-if="variables.length" class="space-y-1 pt-1">
            <p class="text-xs text-gray-400">Clique para inserir na mensagem:</p>
            <div class="flex flex-wrap gap-1">
              <button
                v-for="v in variables"
                :key="v.key"
                type="button"
                @click="insertVariable(v.key)"
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded border border-cyan-200 bg-cyan-50 text-cyan-700 text-xs font-mono hover:bg-cyan-100 transition-colors"
                :title="v.label"
              >
                {{ v.label }}
              </button>
            </div>
          </div>

          <p v-if="form.errors.message" class="text-xs text-red-500">{{ form.errors.message }}</p>
        </div>

        <!-- Status ativo -->
        <div class="flex items-center gap-2">
          <input
            id="is_active"
            type="checkbox"
            v-model="form.is_active"
            class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"
          />
          <Label for="is_active">Template ativo</Label>
        </div>

        <!-- Botões -->
        <div class="flex justify-end gap-2 pt-2">
          <Button type="button" variant="outline" @click="close">
            Cancelar
          </Button>
          <Button type="submit" :disabled="form.processing">
            {{ isEditing ? "Salvar alterações" : "Criar template" }}
          </Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
