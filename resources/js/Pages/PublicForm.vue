<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { vMaska } from "maska/vue";
import { LockKeyhole, LoaderCircle, CircleCheck } from 'lucide-vue-next';
import Label from "@/Components/ui/label/Label.vue";
import Input from "@/Components/ui/input/Input.vue";

const getMask = (question) => {
  const title = question.title.toLowerCase();
  if (title.includes('cpf')) return '###.###.###-##';
  if (question.type === 'tel' || title.includes('whatsapp') || title.includes('telefone') || title.includes('celular'))
    return '(##) #####-####';
  return undefined;
};

const props = defineProps({
  questions: {
    type: Array,
    default: () => [],
  },
  tenantId: {
    type: [Number, String],
    required: true,
  },
  tenantPhoto: {
    type: String,
    default: null,
  },
  tenantBgColor: {
    type: String,
    default: null,
  },
  tenantButtonColor: {
    type: String,
    default: null,
  },
  tenantName: {
    type: String,
    default: null,
  },
});


const submitted = ref(false);
const errors = ref({});

const form = useForm({
  tenant_id: props.tenantId,
  answers: {},
})

const validate = () => {
  const newErrors = {};
  let valid = true;

  for (const question of props.questions) {
    if (question.is_required) {
      const value = form.answers[question.id];
      if (!value || String(value).trim() === '') {
        newErrors[question.id] = 'Campo obrigatório';
        valid = false;
      }
    }
  }

  errors.value = newErrors;
  return valid;
};

watch(() => form.answers, () => {
  for (const id in errors.value) {
    const value = form.answers[id];
    if (value && String(value).trim() !== '') {
      delete errors.value[id];
    }
  }

  for (const key in form.errors) {
    if (key.startsWith('answers.')) {
      const questionId = key.replace('answers.', '');
      const value = form.answers[questionId];
      if (value && String(value).trim() !== '') {
        form.clearErrors(key);
      }
    }
  }
}, { deep: true });

const submit = () => {
  if (!validate()) return;

  form.post(route("public_form.store"), {
    onSuccess: () => {
      form.reset();
      errors.value = {};
      submitted.value = true;
    },
  })
};
</script>

<template>
  <Head :title="tenantName" />

   <Link
    :href="route('tenant.login')"
    class="fixed top-4 right-4 z-50 flex items-center gap-2 rounded-xl
           bg-white text-black px-4 py-2 text-sm font-semibold
           shadow-lg hover:bg-gray-200 transition"
  >
  <span>Área Administrativa</span>
  </Link>

  <div class="min-h-screen grid grid-cols-1 md:grid-cols-2">
    
    <!-- Esquerdo -->
    <div class="flex items-center justify-center p-24">
      <img
        :src="tenantPhoto || '/images/bg-logo.jpg'"
        alt="Imagem do formulário"
        class="w-full h-full object-contain"
      />
    </div>

    <!-- Direito -->
    <div
      class="flex flex-col items-center justify-center p-6"
      :style="{ backgroundColor: tenantBgColor || '#06b6d4' }"
    >
      <!-- Tela de sucesso -->
      <div
        v-if="submitted"
        class="w-full max-w-xl bg-white rounded-2xl border shadow-xl p-10 text-center"
      >
        <CircleCheck class="w-16 h-16 mx-auto mb-4 text-green-500" />
        <h2 class="text-2xl font-semibold mb-2">Cadastro enviado!</h2>
        <p class="text-gray-500 mb-6">Seus dados foram registrados com sucesso.</p>
        <button
          type="button"
          @click="submitted = false"
          class="rounded-xl text-white px-6 py-2.5 font-semibold hover:opacity-90 transition"
          :style="{ backgroundColor: tenantButtonColor || tenantBgColor || '#06b6d4' }"
        >
          Enviar outro cadastro
        </button>
      </div>

      <!-- Formulário -->
      <form
        v-else
        @submit.prevent="submit"
        class="w-full max-w-xl bg-white rounded-2xl border shadow-xl p-6"
      >
        <h2 class="text-3xl font-semibold mb-5 text-center">
          Cadastro
        </h2>

        <div class="grid gap-3 py-4">
          <Label class="mb-4">
            Preencha os dados abaixo para se cadastrar no clube de benefícios e telemedicina.
          </Label>

          <div class="space-y-4">
            <div
              v-for="question in questions"
              :key="question.id"
              class="space-y-1"
            >
              <Label
                :for="`question-${question.id}`"
                class="block text-sm font-normal"
              >
                {{ question.title }}
                <span v-if="question.is_required" class="text-red-500">*</span>
              </Label>

              <!-- Select para tipo option -->
              <select
                v-if="question.type === 'option'"
                :id="`question-${question.id}`"
                v-model="form.answers[question.id]"
                class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                :class="(errors[question.id] || form.errors[`answers.${question.id}`]) ? 'border-red-500' : 'border-input'"
              >
                <option value="" disabled>Selecione uma opção</option>
                <option
                  v-for="opt in question.options"
                  :key="opt.value"
                  :value="opt.label"
                >
                  {{ opt.label }}
                </option>
              </select>

              <!-- Input com máscara (CPF, Telefone) -->
              <input
                v-else-if="getMask(question)"
                v-maska
                :data-maska="getMask(question)"
                :id="`question-${question.id}`"
                type="text"
                v-model="form.answers[question.id]"
                class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm accent-cyan-500 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                :class="(errors[question.id] || form.errors[`answers.${question.id}`]) ? 'border-red-500' : 'border-input'"
              />

              <!-- Input para outros tipos -->
              <Input
                v-else
                :id="`question-${question.id}`"
                :type="question.type"
                v-model="form.answers[question.id]"
                :class="(errors[question.id] || form.errors[`answers.${question.id}`]) ? 'border-red-500 accent-cyan-500' : 'accent-cyan-500'"
              />

              <span v-if="errors[question.id]" class="text-xs text-red-500">
                {{ errors[question.id] }}
              </span>
              <span v-else-if="form.errors[`answers.${question.id}`]" class="text-xs text-red-500">
                {{ form.errors[`answers.${question.id}`] }}
              </span>
            </div>
          </div>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full mt-6 rounded-xl text-white py-2.5 font-semibold
                 hover:opacity-90 transition flex items-center justify-center gap-2
                 disabled:opacity-60 disabled:cursor-not-allowed"
          :style="{ backgroundColor: tenantButtonColor || tenantBgColor || '#06b6d4' }"
        >
          <LoaderCircle v-if="form.processing" class="w-5 h-5 animate-spin" />
          {{ form.processing ? 'Enviando...' : 'Enviar' }}
        </button>

      </form>

      <div class="mt-4 flex items-center gap-2 justify-center bg-white/20 rounded-xl px-4 py-3 max-w-xl w-full">
        <LockKeyhole class="w-5 h-5 shrink-0 text-white" />
        <span class="text-sm text-white font-medium">Seus dados estão protegidos e serão usados para o clube de benefícios e telemedicina.</span>
      </div>
    </div>
  </div>
</template>
