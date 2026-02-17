<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-br from-gray-50 to-gray-100 pt-6 sm:justify-center sm:pt-0"
    >

        <!-- <div class="mb-8">
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-cyan-600 drop-shadow-md transition-transform hover:scale-105" />
            </Link>
        </div> -->

        <div
            class="w-full overflow-hidden bg-white px-8 py-8 shadow-2xl sm:max-w-md sm:rounded-2xl border border-gray-200 backdrop-blur-sm"
        >
            <slot />
        </div>
    </div>
</template>
