<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Button } from '@/Components/ui/button';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-8 text-center border-b border-gray-200 pb-6">
            <h2 class="text-3xl font-bold text-gray-900">Bem-vindo</h2>
            <p class="text-sm text-gray-600 mt-2">Entre com suas credenciais para acessar o sistema</p>
        </div>

        <div v-if="status" class="mb-4 p-3 text-sm font-medium text-green-700 bg-green-50 rounded-lg border border-green-200">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email" class="font-semibold text-gray-700" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all shadow-sm hover:border-gray-400"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="seu@email.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Senha" class="font-semibold text-gray-700" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all shadow-sm hover:border-gray-400"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block">
                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-md transition-colors -ml-2">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-700 font-medium">Lembrar-me</span>
                </label>
            </div>

            <div class="pt-4 flex items-center justify-between border-t border-gray-200">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-cyan-600 hover:text-cyan-700 font-medium transition-colors"
                >
                    Esqueceu a senha?
                </Link>

                <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    :disabled="form.processing"
                >
                    Entrar
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
