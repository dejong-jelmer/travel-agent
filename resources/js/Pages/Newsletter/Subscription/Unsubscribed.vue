<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
    <NewsletterTemplate
        :title="t('newsletter_subscription.unsubscribed.title')"
        :message="t('newsletter_subscription.unsubscribed.message')"
        :box-title="t('newsletter_subscription.unsubscribed.box_title')"
    >
        <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" fill="none"
                class="mx-auto w-20 h-20">
                <circle cx="40" cy="40" r="40" class="fill-status-warning/10" />
                <circle cx="40" cy="40" r="32" class="fill-status-warning/20" />
                <path d="M30 30L50 50M50 30L30 50" stroke="currentColor" stroke-width="4"
                    stroke-linecap="round" stroke-linejoin="round" class="text-status-warning" />
            </svg>
        </template>

        <template #list-items>
            <li class="flex items-center gap-2">
                <span class="w-2 h-2 bg-accent-primary rounded-full flex-shrink-0"></span>
                <span class="text-brand-primary">
                    {{ t('newsletter_subscription.unsubscribed.no_more_emails') }}
                </span>
            </li>
            <li class="flex items-center gap-2">
                <span class="w-2 h-2 bg-accent-primary rounded-full flex-shrink-0"></span>
                <span class="text-brand-primary">
                    {{ t('newsletter_subscription.unsubscribed.resubscribe_anytime') }}
                </span>
            </li>
            <li class="flex items-center gap-2">
                <span class="w-2 h-2 bg-accent-primary rounded-full flex-shrink-0"></span>
                <span class="text-brand-primary">
                    {{ t('newsletter_subscription.unsubscribed.contact_us') }}
                </span>
            </li>
        </template>
    </NewsletterTemplate>
</template>
