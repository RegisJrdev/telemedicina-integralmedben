<script setup>
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import CentralAdminLayout from "@/Layouts/CentralAdminLayout.vue";
import TableSmsTemplates from "@/Components/TableSmsTemplates.vue";
import SmsTemplateDialog from "@/Components/SmsTemplateDialog.vue";
import LinkTenantSmsTemplatesDialog from "@/Components/LinkTenantSmsTemplatesDialog.vue";

defineProps({
  templates: { type: Object, required: true },
  tenants: { type: Array, required: true },
  events: { type: Array, required: true },
  variables: { type: Array, required: true },
});

const openDialog = ref(false);
const selectedTemplate = ref(null);

const openLinkDialog = ref(false);
const templateToLink = ref(null);

// Abre o dialog no modo criação
const openCreateDialog = () => {
  selectedTemplate.value = null;
  openDialog.value = true;
};

// Abre o dialog no modo edição com o template selecionado
const openEditDialog = (template) => {
  selectedTemplate.value = template;
  openDialog.value = true;
};

// Abre o dialog de vínculo de tenants
const openLinkTenantsDialog = (template) => {
  templateToLink.value = template;
  openLinkDialog.value = true;
};

// Pede confirmação e deleta o template
const deleteTemplate = (template) => {
  if (!confirm(`Deseja excluir o template "${template.name}"?`)) return;
  router.delete(route("sms-templates.destroy", template.id));
};
</script>

<template>
  <Head title="Templates de SMS" />

  <CentralAdminLayout>
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
      <h1 class="text-xl sm:text-2xl font-bold">Templates de SMS</h1>
      <Button @click="openCreateDialog">Novo Template</Button>
    </div>

    <div class="bg-white rounded-lg shadow">
      <TableSmsTemplates
        :templates="templates"
        @edit-template="openEditDialog"
        @delete-template="deleteTemplate"
        @link-tenants="openLinkTenantsDialog"
      />
    </div>
  </CentralAdminLayout>

  <SmsTemplateDialog
    v-model:open="openDialog"
    :template="selectedTemplate"
    :events="events"
    :variables="variables"
  />

  <LinkTenantSmsTemplatesDialog
    v-model:open="openLinkDialog"
    :template="templateToLink"
    :tenants="tenants"
  />
</template>
