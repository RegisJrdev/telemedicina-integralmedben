<script setup>
import { FileText, Download } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/Components/ui/table";

const props = defineProps({
  patients: {
    type: Object,
    required: true,
  },
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const questions = props.patients.data?.[0]?.answers?.map(a => a.question) || [];

const getAnswer = (patient, questionId) => {
  const answer = patient.answers.find(a => a.question_id === questionId);
  return answer?.answer || '-';
};
</script>

<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Pacientes Cadastrados</h1>
      <a
        :href="route('patients.report')"
        target="_blank"
        class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 text-white text-sm font-medium rounded hover:bg-cyan-700 transition-colors"
      >
        <Download class="w-4 h-4" />
        Relatório Geral
      </a>
    </div>

    <Table>
      <TableHeader>
        <TableRow>
          <TableHead class="text-center">Código</TableHead>
          <TableHead
            v-for="question in questions"
            :key="question.id"
            class="text-center"
          >
            <div class="truncate" :title="question.title">
              {{ question.title }}
            </div>
          </TableHead>
          <TableHead class="text-center">Data</TableHead>
          <TableHead class="text-center">Ações</TableHead>
        </TableRow>
      </TableHeader>

      <TableBody>
        <TableRow v-for="patient in patients.data" :key="patient.id">
          <TableCell class="text-center font-medium">
            {{ patient.id }}
          </TableCell>

          <TableCell
            v-for="question in questions"
            :key="question.id"
            class="text-center"
          >
            <div class="truncate max-w-[200px]" :title="getAnswer(patient, question.id)">
              {{ getAnswer(patient, question.id) }}
            </div>
          </TableCell>

          <TableCell class="text-center">
            {{ formatDate(patient.created_at) }}
          </TableCell>

          <TableCell class="text-center">
            <div class="flex gap-3 justify-center">
              <a
                :href="route('patients.pdf', patient.id)"
                target="_blank"
                class="inline-block"
              >
                <FileText class="w-5 h-5 cursor-pointer hover:text-green-600" />
              </a>
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>

    <div v-if="patients.links" class="flex gap-2 justify-center mt-4">
      <template v-for="link in patients.links" :key="link.label">
        <Link
          v-if="link.url"
          :href="link.url"
          :class="[
            'px-3 py-1 rounded',
            link.active ? 'bg-cyan-600 text-white' : 'bg-gray-200'
          ]"
          v-html="link.label"
        />
        <span
          v-else
          class="px-3 py-1 rounded bg-gray-100 text-gray-400"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>
