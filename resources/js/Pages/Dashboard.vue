<script setup>
import CardTenants from "@/Components/CardTenants.vue";
import TableQuestions from "@/Components/TableQuestions.vue";
import QuestionDialog from "@/Components/QuestionDialog.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import NewEditTenantDialog from "@/Components/NewEditTenantDialog.vue";
import LinkTenantQuestionsDialog from "@/Components/LinkTenantQuestionsDialog.vue";
import { Button } from "@/Components/ui/button";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import BarChart from "@/Components/BarChart.vue";


const activeTab = ref("tenants");
const open = ref(false);
const openModalQuestions = ref(false);
const openQuestionDialog = ref(false);
const selectedTenant = ref(null);
const selectedQuestion = ref(null);
const editingTenant = ref(null);

const props = defineProps({
  tenants: {
    type: Object,
  },
  questions: {
    type: Object,
  },
});

const openAssignQuestionsModal = (tenant) => {
  selectedTenant.value = tenant;
  openModalQuestions.value = true;
};

const openEditTenantModal = (tenant) => {
  editingTenant.value = tenant;
  open.value = true;
};

const handleCloseEditModal = () => {
  editingTenant.value = null;
  open.value = false;
};

const openCreateQuestionDialog = () => {
  selectedQuestion.value = null;
  openQuestionDialog.value = true;
};

const openEditQuestionDialog = (question) => {
  selectedQuestion.value = question;
  openQuestionDialog.value = true;
};

const viewQuestion = (question) => {
  router.visit(route("questions.show", question.id));
};
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>

    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 mt-6">
      <BarChart
        title="Cadastros Mensais"
        description="Janeiro - Junho 2024"
        :data="[
          { label: 'Jan', value: 42 },
          { label: 'Fev', value: 78 },
          { label: 'Mar', value: 65 },
          { label: 'Abr', value: 120 },
          { label: 'Mai', value: 95 },
          { label: 'Jun', value: 330 },
          { label: 'Jun', value: 150 },
          { label: 'Jun', value: 150 },
          { label: 'Jun', value: 150 },
          { label: 'Jun', value: 150 },
          { label: 'Jun', value: 150 },
          { label: 'Jun', value: 150 },
        ]"
        class=""
      />
    </div>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 uppercase">
        Dashboard
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-6 border-b border-gray-200">
          <nav class="-mb-px flex gap-6">
            <button
              @click="activeTab = 'tenants'"
              :class="[
                'border-b-2 py-3 px-1 text-sm font-medium transition-colors',
                activeTab === 'tenants'
                  ? 'border-cyan-500 text-cyan-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              Tenants
            </button>
            <button
              @click="activeTab = 'questions'"
              :class="[
                'border-b-2 py-3 px-1 text-sm font-medium transition-colors',
                activeTab === 'questions'
                  ? 'border-cyan-500 text-cyan-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              Questões
            </button>
          </nav>
        </div>

        <div v-if="activeTab === 'tenants'">
          <div class="flex justify-end mb-4">
            <Button
              variant="primary"
              @click="() => { editingTenant = null; open = true; }"
            >
              Cadastrar Novo Tenant
            </Button>
          </div>

          <NewEditTenantDialog
            v-model:open="open"
            :tenant="editingTenant"
            @close="handleCloseEditModal"
          />

          <div class="bg-white border rounded-xl border-gray-200 shadow-sm overflow-hidden">
            <div class="max-h-[800px] overflow-auto">
              <TableTenants
                @request-assign-questions="openAssignQuestionsModal"
                @edit-tenant="openEditTenantModal"
                class="bg-white"
                :tenants="props.tenants.data"
              />
            </div>
          </div>

          <LinkTenantQuestionsDialog
            :tenant="selectedTenant"
            :open-modal-questions="openModalQuestions"
            @close="openModalQuestions = false"
            :questions="props.questions"
          />
        </div>

        <div v-if="activeTab === 'questions'">
          <div class="flex justify-end mb-4">
            <Button
              variant="primary"
              @click="openCreateQuestionDialog"
            >
              Nova Pergunta
            </Button>
          </div>

          <QuestionDialog
            v-model:open="openQuestionDialog"
            :question="selectedQuestion"
          />

          <div class="bg-white border rounded-xl border-gray-200 shadow-sm overflow-hidden">
            <div class="max-h-[800px] overflow-auto">
              <TableQuestions
                @edit-question="openEditQuestionDialog"
                @view-question="viewQuestion"
                class="bg-white"
                :questions="props.questions"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
