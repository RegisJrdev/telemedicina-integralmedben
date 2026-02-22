<script setup>
import { Head } from "@inertiajs/vue3";
import CentralAdminLayout from "@/Layouts/CentralAdminLayout.vue";
import BarChart from "@/Components/BarChart.vue";
</script>

<template>
  <Head title="Dashboard" />

  <CentralAdminLayout>
    <div class="grid grid-cols-1 gap-6">
      <BarChart
        title="Cadastros Mensais"
        description="Janeiro - Junho 2024"
        :data="[
          { label: 'Jan', value: 42 },
          { label: 'Fev', value: 78 },
          { label: 'Mar', value: 65 },
          { label: 'Abr', value: 120 },
          { label: 'Mai', value: 95 },
          { label: 'Jun', value: 330 },
          { label: 'Jul', value: 150 },
          { label: 'Ago', value: 150 },
          { label: 'Set', value: 150 },
          { label: 'Out', value: 150 },
          { label: 'Nov', value: 150 },
          { label: 'Dez', value: 150 },
        ]"
      />
    </div>
  </CentralAdminLayout>
</template>
