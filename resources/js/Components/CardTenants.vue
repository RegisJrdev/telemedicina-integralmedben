<script setup>
import { Settings, Pencil, Trash2, Trash } from "lucide-vue-next";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import AlertDialog from "./ConfirmDeleteDialog.vue";

const open = ref(false);
const tenantIdToDelete = ref(null);

const props = defineProps({
  tenants: {
    type: Array,
    required: true,
    default: () => [],
  },
});

const askDelete = (id) => {
  tenantIdToDelete.value = id;
  open.value = true;
};

const confirmDelete = (id) => {
  router.delete(route("tenants.destroy", id), {
    onSuccess: () => {
      open.value = false;
      tenantIdToDelete.value = null;
    }
  });
};
</script>

<template>
  <div
    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 max-h-[800px] overflow-y-auto"
  >
    <Card v-for="tenant in props.tenants" :key="tenant.id" class="p-3">
      <div class="space-y-1">
        <h3 class="text-sm font-semibold leading-tight truncate">
          {{ tenant.name }}
        </h3>

        <p class="text-xs text-muted-foreground truncate">
          {{ tenant.tenant_domain }}
        </p>
      </div>

      <div class="mt-3 flex justify-start gap-2">
        <button variant="ghost" size="icon">
          <Settings class="w-4 h-4 hover:bg-slate-400" />
        </button>

        <button variant="ghost" size="icon">
          <Pencil class="w-4 h-4" />
        </button>

        <button @click="askDelete(tenant.id)" variant="ghost" size="icon">
          <Trash2 class="w-4 h-4" />
        </button>
      </div>
    </Card>

    <AlertDialog v-model:open="open" @confirm="confirmDelete" />
  </div>
</template>
