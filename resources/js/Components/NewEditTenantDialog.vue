<script setup>
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { watch, ref } from 'vue'
import { vMaska } from 'maska/vue'
import { LoaderCircle } from 'lucide-vue-next'
import Dialog from './ui/dialog/Dialog.vue'
import DialogContent from './ui/dialog/DialogContent.vue'
import DialogHeader from './ui/dialog/DialogHeader.vue'
import DialogTitle from './ui/dialog/DialogTitle.vue'
import DialogFooter from './ui/dialog/DialogFooter.vue'
import DialogClose from './ui/dialog/DialogClose.vue'
import Label from './ui/label/Label.vue'
import Input from './ui/input/Input.vue'
import { Button } from './ui/button'
import ColorThief from 'colorthief'

const props = defineProps({
  open: { type: Boolean, required: true },
  tenant: { type: Object, default: null }
})

const form = useForm({
  id: null,
  name: '',
  subdomain: '',
  photo: null,
  bg_color: '#06b6d4',
  button_color: '#06b6d4',
  cep: '',
  logradouro: '',
  numero: '',
  complemento: '',
  bairro: '',
  cidade: '',
  estado: '',
})

const photoPreview = ref(null)
const extractedColors = ref([])
const colorThief = new ColorThief()
const fetchingCep = ref(false)
const emit = defineEmits(['update:open', 'close'])

const rgbToHex = (r, g, b) =>
  '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('')

const extractColors = (imgSrc) => {
  const img = new Image()
  img.crossOrigin = 'anonymous'
  img.onload = () => {
    try {
      const palette = colorThief.getPalette(img, 8, 1)
      extractedColors.value = palette.map(([r, g, b]) => rgbToHex(r, g, b))
    } catch {
      extractedColors.value = []
    }
  }
  img.src = imgSrc
}

const fetchAddress = async (cep) => {
  const cleanCep = cep.replace(/\D/g, '')
  if (cleanCep.length !== 8) return

  fetchingCep.value = true
  try {
    const response = await fetch(`https://viacep.com.br/ws/${cleanCep}/json/`)
    const data = await response.json()

    if (!data.erro) {
      form.logradouro = data.logradouro || ''
      form.bairro = data.bairro || ''
      form.cidade = data.localidade || ''
      form.estado = data.uf || ''
    }
  } catch {
    // CEP inválido ou erro de rede
  } finally {
    fetchingCep.value = false
  }
}

watch(() => form.cep, (newCep) => {
  if (newCep && newCep.replace(/\D/g, '').length === 8) {
    fetchAddress(newCep)
  }
})

watch(() => props.tenant, (newTenant) => {
  if (newTenant) {
    form.id = newTenant.id
    form.name = newTenant.name
    form.subdomain = newTenant.tenant_domain?.replace(/\..+$/, '') || ''
    form.bg_color = newTenant.bg_color || '#06b6d4'
    form.button_color = newTenant.button_color || '#06b6d4'
    form.photo = null
    form.cep = newTenant.cep || ''
    form.logradouro = newTenant.logradouro || ''
    form.numero = newTenant.numero || ''
    form.complemento = newTenant.complemento || ''
    form.bairro = newTenant.bairro || ''
    form.cidade = newTenant.cidade || ''
    form.estado = newTenant.estado || ''
    photoPreview.value = newTenant.photo_url || null
    if (newTenant.photo_url) extractColors(newTenant.photo_url)
  } else {
    form.reset()
    photoPreview.value = null
    extractedColors.value = []
  }
}, { immediate: true })

watch(() => props.open, (isOpen) => {
  if (!isOpen && !props.tenant) {
    form.reset()
    photoPreview.value = null
  }
})

const handlePhotoChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.photo = file
    const url = URL.createObjectURL(file)
    photoPreview.value = url
    extractColors(url)
  }
}

const submit = () => {
  const isEditing = !!props.tenant

  const payload = {
    name: form.name,
    subdomain: form.subdomain,
    photo: form.photo,
    bg_color: form.bg_color,
    button_color: form.button_color,
    cep: form.cep,
    logradouro: form.logradouro,
    numero: form.numero,
    complemento: form.complemento,
    bairro: form.bairro,
    cidade: form.cidade,
    estado: form.estado,
  }

  if (isEditing) {
    router.post(route('tenants.update', props.tenant.id), {
      _method: 'PUT',
      ...payload,
    }, {
      forceFormData: true,
      onSuccess: () => {
        form.reset()
        photoPreview.value = null
        emit('update:open', false)
        emit('close')
        router.reload({ only: ['tenants'], preserveState: true })
      }
    })
  } else {
    form.transform(() => payload).post(route('tenants.store'), {
      forceFormData: true,
      onSuccess: () => {
        form.reset()
        photoPreview.value = null
        emit('update:open', false)
        emit('close')
        router.reload({ only: ['tenants'], preserveState: true })
      },
      preserveScroll: true,
    })
  }
}
</script>

