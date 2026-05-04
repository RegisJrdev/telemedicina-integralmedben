<template>
    <div class="w-full" :class="maxWidthClass">
        <div class="rounded-xl border shadow-lg overflow-hidden" :class="cardBgClass">
            <div class="p-4 sm:p-6 flex flex-col items-center text-center">

                <!-- Título / Status -->
                <div class="mb-4">
                    <h3 v-if="title" class="text-sm sm:text-base font-semibold" :class="textMutedClass">
                        {{ title }}
                    </h3>
                    <div class="flex items-center gap-2 justify-center mt-1.5">
                        <span class="w-2 h-2 rounded-full animate-pulse"
                            :class="isExpired ? 'bg-red-500' : 'bg-emerald-500'"></span>
                        <span class="text-xs" :class="textMutedClass">
                            {{ isExpired ? 'Expirado' : 'Em andamento' }}
                        </span>
                    </div>
                </div>

                <!-- Grid do Contador -->
                <div class="grid grid-cols-4 gap-2 sm:gap-4 w-full">
                    <!-- Dias -->
                    <div class="flex flex-col items-center">
                        <div class="rounded-lg flex items-center justify-center shadow-inner font-mono font-bold tabular-nums"
                            :class="[boxSizeClass, numberBgClass, textClass]">
                            {{ pad(timeLeft.days) }}
                        </div>
                        <span class="text-[10px] sm:text-xs mt-1.5 uppercase tracking-wider" :class="textMutedClass">
                            {{ timeLeft.days === 1 ? 'Dia' : 'Dias' }}
                        </span>
                    </div>

                    <!-- Horas -->
                    <div class="flex flex-col items-center">
                        <div class="rounded-lg flex items-center justify-center shadow-inner font-mono font-bold tabular-nums"
                            :class="[boxSizeClass, numberBgClass, textClass]">
                            {{ pad(timeLeft.hours) }}
                        </div>
                        <span class="text-[10px] sm:text-xs mt-1.5 uppercase tracking-wider" :class="textMutedClass">
                            {{ timeLeft.hours === 1 ? 'Hora' : 'Horas' }}
                        </span>
                    </div>

                    <!-- Minutos -->
                    <div class="flex flex-col items-center">
                        <div class="rounded-lg flex items-center justify-center shadow-inner font-mono font-bold tabular-nums"
                            :class="[boxSizeClass, numberBgClass, textClass]">
                            {{ pad(timeLeft.minutes) }}
                        </div>
                        <span class="text-[10px] sm:text-xs mt-1.5 uppercase tracking-wider" :class="textMutedClass">
                            {{ timeLeft.minutes === 1 ? 'Min' : 'Mins' }}
                        </span>
                    </div>

                    <!-- Segundos -->
                    <div class="flex flex-col items-center">
                        <div class="rounded-lg flex items-center justify-center shadow-inner font-mono font-bold tabular-nums"
                            :class="[boxSizeClass, numberBgClass, textClass]">
                            {{ pad(timeLeft.seconds) }}
                        </div>
                        <span class="text-[10px] sm:text-xs mt-1.5 uppercase tracking-wider" :class="textMutedClass">
                            {{ timeLeft.seconds === 1 ? 'Seg' : 'Segs' }}
                        </span>
                    </div>
                </div>

                <!-- Barra de progresso -->
                <div v-if="showProgress && !isExpired" class="w-full mt-5">
                    <div class="flex justify-between text-xs mb-1.5" :class="textMutedClass">
                        <span>{{ formatDateBR(currentDateValue) }}</span>
                        <span>{{ formatDateBR(expireDateValue) }}</span>
                    </div>
                    <div class="w-full h-2 rounded-full overflow-hidden" :class="progressTrackClass">
                        <div class="h-full rounded-full transition-all duration-1000 ease-linear"
                            :class="progressFillClass" :style="{ width: progressPercent + '%' }"></div>
                    </div>
                    <div class="text-xs mt-1 text-center" :class="textMutedClass">
                        {{ progressPercent }}% decorrido
                    </div>
                </div>

                <!-- Mensagem quando expirar -->
                <div v-if="isExpired" class="mt-4 w-full">
                    <div class="flex items-center gap-2.5 px-4 py-3 rounded-lg text-sm" :class="alertClass">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Expirado em {{ formatDateBR(expireDateValue) }}</span>
                    </div>
                </div>

                <!-- Slot de ações -->
                <div v-if="$slots.actions" class="mt-4 w-full flex justify-center">
                    <slot name="actions" :is-expired="isExpired" :time-left="timeLeft"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
    currentDate: {
        type: [Date, String, Number],
        default: () => new Date()
    },
    expireDate: {
        type: [Date, String, Number],
        required: true
    },
    title: {
        type: String,
        default: ''
    },
    size: {
        type: String,
        default: 'md'
    },
    showProgress: {
        type: Boolean,
        default: true
    },
    variant: {
        type: String,
        default: 'default' // 'default' | 'dark'
    }
})

