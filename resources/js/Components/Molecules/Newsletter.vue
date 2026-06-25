<script setup>
import { ref } from "vue"
import { useForm } from '@inertiajs/vue3'
import { useToast } from "vue-toastification"
import { LoaderCircle } from "lucide-vue-next"
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const honeypot = ref(null)
const toast = useToast()
const alreadySubscribed = ref(false)

const form = useForm({
    name: '',
    email: '',
})

function submit() {
    if (!form.isDirty) return;
    form.clearErrors()
    try {
        honeypot.value.validate()
        form.post(route('newsletter.subscription.subscribe'), {
            preserveScroll: true,
            timeout: 10000,
            onSuccess: () => {
                alreadySubscribed.value = false
                const email = form.email
                form.reset()
                toast.success(t('newsletter.subscription.success', { "email": email }))
            },
            onError: (errors) => {
                if (errors.already_subscribed) {
                    alreadySubscribed.value = true
                } else {
                    toast.error(t('newsletter.subscription.error'))
                }
            }
        })
    } catch (error) { }
}

</script>

<template>
    <section id="nieuwsbrief" class="bg-brand-secondary scroll-mt-24">
        <div class="max-w-6xl mx-auto px-4 py-12 phone:px-6 laptop:px-8 laptop:py-24">
            <div
                class="grid grid-cols-1 tablet:grid-cols-[1.15fr_1fr] gap-8 laptop:gap-12 tablet:items-end mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <span class="block w-10 h-0.5 bg-brand-accent"></span>
                        <span class="text-xs font-semibold tracking-[0.22em] uppercase text-brand-accent">
                            {{ $t('newsletter.eyebrow') }}
                        </span>
                    </div>
                    <h2
                        class="font-bold text-brand-primary leading-[1.05] tracking-[-0.01em] text-4xl tablet:text-5xl laptop:text-[56px]">
                        {{ $t('newsletter.heading') }}
                    </h2>
                </div>
                <p class="text-lg laptop:text-xl text-gray-600 leading-relaxed max-w-md tablet:pb-2">
                    {{ $t('newsletter.description') }}
                </p>
            </div>

            <form @submit.prevent="submit">
                <div class="border-l-[3px] border-brand-accent pl-5 mb-6">
                    <h3 class="text-xl font-semibold text-brand-primary mb-1">
                        {{ $t('newsletter.form.heading') }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ $t('newsletter.form.note') }}
                    </p>
                </div>

                <!-- velden -->
                <div class="grid grid-cols-1 tablet:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                    <Input v-model="form.name" type="text" name="name"
                        :placeholder="$t('newsletter.form.name_placeholder')" :feedback="form.errors.name"
                        @change="form.clearErrors('name')" />
                    <Input v-model="form.email" type="email" name="email"
                        :placeholder="$t('newsletter.form.email_placeholder')" :feedback="form.errors.email"
                        @change="form.clearErrors('email'); alreadySubscribed = false" />
                </div>
                <!-- Setup the honeypot -->
                <vue-honeypot ref="honeypot" />

                <!-- onderste rij: privacy links, knop rechts -->
                <div class="flex flex-col-reverse gap-6 tablet:flex-row tablet:items-center tablet:justify-between">
                    <i18n-t keypath="newsletter.privacy" tag="p" class="text-sm text-gray-500 max-w-xl">
                        <template #link>
                            <DefaultLink :href="route('privacy')" class="underline hover:text-gray-700">
                                {{ $t('newsletter.privacy_link') }}
                            </DefaultLink>
                        </template>
                    </i18n-t>

                    <Button :disabled="form.processing" color="accent" class="w-full tablet:w-auto whitespace-nowrap">
                        <span class="flex items-center justify-center gap-2">
                            <LoaderCircle v-if="form.processing" class="size-5 animate-spin" viewBox="0 0 24 24" />
                            <span>{{ form.processing ? $t('newsletter.form.submitting') : $t('newsletter.form.submit') }}</span>
                            <span v-if="!form.processing" aria-hidden="true">&rarr;</span>
                        </span>
                    </Button>
                </div>

                <div v-if="alreadySubscribed" class="flex tablet:justify-end mt-4">
                    <Pill type="success" variant="transparent">
                        {{ $t('newsletter.subscription.already_subscribed') }}
                    </Pill>
                </div>
            </form>

        </div>
    </section>
</template>
