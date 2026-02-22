<script setup>
import { ref, watch, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/Components/ui/dialog";

const props = defineProps({
  open: {
    type: Boolean,
    required: true,
  },
  user: {
    type: Object,
    default: null,
  },
  storeRoute: {
    type: String,
    default: "users.store",
  },
  updateRoute: {
    type: String,
    default: "users.update",
  },
});

const emit = defineEmits(["update:open"]);

const isEditing = computed(() => !!props.user);

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

watch(
  () => props.user,
  (user) => {
    if (user) {
      form.name = user.name;
      form.email = user.email;
      form.password = "";
      form.password_confirmation = "";
    } else {
      form.reset();
    }
  },
  { immediate: true }
);

const close = () => emit("update:open", false);

const submit = () => {
  if (isEditing.value) {
    form.put(route(props.updateRoute, props.user.id), {
      onSuccess: () => close(),
    });
  } else {
    form.post(route(props.storeRoute), {
      onSuccess: () => close(),
    });
  }
};
</script>

<template>
  <Dialog :open="open" @update:open="close">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>
          {{ isEditing ? "Editar Usuário" : "Novo Usuário" }}
        </DialogTitle>
      </DialogHeader>

      <form @submit.prevent="submit" class="space-y-4">
        <div class="space-y-1">
          <Label for="name">Nome</Label>
          <Input
            id="name"
            v-model="form.name"
            type="text"
            placeholder="Nome completo"
            required
          />
          <p v-if="form.errors.name" class="text-sm text-red-500">
            {{ form.errors.name }}
          </p>
        </div>

        <div class="space-y-1">
          <Label for="email">E-mail</Label>
          <Input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="email@exemplo.com"
            required
          />
          <p v-if="form.errors.email" class="text-sm text-red-500">
            {{ form.errors.email }}
          </p>
        </div>

        <div class="space-y-1">
          <Label for="password">
            {{ isEditing ? "Nova Senha (deixe em branco para manter)" : "Senha" }}
          </Label>
          <Input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="Mínimo 8 caracteres"
            :required="!isEditing"
          />
          <p v-if="form.errors.password" class="text-sm text-red-500">
            {{ form.errors.password }}
          </p>
        </div>

        <div class="space-y-1">
          <Label for="password_confirmation">Confirmar Senha</Label>
          <Input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            placeholder="Repita a senha"
            :required="!isEditing || !!form.password"
          />
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <Button type="button" variant="secondary" @click="close">
            Cancelar
          </Button>
          <Button type="submit" :disabled="form.processing">
            {{ isEditing ? "Salvar" : "Criar" }}
          </Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
