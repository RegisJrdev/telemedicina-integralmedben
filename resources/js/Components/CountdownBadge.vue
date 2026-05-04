<template>
    <div class="inline-flex items-center gap-1.5">
        <!-- Badge com dias restantes -->
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium tabular-nums"
            :class="badgeClass">
            <span class="w-1.5 h-1.5 rounded-full"
                :class="isExpired ? 'bg-red-500' : 'bg-emerald-500 animate-pulse'"></span>
            {{ displayText }}
        </span>

        <!-- Tooltip com contador completo (hover) -->
        <div class="group relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400 cursor-help" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <!-- Tooltip -->
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-50">
                <div class="rounded-lg border shadow-xl p-3 whitespace-nowrap" :class="tooltipBgClass">
                    <div class="text-xs font-medium mb-1.5" :class="tooltipTextClass">
                        {{ isExpired ? 'Expirado em:' : 'Expira em:' }}
                    </div>
                    <div class="flex items-center gap-1.5 text-sm font-mono font-bold" :class="tooltipTextClass">
                        <span v-if="timeLeft.days > 0">{{ pad(timeLeft.days) }}d</span>
                        <span>{{ pad(timeLeft.hours) }}h</span>
                        <span>{{ pad(timeLeft.minutes) }}m</span>
                        <span>{{ pad(timeLeft.seconds) }}s</span>
                    </div>
                    <div class="text-[10px] mt-1" :class="tooltipMutedClass">
                        {{ formatDateBR(expireDateValue) }}
                    </div>
                    <!-- Setinha do tooltip -->
                    <div class="absolute top-full left-1/2 -translate-x-1/2 w-2 h-2 rotate-45"
                        :class="tooltipArrowClass"></div>
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
    variant: {
        type: String,
        default: 'default' // 'default' | 'dark'
    }
})

const emit = defineEmits(['expire'])

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
            return new Date(parseInt(year), parseInt(month) - 1, parseInt(day), parseInt(hour), parseInt(minute), 0, 0)
        }

        const parsed = new Date(val)
        if (!isNaN(parsed.getTime())) return parsed
    }

    return new Date()
}

const currentDateValue = computed(() => parseBRDate(props.currentDate))
const expireDateValue = computed(() => parseBRDate(props.expireDate))

// ====== ESTILOS ======
const isDark = computed(() => props.variant === 'dark')

const badgeClass = computed(() => {
    if (isExpired.value) {
        return isDark.value
            ? 'bg-red-900/40 text-red-300 border border-red-800'
            : 'bg-red-50 text-red-700 border border-red-200'
    }
    if (timeLeft.value.days <= 1) {
        return isDark.value
            ? 'bg-amber-900/40 text-amber-300 border border-amber-800'
            : 'bg-amber-50 text-amber-700 border border-amber-200'
    }
    return isDark.value
        ? 'bg-emerald-900/40 text-emerald-300 border border-emerald-800'
        : 'bg-emerald-50 text-emerald-700 border border-emerald-200'
})

const tooltipBgClass = computed(() =>
    isDark.value ? 'bg-slate-800 border-slate-700' : 'bg-white border-slate-200'
)
const tooltipTextClass = computed(() =>
    isDark.value ? 'text-white' : 'text-slate-900'
)
const tooltipMutedClass = computed(() =>
    isDark.value ? 'text-slate-400' : 'text-slate-500'
)
const tooltipArrowClass = computed(() =>
    isDark.value ? 'bg-slate-800 border-b border-r border-slate-700' : 'bg-white border-b border-r border-slate-200'
)

// ====== TEXTO DO BADGE ======
const displayText = computed(() => {
    if (isExpired.value) return 'Expirado'
    if (timeLeft.value.days > 0) return `${timeLeft.value.days}d ${timeLeft.value.hours}h restantes`
    if (timeLeft.value.hours > 0) return `${timeLeft.value.hours}h ${timeLeft.value.minutes}m restantes`
    return `${timeLeft.value.minutes}m restantes`
})

// ====== CÁLCULO ======
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
    timeLeft.value = {
        days: Math.floor(diff / (1000 * 60 * 60 * 24)),
        hours: Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
        minutes: Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)),
        seconds: Math.floor((diff % (1000 * 60)) / 1000)
    }
}

const pad = (n) => String(n).padStart(2, '0')

const formatDateBR = (date) => {
    if (!date || isNaN(date.getTime())) return '--/--/----'
    return `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}/${date.getFullYear()} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`
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
