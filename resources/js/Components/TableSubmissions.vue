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
  submissions: {
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

const questions = props.submissions.data?.[0]?.answers?.map(a => a.question) || [];

const getAnswer = (submission, questionId) => {
  const answer = submission.answers.find(a => a.question_id === questionId);
  return answer?.answer || '-';
};
</script>

<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Cadastros Registrados</h1>
      <a
        :href="route('form_submissions.report')"
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
        <TableRow v-for="submission in submissions.data" :key="submission.id">
          <TableCell class="text-center font-medium">
            {{ submission.id }}
          </TableCell>
          
          <TableCell 
            v-for="question in questions" 
            :key="question.id"
            class="text-center"
          >
            <div class="truncate max-w-[200px]" :title="getAnswer(submission, question.id)">
              {{ getAnswer(submission, question.id) }}
            </div>
          </TableCell>
          
          <TableCell class="text-center">
            {{ formatDate(submission.created_at) }}
          </TableCell>
          
          <TableCell class="text-center">
            <div class="flex gap-3 justify-center">
              <a
                :href="route('form_submissions.pdf', submission.id)"
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

    <div v-if="submissions.links" class="flex gap-2 justify-center mt-4">
      <template v-for="link in submissions.links" :key="link.label">
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