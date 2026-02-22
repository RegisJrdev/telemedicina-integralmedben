<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { Users, UserCircle, FileText, LogOut, ExternalLink } from "lucide-vue-next";

defineProps({
  tenantName: {
    type: String,
    default: "",
  },
  tenantPhoto: {
    type: String,
    default: null,
  },
});

const logoutForm = useForm({});

const logout = () => {
  logoutForm.post(route("tenant.logout"));
};

const navLinks = [
  { label: "Pacientes", routeName: "patients.index", icon: Users },
  { label: "Usuários", routeName: "users.index", icon: UserCircle },
];
</script>

<template>
  <div class="flex min-h-screen bg-gray-50">
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
      <div class="p-6 border-b border-gray-200">
        <img
          v-if="tenantPhoto"
          :src="tenantPhoto"
          :alt="tenantName"
          class="h-14 object-contain"
        />
        <div
          v-else
          class="flex items-center gap-3"
        >
          <div class="w-10 h-10 rounded-lg bg-gray-700 flex items-center justify-center shrink-0">
            <span class="text-white text-base font-bold">
              {{ tenantName?.charAt(0)?.toUpperCase() }}
            </span>
          </div>
          <span class="font-semibold text-gray-800 truncate">{{ tenantName }}</span>
        </div>
      </div>

      <nav class="flex-1 p-4 space-y-1">
        <Link
          v-for="link in navLinks"
          :key="link.routeName"
          :href="route(link.routeName)"
          :class="[
            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
            route().current(link.routeName)
              ? 'bg-cyan-50 text-cyan-700'
              : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
          ]"
        >
          <component :is="link.icon" class="w-5 h-5 shrink-0" />
          {{ link.label }}
        </Link>

        <a
          :href="route('public_form.show')"
          target="_blank"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors"
        >
          <FileText class="w-5 h-5 shrink-0" />
          Formulário Público
          <ExternalLink class="w-3.5 h-3.5 ml-auto opacity-50" />
        </a>
      </nav>

      <div class="p-4 border-t border-gray-200">
        <button
          @click="logout"
          :disabled="logoutForm.processing"
          class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors"
        >
          <LogOut class="w-5 h-5 shrink-0" />
          Sair
        </button>
      </div>
    </aside>

    <main class="flex-1 px-8 py-10 overflow-auto">
      <slot />
    </main>
  </div>
</template>
