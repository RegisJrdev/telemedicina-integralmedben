<script setup>
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import CentralAdminLayout from "@/Layouts/CentralAdminLayout.vue";
import TableQuestions from "@/Components/TableQuestions.vue";
import QuestionDialog from "@/Components/QuestionDialog.vue";

defineProps({
  questions: {
    type: Object,
  },
});

const openQuestionDialog = ref(false);
const selectedQuestion = ref(null);

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
  <Head title="Registros" />

  <CentralAdminLayout>
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
      <h1 class="text-xl sm:text-2xl font-bold">Registros</h1>
      <Button variant="primary" @click="openCreateQuestionDialog">
        Nova Pergunta
      </Button>
    </div>

    <QuestionDialog
      v-model:open="openQuestionDialog"
      :question="selectedQuestion"
    />

    <div class="bg-white border rounded-xl border-gray-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <TableQuestions
          @edit-question="openEditQuestionDialog"
          @view-question="viewQuestion"
          class="bg-white"
          :questions="questions"
        />
      </div>
    </div>
  </CentralAdminLayout>
</template>
