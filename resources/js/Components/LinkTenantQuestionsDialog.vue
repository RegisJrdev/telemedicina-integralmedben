<script setup>
import { useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { CheckCircle2 } from "lucide-vue-next";
import Dialog from "./ui/dialog/Dialog.vue";
import DialogContent from "./ui/dialog/DialogContent.vue";
import DialogHeader from "./ui/dialog/DialogHeader.vue";
import DialogTitle from "./ui/dialog/DialogTitle.vue";
import DialogFooter from "./ui/dialog/DialogFooter.vue";
import Label from "./ui/label/Label.vue";
import { Button } from "./ui/button";
import { ref, computed, watch } from "vue";

const selectedQuestions = ref([]);

const props = defineProps({
  openModalQuestions: { type: Boolean, required: true },
  questions: { type: Array, required: true },
  tenant: { type: Object, default: null },
});

const form = useForm({
  tenant_id: null,
  questions: selectedQuestions,
});

const emit = defineEmits(["close"]);
const selectedCount = computed(() => selectedQuestions.value.length);

watch(() => props.tenant, (newTenant) => {
  if (newTenant && newTenant.questions) {
    selectedQuestions.value = newTenant.questions.map(q => q.id);
  } else {
    selectedQuestions.value = [];
  }
}, { immediate: true });

const toggleQuestion = (id) => {
  const index = selectedQuestions.value.indexOf(id);

  if (index === -1) {
    selectedQuestions.value.push(id);
  } else {
    selectedQuestions.value.splice(index, 1);
  }
};

const formatType = (type) => {
  const types = {
    text: { label: "Texto", color: "bg-blue-100 text-blue-800" },
    email: { label: "Email", color: "bg-purple-100 text-purple-800" },
    number: { label: "Número", color: "bg-green-100 text-green-800" },
    tel: { label: "Telefone", color: "bg-yellow-100 text-yellow-800" },
    date: { label: "Data", color: "bg-pink-100 text-pink-800" },
    option: { label: "Seleção", color: "bg-cyan-100 text-cyan-800" },
  };
  return types[type] || { label: type, color: "bg-gray-100 text-gray-800" };
};

const submit = () => {
  if (!props.tenant) {
    return;
  }

  form.tenant_id = props.tenant.id;
  form.questions = selectedQuestions.value;
  form.post(route("tenant_questions.store"), {
    onSuccess: () => {
      form.reset();
      selectedQuestions.value = [];
      emit("close");
      router.reload({ only: ["tenants"] });
    }
  });
};
</script>

<template>
  <Dialog :open="props.openModalQuestions">
    <DialogContent class="sm:max-w-[700px]">
      <form @submit.prevent="submit">
        <DialogHeader>
          <DialogTitle class="text-xl font-bold">Associar Questões ao Tenant</DialogTitle>
          <p class="text-sm text-gray-500 mt-2">
            Selecione as questões que farão parte do formulário deste tenant
            <span v-if="selectedCount > 0" class="inline-flex ml-2 px-2 py-0.5 bg-cyan-100 text-cyan-800 rounded-full text-xs font-semibold">
              {{ selectedCount }} {{ selectedCount === 1 ? 'selecionada' : 'selecionadas' }}
            </span>
          </p>
        </DialogHeader>

        <div class="py-4 max-h-[400px] overflow-y-auto">
          <div class="grid gap-3">
            <div
              v-for="question in questions"
              :key="question.id"
              @click="toggleQuestion(question.id)"
              class="group flex items-start gap-4 border-2 p-4 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-md"
              :class="{
                'border-cyan-500 bg-cyan-50 shadow-sm': selectedQuestions.includes(question.id),
                'border-gray-200 hover:border-gray-300 bg-white': !selectedQuestions.includes(question.id),
              }"
            >
              <div class="flex items-center pt-0.5">
                <input
                  type="checkbox"
                  :checked="selectedQuestions.includes(question.id)"
                  class="w-4 h-4 text-cyan-600 bg-gray-100 border-gray-300 rounded focus:ring-cyan-500 pointer-events-none"
                />
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                  <Label class="cursor-pointer font-medium text-gray-900 group-hover:text-gray-700">
                    {{ question.title }}
                  </Label>
                  <CheckCircle2
                    v-if="selectedQuestions.includes(question.id)"
                    class="w-5 h-5 text-cyan-600 flex-shrink-0"
                  />
                </div>
                <div class="mt-2">
                </div>
              </div>
            </div>

            <div v-if="questions.length === 0" class="text-center py-8 text-gray-500">
              <p class="text-sm">Nenhuma questão disponível</p>
            </div>
          </div>
        </div>

        <DialogFooter class="gap-2">
          <Button
            type="button"
            variant="secondary"
            @click="emit('close')"
          >
            Cancelar
          </Button>

          <Button
            type="submit"
            variant="primary"
            :disabled="selectedCount === 0"
          >
            Salvar Seleção
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
