<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { Users, UserCircle, FileText, LogOut, ExternalLink, ChevronRight } from "lucide-vue-next";
import { ref } from "vue";

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
const open = ref(false);

const logout = () => {
  logoutForm.post(route("tenant.logout"));
};

const navLinks = [
  { label: "Pacientes", routeName: "patients.index", icon: Users },
  { label: "Usuários", routeName: "users.index", icon: UserCircle },
];
</script>

<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Backdrop -->
    <transition name="fade">
      <div
        v-if="open"
        class="fixed inset-0 z-30 bg-black/30"
        @click="open = false"
      />
    </transition>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-40 flex flex-col bg-white border-r border-gray-200 transition-all duration-300 ease-in-out',
        open ? 'w-64 shadow-xl' : 'w-16',
      ]"
    >
      <!-- Toggle -->
      <button
        @click="open = !open"
        class="absolute -right-3 top-6 z-10 w-6 h-6 bg-white border border-gray-200 rounded-full flex items-center justify-center shadow-sm hover:bg-gray-50 transition-colors"
      >
        <ChevronRight
          :class="['w-3.5 h-3.5 text-gray-500 transition-transform duration-300', open ? 'rotate-180' : '']"
        />
      </button>

      <!-- Header -->
      <div class="p-4 border-b border-gray-200 overflow-hidden">
        <template v-if="open">
          <img
            v-if="tenantPhoto"
            :src="tenantPhoto"
            :alt="tenantName"
            class="h-14 object-contain"
          />
          <div v-else class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gray-700 flex items-center justify-center shrink-0">
              <span class="text-white text-base font-bold">
                {{ tenantName?.charAt(0)?.toUpperCase() }}
              </span>
            </div>
            <span class="font-semibold text-gray-800 truncate">{{ tenantName }}</span>
          </div>
        </template>
        <template v-else>
          <div class="w-8 h-8 rounded-lg bg-gray-700 flex items-center justify-center mx-auto">
            <span class="text-white text-sm font-bold">
              {{ tenantName?.charAt(0)?.toUpperCase() }}
            </span>
          </div>
        </template>
      </div>

      <!-- Nav -->
      <nav class="flex-1 p-2 space-y-1 overflow-hidden">
        <Link
          v-for="link in navLinks"
          :key="link.routeName"
          :href="route(link.routeName)"
          :title="!open ? link.label : undefined"
          @click="open = false"
          :class="[
            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
            !open ? 'justify-center' : '',
            route().current(link.routeName)
              ? 'bg-cyan-50 text-cyan-700'
              : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
          ]"
        >
          <component :is="link.icon" class="w-5 h-5 shrink-0" />
          <span v-if="open" class="whitespace-nowrap">{{ link.label }}</span>
        </Link>

        <a
          :href="route('public_form.show')"
          target="_blank"
          :title="!open ? 'Formulário Público' : undefined"
          @click="open = false"
          :class="[
            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors',
            !open ? 'justify-center' : '',
          ]"
        >
          <FileText class="w-5 h-5 shrink-0" />
          <template v-if="open">
            Formulário Público
            <ExternalLink class="w-3.5 h-3.5 ml-auto opacity-50" />
          </template>
        </a>
      </nav>

      <!-- Footer -->
      <div class="p-2 border-t border-gray-200 overflow-hidden">
        <button
          @click="logout"
          :disabled="logoutForm.processing"
          :title="!open ? 'Sair' : undefined"
          :class="[
            'flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors',
            !open ? 'justify-center' : '',
          ]"
        >
          <LogOut class="w-5 h-5 shrink-0" />
          <span v-if="open">Sair</span>
        </button>
      </div>
    </aside>

    <!-- Main content — sempre com ml-16 para o strip de ícones -->
    <main class="ml-16 px-4 py-6 sm:px-6 sm:py-8 md:px-8 md:py-10 min-h-screen overflow-auto">
      <slot />
    </main>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
