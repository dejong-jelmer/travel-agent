<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { CircleCheckBig, Phone } from '@lucide/vue'

const props = defineProps({
    trip: {
        type: Object,
        required: true,
    },
})

const { t } = useI18n()
</script>

<template>
    <Layout>
        <section class="min-h-[60vh] py-12 laptop:py-20">
            <div class="max-w-screen-tablet mx-auto px-4 text-center">
                <!-- Success icon -->
                <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-subtle/30 rounded-full mb-8">
                    <CircleCheckBig class="w-10 h-10 text-brand-subtle" />
                </div>

                <!-- Title -->
                <h1 class="text-2xl laptop:text-3xl font-bold text-brand-primary mb-6">
                    {{ t('trip_request.thanks.title') }}
                </h1>

                <!-- Message -->
                <p class="text-base laptop:text-lg text-brand-text leading-relaxed max-w-xl mx-auto mb-8">
                    {{ t('trip_request.thanks.message') }}
                </p>

                <!-- Phone -->
                <div class="flex items-center justify-center gap-2 text-brand-text mb-12">
                    <Phone class="w-5 h-5 text-brand-accent" />
                    <span class="text-sm">
                        {{ t('trip_request.thanks.question_prefix') }}
                        <a :href="'tel:' + $page.props.settings?.contact_phone" class="tel-field font-semibold text-brand-primary hover:text-brand-accent transition-colors">
                        </a>
                    </span>
                </div>

                <!-- Back link -->
                <Link :href="route('trips.index')" class="inline-block">
                    <Button color="primary">
                        {{ t('trip_request.thanks.back_to_trips') }}
                    </Button>
                </Link>
            </div>
        </section>
    </Layout>
</template>
