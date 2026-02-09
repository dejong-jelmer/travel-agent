<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import {
    CalendarDaysIcon,
    CheckCircleIcon,
    ClockIcon,
    SparklesIcon,
    ArrowTrendingUpIcon,
    UserGroupIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    bookings: Object,
    systemHealth: Object
})

const { t } = useI18n()

// Helper functies voor system health
const getStatusColor = (status) => {
    switch (status) {
        case 'healthy':
            return 'bg-status-success'
        case 'warning':
            return 'bg-status-warning'
        case 'error':
            return 'bg-status-error'
        default:
            return 'bg-gray-400'
    }
}

const getStatusTextColor = (status) => {
    switch (status) {
        case 'healthy':
            return 'text-status-success'
        case 'warning':
            return 'text-status-warning'
        case 'error':
            return 'text-status-error'
        default:
            return 'text-gray-500'
    }
}

const getStatusLabel = (status) => {
    switch (status) {
        case 'healthy':
            return t('admin.dashboard.system_status.status_labels.healthy')
        case 'warning':
            return t('admin.dashboard.system_status.status_labels.warning')
        case 'error':
            return t('admin.dashboard.system_status.status_labels.error')
        default:
            return t('admin.dashboard.system_status.status_labels.unknown')
    }
}

// System health items met labels
const systemHealthItems = computed(() => [
    {
        id: 'database',
        label: t('admin.dashboard.system_status.services.database'),
        data: props.systemHealth?.database,
        details: props.systemHealth?.database?.responseTime
            ? t('admin.dashboard.system_status.details.response', { time: props.systemHealth.database.responseTime })
            : null
    },
    {
        id: 'email',
        label: t('admin.dashboard.system_status.services.email'),
        data: props.systemHealth?.email,
        details: props.systemHealth?.email?.provider
            ? t('admin.dashboard.system_status.details.provider', { provider: props.systemHealth.email.provider })
            : null
    },
    {
        id: 'queue',
        label: t('admin.dashboard.system_status.services.queue'),
        data: props.systemHealth?.queue,
        details: props.systemHealth?.queue?.pendingJobs !== null
            ? t('admin.dashboard.system_status.details.jobs_in_queue', { count: props.systemHealth.queue.pendingJobs })
            : null
    }
])

// Format timestamp
const lastCheckedTime = computed(() => {
    if (!props.systemHealth?.lastChecked) return null
    return new Date(props.systemHealth.lastChecked).toLocaleTimeString('nl-NL', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    })
})

// Dashboard statistieken
const stats = computed(() => [
    {
        id: 1,
        name: t('admin.dashboard.stats.new.name'),
        value: props.bookings.new,
        description: t('admin.dashboard.stats.new.description'),
        icon: SparklesIcon,
        iconColor: 'text-accent-primary',
        bgColor: 'bg-accent-primary/10',
        link: route('admin.bookings.index'),
        change: null,
    },
    {
        id: 2,
        name: t('admin.dashboard.stats.upcoming_month.name'),
        value: props.bookings.upcomingMonth,
        description: t('admin.dashboard.stats.upcoming_month.description'),
        icon: CalendarDaysIcon,
        iconColor: 'text-accent-sage',
        bgColor: 'bg-accent-sage/10',
        link: route('admin.bookings.index'),
        change: null,
    },
    {
        id: 3,
        name: t('admin.dashboard.stats.upcoming.name'),
        value: props.bookings.upcoming,
        description: t('admin.dashboard.stats.upcoming.description'),
        icon: ClockIcon,
        iconColor: 'text-accent-link',
        bgColor: 'bg-accent-link/10',
        link: route('admin.bookings.index'),
        change: null,
    },
    {
        id: 4,
        name: t('admin.dashboard.stats.total.name'),
        value: props.bookings.all,
        description: t('admin.dashboard.stats.total.description'),
        icon: UserGroupIcon,
        iconColor: 'text-brand-primary',
        bgColor: 'bg-brand-primary/10',
        link: route('admin.bookings.index'),
        change: null,
    },
])
</script>

