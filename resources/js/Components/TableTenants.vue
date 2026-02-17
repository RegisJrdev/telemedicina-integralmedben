<script setup>
import { Settings, Pencil, Trash2 } from "lucide-vue-next";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  tenants: {
    type: Array,
    required: true,
    default: () => [],
  },
});

const emit = defineEmits(["request-assign-questions", "edit-tenant"]);

const linkTenantQuestions = (tenant) => {
  emit("request-assign-questions", tenant);
};

const editTenant = (tenant) => {
  emit("edit-tenant", tenant);
};

const deleteTenant = (tenant) => {
  if (confirm(`Tem certeza que deseja excluir o tenant "${tenant.name}"?`)) {
    router.delete(route("tenants.destroy"), {
      data: { id: tenant.id }
    });
  }
};
</script>

<template>
  <Table>
    <TableHeader>
      <TableRow>
        <TableHead class="w-[100px] text-center"> Nome </TableHead>
        <TableHead class="text-center">SubDomínio</TableHead>
        <TableHead class="text-center"> Teste </TableHead>
        <TableHead class="text-center"> Ações </TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      <TableRow v-for="tenant in tenants" :key="tenant.name">
        <TableCell class="font-medium text-center">
          {{ tenant.name }}
        </TableCell>
        <TableCell class="text-center">
          <a
            :href="`http://${tenant.tenant_domain}:8000`"
            target="_blank"
            class="text-cyan-600 hover:text-cyan-800 hover:underline"
          >
            {{ tenant.tenant_domain }}
          </a>
        </TableCell>
        <TableCell class="text-center"> teste </TableCell>
        <TableCell class="text-center">
          <div class="flex items-center justify-center gap-3">
            <button
              @click="editTenant(tenant)"
              class="text-black hover:text-cyan-900 transition-colors"
              title="Editar"
            >
              <Pencil class="w-5 h-5" />
            </button>
            <button
              @click="linkTenantQuestions(tenant)"
              class="text-black hover:text-gray-900 transition-colors"
              title="Configurar Questões"
            >
              <Settings class="w-5 h-5" />
            </button>
            <button
              @click="deleteTenant(tenant)"
              class="text-black hover:text-red-900 transition-colors"
              title="Excluir"
            >
              <Trash2 class="w-5 h-5" />
            </button>
          </div>
        </TableCell>
      </TableRow>
    </TableBody>
    
  </Table>
</template>
