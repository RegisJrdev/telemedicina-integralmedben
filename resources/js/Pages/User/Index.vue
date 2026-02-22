<script setup>
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import TenantAdminLayout from "@/Layouts/TenantAdminLayout.vue";
import TableUsers from "@/Components/TableUsers.vue";
import UserDialog from "@/Components/UserDialog.vue";

defineProps({
  users: {
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

const openDialog = ref(false);
const selectedUser = ref(null);

const openCreateDialog = () => {
  selectedUser.value = null;
  openDialog.value = true;
};

const openEditDialog = (user) => {
  selectedUser.value = user;
  openDialog.value = true;
};

const deleteUser = (user) => {
  if (!confirm(`Deseja excluir o usuário "${user.name}"?`)) return;
  router.delete(route("users.destroy", user.id));
};
</script>

<template>
  <Head title="Usuários" />

  <TenantAdminLayout :tenant-name="tenantName" :tenant-photo="tenantPhoto">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
      <h1 class="text-xl sm:text-2xl font-bold">Usuários</h1>
      <Button @click="openCreateDialog"> Novo Usuário </Button>
    </div>

    <div class="bg-white rounded-lg shadow">
      <TableUsers
        :users="users"
        @edit-user="openEditDialog"
        @delete-user="deleteUser"
      />
    </div>
  </TenantAdminLayout>

  <UserDialog v-model:open="openDialog" :user="selectedUser" />
</template>
