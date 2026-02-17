<script setup>
const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
})

const emit = defineEmits(['update:open'])
</script>

<template>
  <AlertDialog
    :open="props.open"
    @update:open="emit('update:open', $event)"
  >
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>Tem certeza que dezseja prosseguir?</AlertDialogTitle>
        <AlertDialogDescription>
          Essa ação não pode ser desfeita.
        </AlertDialogDescription>
      </AlertDialogHeader>

      <AlertDialogFooter>
        <AlertDialogCancel>
          Cancelar
        </AlertDialogCancel>

        <AlertDialogAction
          @click="
            emit('confirm');
            emit('update:open', false);
          "
          class="bg-red-500 hover:bg-red-600 text-white"
        >
          Continuar
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

