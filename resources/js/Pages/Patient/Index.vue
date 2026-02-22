<script setup>
import { Head } from "@inertiajs/vue3";
import TenantAdminLayout from "@/Layouts/TenantAdminLayout.vue";
import PatientsTable from "@/Components/TablePatients.vue";

defineProps({
  patients: {
    type: Object,
    required: true,
  },
  tenantName: {
    type: String,
    default: "",
  },
  tenantPhoto: {
    type: String,
    default: null,
  },
});
</script>

<template>
  <Head title="Pacientes Cadastrados" />

  <TenantAdminLayout :tenant-name="tenantName" :tenant-photo="tenantPhoto">
    <div class="bg-white rounded-lg shadow">
      <PatientsTable :patients="patients" />
    </div>
  </TenantAdminLayout>
</template>
