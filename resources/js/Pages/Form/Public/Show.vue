<script setup>
import { ref, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import FormField from "@/Components/FormFields/FormField.vue";
import { showToast } from '@/Utils/toast';
import LeiModal from '@/Components/LeiModal.vue';
import {
    Send, Clock, Shield, FileText, AlertTriangle,
    Lock, PauseCircle, XCircle, CheckSquare
} from "lucide-vue-next";

const props = defineProps({
    form: { type: Object, required: true }
});

const showLeiModal = ref(false);

const primaryColor = computed(() => props.form.primary_color || '#06B6D4');
const secondaryColor = computed(() => props.form.secondary_color || '#0891B2');

// ⭐ LOGO COM POSIÇÃO - Nova estrutura
const formLogo = computed(() => props.form.logo?.url || null);
const logoPosicao = computed(() => props.form.logo?.posicao || 'centro');

// ⭐ Classes de alinhamento baseadas na posição
const logoContainerClasses = computed(() => {
    const classes = {
        esquerda: 'text-left',
        centro: 'text-center',
        direita: 'text-right'
    };
    return classes[logoPosicao.value] || classes.centro;
});

const logoImageClasses = computed(() => {
    const classes = {
        esquerda: 'mr-auto',
        centro: 'mx-auto',
        direita: 'ml-auto'
    };
    return classes[logoPosicao.value] || classes.centro;
});

const formStyles = computed(() => ({
    '--form-primary': primaryColor.value,
    '--form-secondary': secondaryColor.value,
    '--form-primary-light': primaryColor.value + '1A',
    '--form-primary-mid': primaryColor.value + '33',
    '--form-primary-border': primaryColor.value + '66',
}));

const isActive = computed(() => props.form.status === 'ativo');

const isExpired = computed(() => {
    if (!props.form.expires_at) return false;
    return new Date(props.form.expires_at) <= new Date();
});

const isLimitReached = computed(() => {
    if (!props.form.response_limit) return false;
    return props.form.responses_count >= props.form.response_limit;
});

const availabilityStatus = computed(() => {
    if (isLimitReached.value) return 'limite';
    if (isExpired.value) return 'expirado';
    if (!isActive.value) return props.form.status || 'inativo';
    return 'ativo';
});

const statusConfig = computed(() => {
    const configs = {
        ativo: { icon: null, title: '', message: '', color: 'cyan', canSubmit: true },
        rascunho: { icon: Lock, title: 'Formulário em Edição', message: 'Este formulário está em rascunho e ainda não está disponível para respostas.', color: 'gray', canSubmit: false },
        pausado: { icon: PauseCircle, title: 'Formulário Pausado', message: 'Este formulário está pausado temporariamente.', color: 'yellow', canSubmit: false },
        encerrado: { icon: XCircle, title: 'Formulário Encerrado', message: 'Este formulário foi encerrado e não aceita mais respostas.', color: 'red', canSubmit: false },
        expirado: { icon: Clock, title: 'Formulário Expirado', message: 'O prazo para respostas deste formulário encerrou.', color: 'orange', canSubmit: false },
        limite: { icon: AlertTriangle, title: 'Limite Atingido', message: 'Este formulário atingiu o número máximo de respostas permitidas.', color: 'purple', canSubmit: false },
    };
    return configs[availabilityStatus.value] || configs.rascunho;
});

const answers = ref({});
const submitting = ref(false);
const errors = ref({});
const acceptedTerms = ref(false);
const termsError = ref(false);

if (statusConfig.value.canSubmit && props.form.fields) {
    props.form.fields.forEach(field => {
        answers.value[field.id] = field.type === 'checkbox' ? [] : '';
    });
}

const validateTerms = () => {
    if (!acceptedTerms.value) {
        termsError.value = true;
        showToast('Você precisa aceitar os Termos de uso e Política de Privacidade para continuar.', 'error');
        return false;
    }
    termsError.value = false;
    return true;
};

const submitForm = () => {
    if (!statusConfig.value.canSubmit) {
        showToast('Este formulário não pode receber respostas no momento.', 'error');
        return;
    }
    if (!validateTerms()) return;

    const slug = props.form.slug || props.form.code || props.form.id;
    if (!slug) {
        showToast('Erro: Identificador do formulário não encontrado.', 'error');
        return;
    }

    submitting.value = true;
    errors.value = {};

    router.post(route('forms.public.store', { slug }), { answers: answers.value }, {
        onSuccess: () => showToast('Formulário respondido com sucesso!', 'success'),
        onError: (e) => {
            errors.value = e;
            let errorMessages = [];
            if (e.status) errorMessages.push(e.status);
            Object.keys(e).forEach(key => {
                if (key.startsWith('answers.')) {
                    const fieldId = key.replace('answers.', '');
                    const field = props.form.fields.find(f => f.id == fieldId);
                    const fieldName = field ? field.label : 'Campo';
                    errorMessages.push(`${fieldName}: ${Array.isArray(e[key]) ? e[key][0] : e[key]}`);
                }
            });
            showToast(errorMessages[0] || 'Erro ao enviar resposta. Verifique os campos.', 'error');
        },
        onFinish: () => { submitting.value = false; }
    });
};

const progress = computed(() => {
    if (!statusConfig.value.canSubmit || !props.form.fields) return 0;
    const total = props.form.fields.length;
    const filled = props.form.fields.filter(f => {
        const val = answers.value[f.id];
        return Array.isArray(val) ? val.length > 0 : val !== '' && val !== null && val !== undefined;
    }).length;
    return total > 0 ? Math.round((filled / total) * 100) : 0;
});
</script>

<template>

    <Head :title="form.title || 'Formulário'" />

    <div class="min-h-screen bg-gray-50 py-8 px-4" :style="formStyles">
        <div class="max-w-2xl mx-auto">
            <div class="mb-8" :class="logoContainerClasses">
                <div v-if="formLogo" class="mb-4">
                    <img :src="formLogo" :alt="form.title"
                        :class="['max-h-24 w-auto object-contain rounded-lg', logoImageClasses]" />
                </div>
                <div v-else class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl mb-4"
                        :style="{ backgroundColor: primaryColor }">
                        <FileText class="w-6 h-6 text-white" />
                    </div>
                </div>
                <p class="text-sm text-gray-500" :class="logoPosicao === 'centro' ? '' : 'mt-2'">
                    Formulário Online
                </p>
            </div>
            <div v-if="!statusConfig.canSubmit" class="bg-white rounded-2xl shadow-sm border-2 overflow-hidden"
                :class="`border-${statusConfig.color}-200`">
                <div class="p-8 text-center" :class="`bg-${statusConfig.color}-50`">
                    <div class="mx-auto flex items-center justify-center w-20 h-20 rounded-full mb-4 bg-white shadow-sm"
                        :class="`text-${statusConfig.color}-500`">
                        <component :is="statusConfig.icon" class="w-10 h-10" />
                    </div>
                    <h1 class="text-2xl font-bold mb-2" :class="`text-${statusConfig.color}-800`">
                        {{ statusConfig.title }}
                    </h1>
                    <p class="text-lg text-gray-700 font-medium">{{ form.title }}</p>
                </div>

                <div class="p-8 text-center">
                    <div class="rounded-lg p-4 mb-6"
                        :class="`bg-${statusConfig.color}-50 border border-${statusConfig.color}-200`">
                        <p class="text-gray-700">{{ statusConfig.message }}</p>
                    </div>
                    <div v-if="form.status === 'rascunho' || form.status === 'pausado'" class="text-sm text-gray-500">
                        <p class="mb-2">Para receber respostas, o responsável precisa:</p>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-lg">
                            <span class="font-medium text-gray-700">
                                Alterar o status para <span class="text-green-600 font-bold">ATIVO</span>
                            </span>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-xs text-gray-400">Entre em contato com o administrador do formulário.</p>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 text-white"
                    :style="{ background: `linear-gradient(to right, ${primaryColor}, ${secondaryColor})` }">
                    <!-- <div v-if="formLogo" class="mb-3" :class="logoContainerClasses">
                        <img :src="formLogo" :alt="form.title"
                            :class="['h-10 w-auto object-contain rounded bg-white/20 p-1', logoImageClasses]" />
                    </div> -->
                    <h1 class="text-2xl font-bold">{{ form.title }}</h1>
                    <p v-if="form.description" class="mt-2 opacity-90 text-sm">{{ form.description }}</p>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-600">Progresso</span>
                        <span class="font-medium" :style="{ color: primaryColor }">{{ progress }}%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300"
                            :style="{ width: progress + '%', backgroundColor: primaryColor }"></div>
                    </div>
                </div>

                <form @submit.prevent="submitForm" class="p-6 space-y-6">
                    <div v-for="field in form.fields" :key="field.id" class="space-y-2">
                        <FormField :field="field" v-model="answers[field.id]"
                            :class="{ 'border-red-300': errors[`answers.${field.id}`] }" />
                        <p v-if="errors[`answers.${field.id}`]" class="text-sm text-red-600 flex items-center gap-1">
                            <AlertTriangle class="w-4 h-4" />
                            {{ errors[`answers.${field.id}`] }}
                        </p>
                    </div>

                    <!-- Checkbox de Termos -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-start gap-3 p-4 rounded-lg border-2 transition-all duration-200" :style="{
                            borderColor: termsError ? '#FCA5A5' : acceptedTerms ? primaryColor + '99' : '#E5E7EB',
                            backgroundColor: termsError ? '#FEF2F2' : acceptedTerms ? primaryColor + '0D' : '#F9FAFB',
                        }">
                            <div class="flex-shrink-0 mt-0.5">
                                <input type="checkbox" id="terms" v-model="acceptedTerms" @change="termsError = false"
                                    class="w-5 h-5 rounded border-gray-300 cursor-pointer"
                                    :style="{ accentColor: primaryColor }" :class="{ 'border-red-400': termsError }" />
                            </div>
                            <div class="flex-1">
                                <label for="terms" class="text-sm text-gray-700 cursor-pointer select-none">
                                    <span class="font-medium">Li e concordo com </span>

                                    <button v-if="form.lei" type="button"
                                        class="underline font-medium hover:opacity-75 transition-opacity duration-150"
                                        :style="{ color: primaryColor }" @click.stop="showLeiModal = true">
                                        {{ form.lei.title }}
                                    </button>

                                    <template v-else>
                                        <a href="/termos-de-uso" target="_blank"
                                            class="underline font-medium hover:opacity-75"
                                            :style="{ color: primaryColor }" @click.stop>Termos de uso</a>
                                        <span class="font-medium"> e </span>
                                        <a href="/politica-de-privacidade" target="_blank"
                                            class="underline font-medium hover:opacity-75"
                                            :style="{ color: primaryColor }" @click.stop>Política de Privacidade</a>
                                    </template>

                                    <span class="text-red-500 font-bold">*</span>
                                </label>

                                <p v-if="termsError" class="text-sm text-red-600 mt-1 flex items-center gap-1">
                                    <AlertTriangle class="w-4 h-4" />
                                    Você deve aceitar os termos para enviar o formulário.
                                </p>
                                <p v-else-if="acceptedTerms" class="text-sm mt-1 flex items-center gap-1"
                                    :style="{ color: secondaryColor }">
                                    <CheckSquare class="w-4 h-4" />
                                    Termos aceitos.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botão enviar -->
                    <div class="pt-2">
                        <button type="submit" :disabled="submitting"
                            class="w-full flex items-center justify-center gap-2 py-3 px-6 rounded-lg text-white font-medium text-sm transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed hover:scale-[1.02] active:scale-[0.98]"
                            :style="{
                                backgroundColor: primaryColor,
                                boxShadow: `0 4px 14px ${primaryColor}40`
                            }"
                            @mouseover="e => !submitting && (e.currentTarget.style.backgroundColor = secondaryColor)"
                            @mouseleave="e => !submitting && (e.currentTarget.style.backgroundColor = primaryColor)">
                            <Send v-if="!submitting" class="w-4 h-4" />
                            <span>{{ submitting ? 'Enviando...' : 'Enviar Resposta' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center space-y-2">
                <div class="flex items-center justify-center gap-4 text-sm text-gray-500">
                    <span class="flex items-center gap-1">
                        <Shield class="w-4 h-4" /> Dados protegidos
                    </span>
                    <span class="flex items-center gap-1">
                        <Clock class="w-4 h-4" /> Resposta rápida
                    </span>
                </div>
                <p class="text-xs text-gray-400">Powered by FormSystem</p>
            </div>
        </div>
    </div>

    <!-- Modal da Lei -->
    <LeiModal :lei="form.lei" :show="showLeiModal" @close="showLeiModal = false" />
</template>
