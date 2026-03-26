<script setup>
import { ref } from "vue"
import { useForm } from '@inertiajs/vue3'
import { useToast } from "vue-toastification"
import { LoaderCircle } from "lucide-vue-next"
import i18n from '@/plugins/i18n.js';

const t = (key, params) => i18n.global.t(key, params);

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
<<<<<<< HEAD
            onError: (errors) => {
                if (errors.already_subscribed) {
                    alreadySubscribed.value = true
                } else {
                    toast.error(t('newsletter.subscription.error'))
                }
=======
            onError: () => {
                toast.error(t('newsletter.subscription.error'))
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
            }
        })
    } catch (error) { }
}

</script>

<template>
    <div class="max-w-6xl mx-auto px-4 py-12 phone:px-6 laptop:px-8">
        <div class="bg-white rounded-lg p-8 phone:p-10 laptop:p-12 border border-brand-secondary shadow-sm">
            <div class="text-center mb-8">
                <SectionHeader class="py-0 pb-4">
                    {{ $t('newsletter.heading') }}
                </SectionHeader>
                <p class="text-left text-sm laptop:text-base text-gray-600 leading-relaxed">
                    {{ $t('newsletter.description') }}
                </p>
            </div>
            <form @submit.prevent="submit" class="space-y-8">
                <div class="grid grid-cols-1 tablet:grid-cols-2 gap-x-6 gap-y-4">
                    <Input v-model="form.name" type="text" name="name"
                        :placeholder="$t('newsletter.form.name_placeholder')" :feedback="form.errors.name"
                        @change="form.clearErrors('name')" />
                    <Input v-model="form.email" type="email" name="email"
                        :placeholder="$t('newsletter.form.email_placeholder')" :feedback="form.errors.email"
<<<<<<< HEAD
                        @change="form.clearErrors('email'); alreadySubscribed = false" />
=======
                        @change="form.clearErrors('email')" />
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
                    <!-- Setup the honeypot -->
                    <vue-honeypot ref="honeypot" />
                </div>
                <div class="w-full flex justify-center">

                    <Button :disabled="form.processing" class="w-auto" color="accent">
                        <span class="flex justify-center space-x-2">
                            <LoaderCircle v-if="form.processing" class="size-5 animate-spin" viewBox="0 0 24 24" />
                            <span>{{ form.processing ? $t('newsletter.form.submitting') : $t('newsletter.form.submit')
                                }}</span>
                        </span>
                    </Button>
                </div>
<<<<<<< HEAD
                <div v-if="alreadySubscribed" class="flex justify-center mt-4">
                    <Pill type="success" variant="transparent">
                        {{ $t('newsletter.subscription.already_subscribed') }}
                    </Pill>
                </div>
                <i18n-t keypath="newsletter.privacy" tag="p" class="text-sm text-gray-500 text-center mt-4">
                    <template #link>
                        <DefaultLink :href="route('privacy')" class="underline hover:text-gray-700">
                            {{ $t('newsletter.privacy_link') }}
                        </DefaultLink>
                    </template>
                </i18n-t>
=======
                <p class="text-sm text-gray-500 text-center mt-4">
                    {{ $t('newsletter.privacy') }}
                </p>
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
            </form>
        </div>
    </div>
</template>
