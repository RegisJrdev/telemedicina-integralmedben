<script setup>
import CentralAdminLayout from "@/Layouts/CentralAdminLayout.vue";
import NewEditTenantDialog from "@/Components/NewEditTenantDialog.vue";
import LinkTenantQuestionsDialog from "@/Components/LinkTenantQuestionsDialog.vue";
import { Button } from "@/Components/ui/button";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

const open = ref(false);
const openModalQuestions = ref(false);
const selectedTenant = ref(null);
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
</script>

<template>
  <Head title="Credenciados" />

  <CentralAdminLayout>

    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Credenciados</h1>
      <Button
        variant="primary"
        @click="() => { editingTenant = null; open = true; }"
      >
        Novo Credenciado
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
  </CentralAdminLayout>
</template>