<template>
    <Admin>
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-brand-primary mb-2">{{ t('admin.dashboard.header.title') }}</h1>
            <p class="text-brand-light">{{ t('admin.dashboard.header.welcome') }}</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <component
                v-for="stat in stats"
                :key="stat.id"
                :is="stat.link ? Link : 'div'"
                :href="stat.link"
                class="relative overflow-hidden rounded-2xl bg-white shadow-sm hover:shadow-xl transition-all duration-300 group cursor-pointer border border-gray-100"
            >
                <!-- Gradient overlay on hover -->
                <div class="absolute inset-0 bg-gradient-to-br from-transparent to-gray-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <div class="relative px-6 py-6">
                    <!-- Icon -->
                    <div class="flex items-center justify-between mb-4">
                        <div :class="[stat.bgColor, 'rounded-xl p-3 transition-transform duration-300 group-hover:scale-110']">
                            <component
                                :is="stat.icon"
                                :class="[stat.iconColor, 'h-6 w-6']"
                                aria-hidden="true"
                            />
                        </div>
                    </div>

                    <!-- Value -->
                    <div class="mb-2">
                        <p class="text-4xl font-bold text-brand-primary group-hover:text-brand-primary transition-colors">
                            {{ stat.value }}
                        </p>
                    </div>

                    <!-- Label -->
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-1">
                            {{ stat.name }}
                        </p>
                        <p class="text-xs text-brand-light">
                            {{ stat.description }}
                        </p>
                    </div>
                </div>
            </component>
        </div>

        <!-- Quick Actions Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activity Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-brand-primary to-brand-primary px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <ClockIcon class="h-5 w-5 mr-2" />
                        {{ t('admin.dashboard.quick_actions.title') }}
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <Link
                        :href="route('admin.bookings.index')"
                        class="flex items-center justify-between p-4 rounded-xl hover:bg-gray-50 transition-colors group"
                    >
                        <div class="flex items-center">
                            <div class="bg-accent-primary/10 rounded-lg p-2 mr-3">
                                <CalendarDaysIcon class="h-5 w-5 text-accent-primary" />
                            </div>
                            <span class="font-medium text-gray-900 group-hover:text-brand-primary transition-colors">
                                {{ t('admin.dashboard.quick_actions.view_bookings') }}
                            </span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-brand-primary group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>

                    <Link
                        :href="route('admin.trips.index')"
                        class="flex items-center justify-between p-4 rounded-xl hover:bg-gray-50 transition-colors group"
                    >
                        <div class="flex items-center">
                            <div class="bg-accent-sage/10 rounded-lg p-2 mr-3">
                                <svg class="h-5 w-5 text-accent-sage" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900 group-hover:text-brand-primary transition-colors">
                                {{ t('admin.dashboard.quick_actions.manage_trips') }}
                            </span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-brand-primary group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>

                    <Link
                        :href="route('admin.destinations.index')"
                        class="flex items-center justify-between p-4 rounded-xl hover:bg-gray-50 transition-colors group"
                    >
                        <div class="flex items-center">
                            <div class="bg-accent-link/10 rounded-lg p-2 mr-3">
                                <svg class="h-5 w-5 text-accent-link" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900 group-hover:text-brand-primary transition-colors">
                                {{ t('admin.dashboard.quick_actions.manage_destinations') }}
                            </span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-brand-primary group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>
            </div>

            <!-- System Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-accent-sage  px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <CheckCircleIcon class="h-5 w-5 mr-2" />
                        {{ t('admin.dashboard.system_status.title') }}
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Status Items (dynamic) -->
                    <div
                        v-for="item in systemHealthItems"
                        :key="item.id"
                        class="flex items-start justify-between p-3 rounded-lg transition-colors"
                        :class="{
                            'bg-status-success/5': item.data?.status === 'healthy',
                            'bg-status-warning/5': item.data?.status === 'warning',
                            'bg-status-error/5': item.data?.status === 'error',
                            'bg-gray-50': !item.data
                        }"
                    >
                        <div class="flex items-start flex-1">
                            <!-- Status indicator dot -->
                            <div
                                class="h-3 w-3 rounded-full mr-3 mt-0.5 flex-shrink-0"
                                :class="getStatusColor(item.data?.status)"
                            ></div>

                            <!-- Service info -->
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-700 block">
                                    {{ item.label }}
                                </span>

                                <!-- Details (response time, job count, etc) -->
                                <span
                                    v-if="item.details"
                                    class="text-xs text-gray-500 block mt-1"
                                >
                                    {{ item.details }}
                                </span>

                                <!-- Error/warning message -->
                                <span
                                    v-if="item.data?.message && item.data?.status !== 'healthy'"
                                    class="text-xs mt-1 block"
                                    :class="getStatusTextColor(item.data?.status)"
                                >
                                    {{ item.data.message }}
                                </span>
                            </div>
                        </div>

                        <!-- Status label -->
                        <span
                            class="text-xs font-semibold ml-3 flex-shrink-0"
                            :class="getStatusTextColor(item.data?.status)"
                        >
                            {{ getStatusLabel(item.data?.status) }}
                        </span>
                    </div>

                    <!-- Info message -->
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-xs text-brand-light leading-relaxed">
                            <span v-if="lastCheckedTime">
                                {{ t('admin.dashboard.system_status.last_checked', { time: lastCheckedTime }) }}
                            </span>
                            <span v-else>
                                {{ t('admin.dashboard.system_status.loading') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </Admin>
</template>
