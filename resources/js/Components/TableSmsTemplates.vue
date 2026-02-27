<script setup>
import { Pencil, Trash2, Link2 } from "lucide-vue-next";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/Components/ui/table";

defineProps({
  templates: { type: Object, required: true },
});

const emit = defineEmits(["edit-template", "delete-template", "link-tenants"]);
</script>

<template>
  <div class="p-3 sm:p-6 overflow-x-auto">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Nome</TableHead>
          <TableHead>Canal</TableHead>
          <TableHead>Evento</TableHead>
          <TableHead>Tenants vinculados</TableHead>
          <TableHead class="text-center">Status</TableHead>
          <TableHead class="text-center">Ações</TableHead>
        </TableRow>
      </TableHeader>

      <TableBody>
        <TableRow v-for="template in templates.data" :key="template.id">
          <TableCell class="font-medium">{{ template.name }}</TableCell>

          <TableCell>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 uppercase">
              {{ template.channel }}
            </span>
          </TableCell>

          <TableCell class="text-sm text-gray-600">{{ template.event }}</TableCell>

          <!-- Lista de tenants vinculados via pivot -->
          <TableCell class="text-sm text-gray-600">
            <span v-if="template.tenants?.length === 0" class="text-gray-400 italic">
              Nenhum
            </span>
            <div v-else class="flex flex-wrap gap-1">
              <span
                v-for="tenant in template.tenants"
                :key="tenant.id"
                class="inline-flex px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-xs"
              >
                {{ tenant.id }}
              </span>
            </div>
          </TableCell>

          <TableCell class="text-center">
            <span
              :class="[
                'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                template.is_active
                  ? 'bg-green-100 text-green-800'
                  : 'bg-gray-100 text-gray-600',
              ]"
            >
              {{ template.is_active ? "Ativo" : "Inativo" }}
            </span>
          </TableCell>

          <TableCell class="text-center">
            <div class="flex gap-3 justify-center">
              <Link2
                class="w-5 h-5 cursor-pointer hover:text-cyan-600"
                title="Vincular tenants"
                @click="emit('link-tenants', template)"
              />
              <Pencil
                class="w-5 h-5 cursor-pointer hover:text-cyan-600"
                @click="emit('edit-template', template)"
              />
              <Trash2
                class="w-5 h-5 cursor-pointer hover:text-red-600"
                @click="emit('delete-template', template)"
              />
            </div>
          </TableCell>
        </TableRow>

        <TableRow v-if="templates.data.length === 0">
          <TableCell colspan="6" class="text-center text-gray-400 py-8">
            Nenhum template cadastrado.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>

    <!-- Paginação -->
    <div v-if="templates.last_page > 1" class="flex justify-center gap-2 mt-4">
      <a
        v-for="link in templates.links"
        :key="link.label"
        :href="link.url"
        v-html="link.label"
        :class="[
          'px-3 py-1 text-sm rounded border',
          link.active
            ? 'bg-cyan-600 text-white border-cyan-600'
            : 'text-gray-600 border-gray-300 hover:bg-gray-50',
          !link.url ? 'opacity-40 pointer-events-none' : '',
        ]"
      />
    </div>
  </div>
</template>
