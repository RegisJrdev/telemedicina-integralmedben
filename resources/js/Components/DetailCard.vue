<!-- components/DetailCard.vue -->
<script setup>
defineProps({
    detail: {
        type: Object,
        required: true,
        validator: (d) => 'id' in d && 'sigla' in d
    },
    showDescription: {
        type: Boolean,
        default: false
    },
    maxDescWidth: {
        type: String,
        default: 'max-w-xs'
    },
    avatarSize: {
        type: String,
        default: 'w-8 h-8'
    }
});
</script>

<template>
    <div class="flex items-center gap-2">
        <!-- Badge com sigla -->
        <div :class="[
            avatarSize,
            'rounded-lg bg-gradient-to-br from-cyan-400 to-blue-500',
            'flex items-center justify-center shrink-0',
            'text-white text-xs font-semibold tracking-wide'
        ]">
            {{ detail.sigla }}
        </div>

        <!-- Texto ao lado -->
        <div class="min-w-0">
            <span class="text-sm text-gray-700 font-medium truncate">
                {{ detail.descricao || detail.sigla }}
            </span>
            <p v-if="showDescription && detail.descricao" :class="[
                'text-xs text-gray-400 truncate mt-0.5',
                maxDescWidth
            ]">
                {{ detail.descricao }}
            </p>
        </div>
    </div>
</template>