<template>
  <Dialog :open="props.open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-[625px] max-h-[90vh] overflow-y-auto">
      <form @submit.prevent="submit">
        <DialogHeader>
          <DialogTitle>{{ props.tenant ? 'Editar Tenant' : 'Novo Tenant' }}</DialogTitle>
        </DialogHeader>

        <div class="grid gap-4 py-4">
          <div class="grid gap-3">
            <Label for="name">Nome</Label>
            <Input id="name" name="name"
            v-model="form.name" />
          </div>
        </div>

        <div class="grid gap-4 py-4">
          <div class="grid gap-3">
            <Label for="subdomain">SubDomínio</Label>
            <Input id="subdomain" name="subdomain"
            v-model="form.subdomain" />
          </div>
        </div>

        <div class="grid gap-4 py-4">
          <div class="grid gap-3">
            <Label for="photo">Logo / Foto</Label>
            <input
              id="photo"
              type="file"
              accept="image/jpg,image/jpeg,image/png,image/webp"
              @change="handlePhotoChange"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm file:border-0 file:bg-transparent file:text-sm file:font-medium"
            />
            <img
              v-if="photoPreview"
              :src="photoPreview"
              alt="Preview"
              class="mt-2 w-32 h-32 object-contain rounded border"
            />
            <span v-if="form.errors.photo" class="text-sm text-red-600">
              {{ form.errors.photo }}
            </span>
            <div v-if="extractedColors.length" class="mt-3">
              <span class="text-xs text-muted-foreground">Cores detectadas (clique para usar como fundo):</span>
              <div class="flex items-center gap-2 mt-1">
                <button
                  v-for="(color, i) in extractedColors"
                  :key="i"
                  type="button"
                  @click="form.bg_color = color"
                  class="w-7 h-7 rounded-full border-2 cursor-pointer hover:scale-110 transition-transform"
                  :class="form.bg_color === color ? 'border-black' : 'border-gray-300'"
                  :style="{ backgroundColor: color }"
                  :title="color"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="grid gap-4 py-4">
          <div class="grid gap-3">
            <Label for="bg_color">Cor de Fundo do Formulário</Label>
            <div class="flex items-center gap-3">
              <input
                id="bg_color"
                type="color"
                v-model="form.bg_color"
                class="w-12 h-10 rounded border border-input cursor-pointer"
              />
              <Input
                v-model="form.bg_color"
                placeholder="#06b6d4"
                class="flex-1"
              />
            </div>
            <span v-if="form.errors.bg_color" class="text-sm text-red-600">
              {{ form.errors.bg_color }}
            </span>
          </div>
        </div>

        <div class="grid gap-4 py-4">
          <div class="grid gap-3">
            <Label for="button_color">Cor do Botão</Label>
            <div class="flex items-center gap-3">
              <input
                id="button_color"
                type="color"
                v-model="form.button_color"
                class="w-12 h-10 rounded border border-input cursor-pointer"
              />
              <Input
                v-model="form.button_color"
                placeholder="#06b6d4"
                class="flex-1"
              />
            </div>
            <span v-if="form.errors.button_color" class="text-sm text-red-600">
              {{ form.errors.button_color }}
            </span>
          </div>
        </div>

        <!-- Endereço -->
        <div class="border-t pt-4 mt-2">
          <h3 class="text-sm font-semibold mb-3">Endereço</h3>

          <div class="grid gap-4">
            <div class="grid grid-cols-2 gap-3">
              <div class="grid gap-2">
                <Label for="cep">CEP</Label>
                <div class="relative">
                  <input
                    id="cep"
                    v-maska
                    data-maska="#####-###"
                    type="text"
                    v-model="form.cep"
                    placeholder="00000-000"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                  />
                  <LoaderCircle
                    v-if="fetchingCep"
                    class="absolute right-3 top-2.5 w-5 h-5 animate-spin text-gray-400"
                  />
                </div>
              </div>
              <div class="grid gap-2">
                <Label for="estado">Estado</Label>
                <Input
                  id="estado"
                  v-model="form.estado"
                  placeholder="UF"
                  maxlength="2"
                  readonly
                  class="bg-gray-50"
                />
              </div>
            </div>

            <div class="grid gap-2">
              <Label for="logradouro">Logradouro</Label>
              <Input
                id="logradouro"
                v-model="form.logradouro"
                placeholder="Rua, Avenida..."
                readonly
                class="bg-gray-50"
              />
            </div>

            <div class="grid grid-cols-3 gap-3">
              <div class="grid gap-2">
                <Label for="numero">Número</Label>
                <Input
                  id="numero"
                  v-model="form.numero"
                  placeholder="Nº"
                />
              </div>
              <div class="col-span-2 grid gap-2">
                <Label for="complemento">Complemento</Label>
                <Input
                  id="complemento"
                  v-model="form.complemento"
                  placeholder="Sala, Andar..."
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div class="grid gap-2">
                <Label for="bairro">Bairro</Label>
                <Input
                  id="bairro"
                  v-model="form.bairro"
                  placeholder="Bairro"
                  readonly
                  class="bg-gray-50"
                />
              </div>
              <div class="grid gap-2">
                <Label for="cidade">Cidade</Label>
                <Input
                  id="cidade"
                  v-model="form.cidade"
                  placeholder="Cidade"
                  readonly
                  class="bg-gray-50"
                />
              </div>
            </div>
          </div>
        </div>

        <DialogFooter class="mt-4">
          <DialogClose as-child>
            <Button variant="outline" :disabled="form.processing">Cancelar</Button>
          </DialogClose>

          <Button variant="primary" type="submit" :disabled="form.processing">
            <span v-if="form.processing" class="flex items-center gap-2">
              <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Salvando...
            </span>
            <span v-else>Salvar</span>
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
