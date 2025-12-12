<script setup>
import { ref } from "vue"
import { useForm } from '@inertiajs/vue3'
import { useToast } from "vue-toastification"
import { LoaderCircle } from "lucide-vue-next"

const honeypot = ref(null)
const toast = useToast()

const form = useForm({
    name: '',
    email: '',
})

function submit() {
    if(!form.isDirty) return;
    form.clearErrors()
    try {
        honeypot.value.validate()
        form.post(route('newsletter.subscription.subscribe'), {
            preserveScroll: true,
            timeout: 10000,
            onSuccess: () => {
                const email = form.email
                form.reset()
                toast.success($t('newsletter.subscription.success', { "email": email }))
            },
            onError: () => {
                toast.error($t('newsletter.subscription.error'))
            }
        })
    } catch (error) { }
}

</script>

<template>
    <div class="max-w-6xl mx-auto px-4 py-12 phone:px-6 laptop:px-8">
        <div class="bg-white rounded-lg p-8 phone:p-10 laptop:p-12 border border-brand-primary">
            <div class="text-center mb-8">
                <h2 class="text-2xl phone:text-3xl laptop:text-4xl font-semibold text-brand-primary mb-4">
                    {{ $t('newsletter.heading') }}
                </h2>
                <p class="text-base phone:text-lg text-black leading-relaxed">
                    {{ $t('newsletter.description') }}
                </p>
            </div>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-1 tablet:grid-cols-2 gap-x-6">
                    <Input v-model="form.name" type="text" name="name" :placeholder="$t('newsletter.form.name_placeholder')"
                        :feedback="form.errors.name" @change="form.clearErrors('name')" />
                    <Input v-model="form.email" type="email" name="email" :placeholder="$t('newsletter.form.email_placeholder')"
                        :feedback="form.errors.email" @change="form.clearErrors('email')" />
                    <!-- Setup the honeypot -->
                    <vue-honeypot ref="honeypot" />
                </div>
                <Button :disabled="form.processing" class="w-full" color="primary">
                    <span class="flex justify-center space-x-2">
                        <LoaderCircle v-if="form.processing" class="size-5 animate-spin" viewBox="0 0 24 24" />
                        <span>{{ form.processing ? $t('newsletter.form.submitting') : $t('newsletter.form.submit') }}</span>
                    </span>
                </Button>
                <p class="text-sm text-brand-primary text-center mt-4">
                    {{ $t('newsletter.privacy') }}
                </p>
            </form>
        </div>
    </div>
</template>
