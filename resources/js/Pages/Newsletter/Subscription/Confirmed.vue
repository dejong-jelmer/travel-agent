<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
    <NewsletterTemplate
        :title="t('newsletter_subscription.confirmed.title')"
        :message="t('newsletter_subscription.confirmed.message')"
        :box-title="t('newsletter_subscription.confirmed.box_title')"
    >
        <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" fill="none"
                class="mx-auto w-20 h-20">
                <circle cx="40" cy="40" r="40" class="fill-status-success/10" />
                <circle cx="40" cy="40" r="32" class="fill-status-success/20" />
                <path d="M25 40L35 50L55 30" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                    stroke-linejoin="round" class="text-status-success" />
            </svg>
        </template>

        <template #list-items>
            <li class="flex items-start">
                <span class="text-status-success font-bold mr-2">✓</span>
                <span class="text-brand-primary">
                    {{ t('newsletter_subscription.confirmed.updates') }}
                </span>
            </li>
            <li class="flex items-start">
                <span class="text-status-success font-bold mr-2">✓</span>
                <span class="text-brand-primary">
                    {{ t('newsletter_subscription.confirmed.exclusive_content') }}
                </span>
            </li>
            <li class="flex items-start">
                <span class="text-status-success font-bold mr-2">✓</span>
                <span class="text-brand-primary">
                    {{ t('newsletter_subscription.confirmed.unsubscribe_anytime') }}
                </span>
            </li>
        </template>
    </NewsletterTemplate>
</template>
