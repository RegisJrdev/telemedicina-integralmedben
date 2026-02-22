<script setup>
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { LayoutDashboard, Building2, ClipboardList, Users, LogOut } from "lucide-vue-next";
import { computed } from "vue";

const page = usePage();
const authUser = computed(() => page.props.auth?.user);

const logoutForm = useForm({});

const logout = () => {
  logoutForm.post(route("logout"));
};

const navLinks = [
  { label: "Dashboard", routeName: "dashboard", icon: LayoutDashboard },
  { label: "Credenciados", routeName: "credenciados.index", icon: Building2 },
  { label: "Registros", routeName: "registros.index", icon: ClipboardList },
  { label: "Usuários", routeName: "central-users.index", icon: Users },
];
</script>

<template>
  <div class="flex min-h-screen bg-gray-50">
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
      <div class="p-6 border-b border-gray-200">
        <span class="text-lg font-bold text-gray-900 tracking-tight">
          IntegralMedBen
        </span>
        <p class="text-xs text-gray-400 mt-0.5">Painel Administrativo</p>
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
      </nav>

      <div class="p-4 border-t border-gray-200 space-y-3">
        <div v-if="authUser" class="px-3">
          <p class="text-sm font-medium text-gray-800 truncate">{{ authUser.name }}</p>
          <p class="text-xs text-gray-400 truncate">{{ authUser.email }}</p>
        </div>

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
