<script setup>
import { Pencil, Trash2 } from "lucide-vue-next";
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
  users: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["edit-user", "delete-user"]);

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("pt-BR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
};
</script>

<template>
  <div class="p-3 sm:p-6 overflow-x-auto">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead class="text-center">Código</TableHead>
          <TableHead class="text-center">Nome</TableHead>
          <TableHead class="text-center">E-mail</TableHead>
          <TableHead class="text-center">Criado em</TableHead>
          <TableHead class="text-center">Ações</TableHead>
        </TableRow>
      </TableHeader>

      <TableBody>
        <TableRow v-for="user in users.data" :key="user.id">
          <TableCell class="text-center font-medium">
            {{ user.id }}
          </TableCell>

          <TableCell class="text-center">
            {{ user.name }}
          </TableCell>

          <TableCell class="text-center">
            {{ user.email }}
          </TableCell>

          <TableCell class="text-center">
            {{ formatDate(user.created_at) }}
          </TableCell>

          <TableCell class="text-center">
            <div class="flex gap-3 justify-center">
              <Pencil
                class="w-5 h-5 cursor-pointer hover:text-cyan-600"
                @click="emit('edit-user', user)"
              />
              <Trash2
                class="w-5 h-5 cursor-pointer hover:text-red-600"
                @click="emit('delete-user', user)"
              />
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>

    <div v-if="users.links" class="flex gap-2 justify-center mt-4">
      <template v-for="link in users.links" :key="link.label">
        <Link
          v-if="link.url"
          :href="link.url"
          :class="[
            'px-3 py-1 rounded',
            link.active ? 'bg-cyan-600 text-white' : 'bg-gray-200',
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
