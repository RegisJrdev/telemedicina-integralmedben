<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import PatientsTable from "@/Components/TablePatients.vue";
import { Button } from "@/Components/ui/button";

const props = defineProps({
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

const form = useForm({});

const logout = () => {
  form.post(route("tenant.logout"));
};
</script>

<template>
  <Head title="Pacientes Cadastrados" />

  <div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
      <div class="mb-6 bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <img
              v-if="tenantPhoto"
              :src="tenantPhoto"
              :alt="tenantName"
              class="h-20 object-contain"
            />
            <div
              v-else
              class="w-14 h-14 rounded-lg bg-gray-700 flex items-center justify-center"
            >
              <span class="text-white text-xl font-bold">
                {{ tenantName?.charAt(0)?.toUpperCase() }}
              </span>
            </div>
          </div>
          <div class="flex gap-3">
            <Button as-child variant="secondary">
              <Link :href="route('public_form.show')"> Formulário </Link>
            </Button>
            <Button @click="logout" variant="danger"> Sair </Button>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow">
        <PatientsTable :patients="patients" />
      </div>
    </div>
  </div>
</template>