const emit = defineEmits(['expire', 'tick'])

const timeLeft = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 })
const isExpired = ref(false)
let timerInterval = null

// ====== PARSER DE DATA BRASILEIRA ======
const parseBRDate = (val) => {
    if (val instanceof Date) return val
    if (typeof val === 'number') return new Date(val)

    if (typeof val === 'string') {
        const brPattern = /^(\d{2})\/(\d{2})\/(\d{4})(?:\s+(\d{2}):(\d{2}))?$/
        const match = val.trim().match(brPattern)

        if (match) {
            const [, day, month, year, hour = '00', minute = '00'] = match
            return new Date(
                parseInt(year),
                parseInt(month) - 1,
                parseInt(day),
                parseInt(hour),
                parseInt(minute),
                0, 0
            )
        }

        const parsed = new Date(val)
        if (!isNaN(parsed.getTime())) return parsed
    }

    return new Date()
}

// ====== COMPUTEDS DE ESTILO (TAILWIND PURO) ======
const isDark = computed(() => props.variant === 'dark')

const cardBgClass = computed(() =>
    isDark.value
        ? 'bg-slate-900 border-slate-700'
        : 'bg-white border-slate-200 dark:bg-slate-900 dark:border-slate-700'
)

const textClass = computed(() =>
    isDark.value
        ? 'text-white'
        : 'text-slate-900 dark:text-white'
)

const textMutedClass = computed(() =>
    isDark.value
        ? 'text-slate-400'
        : 'text-slate-500 dark:text-slate-400'
)

const numberBgClass = computed(() =>
    isDark.value
        ? 'bg-slate-800'
        : 'bg-slate-100 dark:bg-slate-800'
)

const progressTrackClass = computed(() =>
    isDark.value
        ? 'bg-slate-800'
        : 'bg-slate-100 dark:bg-slate-800'
)

const progressFillClass = computed(() =>
    isDark.value
        ? 'bg-cyan-400'
        : 'bg-cyan-500 dark:bg-cyan-400'
)

const alertClass = computed(() =>
    isDark.value
        ? 'bg-red-900/30 text-red-300 border border-red-800'
        : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800'
)

const maxWidthClass = computed(() => {
    const sizes = { sm: 'max-w-xs', md: 'max-w-md', lg: 'max-w-lg', xl: 'max-w-xl' }
    return sizes[props.size] || sizes.md
})

const boxSizeClass = computed(() => {
    const sizes = {
        sm: 'w-12 h-12 text-lg',
        md: 'w-16 h-16 sm:w-20 sm:h-20 text-2xl sm:text-3xl',
        lg: 'w-20 h-20 sm:w-24 sm:h-24 text-3xl sm:text-4xl',
        xl: 'w-24 h-24 sm:w-32 sm:h-32 text-4xl sm:text-5xl'
    }
    return sizes[props.size] || sizes.md
})

// ====== DATAS ======
const currentDateValue = computed(() => parseBRDate(props.currentDate))
const expireDateValue = computed(() => parseBRDate(props.expireDate))

// ====== CÁLCULO DO TEMPO ======
const calculateTimeLeft = () => {
    const now = new Date().getTime()
    const end = expireDateValue.value.getTime()
    const diff = end - now

    if (diff <= 0) {
        timeLeft.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
        if (!isExpired.value) {
            isExpired.value = true
            emit('expire')
        }
        return
    }

    isExpired.value = false

    const days = Math.floor(diff / (1000 * 60 * 60 * 24))
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
    const seconds = Math.floor((diff % (1000 * 60)) / 1000)

    timeLeft.value = { days, hours, minutes, seconds }
    emit('tick', timeLeft.value)
}

const pad = (n) => String(n).padStart(2, '0')

// ====== BARRA DE PROGRESSO ======
const totalDuration = computed(() =>
    expireDateValue.value.getTime() - currentDateValue.value.getTime()
)

const elapsed = computed(() =>
    new Date().getTime() - currentDateValue.value.getTime()
)

const progressPercent = computed(() => {
    const total = totalDuration.value
    if (total <= 0) return 100
    return Math.min(Math.max(Math.round((elapsed.value / total) * 100), 0), 100)
})

// ====== FORMATADOR ======
const formatDateBR = (date) => {
    if (!date || isNaN(date.getTime())) return '--/--/---- --:--'
    const d = date
    return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()} ${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`
}

// ====== LIFECYCLE ======
const startTimer = () => {
    calculateTimeLeft()
    timerInterval = setInterval(calculateTimeLeft, 1000)
}

onMounted(() => startTimer())
onUnmounted(() => { if (timerInterval) clearInterval(timerInterval) })

watch(() => [props.currentDate, props.expireDate], () => {
    if (timerInterval) clearInterval(timerInterval)
    startTimer()
}, { deep: true })
</script>
