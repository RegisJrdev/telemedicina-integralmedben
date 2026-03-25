<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Button } from '@/Components/ui/button';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
const form = useForm({
    email: '',
    password: '',
    remember: false,
});
const showPassword = ref(false);
const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
const togglePassword = () => {
    showPassword.value = !showPassword.value;
};
</script>
<template>
    <GuestLayout>

        <Head title="Log in" />
        <div class="mb-8 text-center border-b border-gray-200 pb-6">
            <h2 class="text-3xl font-bold text-gray-900">Bem-vindo</h2>
            <p class="text-sm text-gray-600 mt-2">Entre com suas credenciais para acessar o sistema</p>
        </div>
        <div v-if="status"
            class="mb-4 p-3 text-sm font-medium text-green-700 bg-green-50 rounded-lg border border-green-200">
            {{ status }}
        </div>
        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email" class="font-semibold text-gray-700" />
                <TextInput id="email" type="email"
                    class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all shadow-sm hover:border-gray-400"
                    v-model="form.email" required autofocus autocomplete="username" placeholder="seu@email.com" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
                <InputLabel for="password" value="Senha" class="font-semibold text-gray-700" />
                <div class="relative mt-2">
                    <TextInput id="password" :type="showPassword ? 'text' : 'password'"
                        class="block w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all shadow-sm hover:border-gray-400"
                        v-model="form.password" required autocomplete="current-password" placeholder="••••••••" />
                    <!-- Botão de toggle da senha -->
                    <button type="button" @click="togglePassword"
                        class="absolute inset-y-0 right-0 flex items-center justify-center w-12 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors"
                        :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'">
                        <!-- Ícone Olho Aberto (mostrar senha) -->
                        <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Ícone Olho Fechado (ocultar senha) -->
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="block">
                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-md transition-colors -ml-2">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-700 font-medium">Lembrar-me</span>
                </label>
            </div>
            <div class="pt-4 flex items-center justify-between border-t border-gray-200">
                <Link v-if="canResetPassword" :href="route('password.request')"
                    class="text-sm text-cyan-600 hover:text-cyan-700 font-medium transition-colors">
                    Esqueceu a senha?
                </Link>
                <Button type="submit" variant="primary" size="lg" :disabled="form.processing">
                    Entrar
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
