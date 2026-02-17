<script setup>
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  open: { type: Boolean, required: true }
})

const form = useForm({
  name: '',
  subdomain: ''
})

const submit = () => {
  form.post(route('tenants.store'), {
    onSuccess: () => {
      form.reset()
      emit('update:open', false)
      router.reload({ only: ['tenants'] })
    }
  })
}

const emit = defineEmits(['update:open'])
</script>

<template>

  <Dialog :open="props.open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-[625px]">
      <form @submit.prevent="submit">
        <DialogHeader>
          <DialogTitle>Novo Tenant</DialogTitle>
        </DialogHeader>

        <div class="grid gap-4 py-4 ">
          <div class="grid gap-3">
            <Label for="name">Nome</Label>
            <Input id="name" name="name" 
            v-model="form.name" />
          </div>
        </div>

        <div class="grid gap-4 py-4 ">
          <div class="grid gap-3">
            <Label for="subdomain">SubDomínio</Label>
            <Input id="subdomain" name="subdomain" 
            v-model="form.subdomain" />
          </div>
      </div>


        <DialogFooter>
          <DialogClose as-child>
            <PrimaryButton class="bg-red-500 hover:bg-red-600"  variant="outline">Cancelar</PrimaryButton>
          </DialogClose>

          <PrimaryButton class="bg-gradient-to-r from-cyan-500 to-cyan-600" type="submit">
            Salvar
          </PrimaryButton>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
