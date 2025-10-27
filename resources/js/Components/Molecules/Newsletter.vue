<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3'
import { useToast } from "vue-toastification";

const honeypot = ref(null);
const toast = useToast();

const form = useForm({
    name: '',
    email: '',
})

function submit() {
    form.clearErrors()
    try {
        honeypot.value.validate()
        form.post(route('newsletter.subscribe'), {
            preserveScroll: true,
            timeout: 10000,
            onSuccess: () => {
                const email = form.email
                form.reset()
                toast.success(`Bedankt voor je inschrijving! Bevestig je aanmelding via de e-mail die we zojuist hebben verstuurd naar ${email}.`)
            }
        })
    } catch (error) { }
}

</script>

<template>
    <div class="max-w-4xl mx-auto px-4 py-12 phone:px-6 laptop:px-8">
        <div class="bg-neutral-50 rounded-lg p-8 phone:p-10 laptop:p-12 border border-secondary-stone/20">
            <div class="text-center mb-8">
                <h2 class="text-2xl phone:text-3xl laptop:text-4xl font-semibold text-primary-default mb-4">
                    Blijf op de hoogte
                </h2>
                <p class="text-base phone:text-lg text-primary-default/70 leading-relaxed">
                    Schrijf je in voor onze nieuwsbrief en ontvang alle nieuwste updates over onze reizen.
                </p>
            </div>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-2 gap-x-6">
                    <Input v-model="form.name" type="text" name="name" placeholder="Uw voornaam (optioneel)"
                        :feedback="form.errors.name" @change="form.clearErrors('name')" />
                    <Input v-model="form.email" type="email" name="email" placeholder="uw.email@voorbeeld.nl"
                        :feedback="form.errors.email" @change="form.clearErrors('email')" />
                    <!-- Setup the honeypot -->
                    <vue-honeypot ref="honeypot" />
                </div>
                <Button :disabled="form.processing" class="w-full">
                    <span class="flex justify-center space-x-2">
                        <Spinner v-if="form.processing" class="size-5 animate-spin" viewBox="0 0 24 24" />
                        <span>{{ form.processing ? 'Bezig met verzenden...' : 'Inschrijven' }}</span>
                    </span>
                </Button>
                <p class="text-sm text-primary-default/60 text-center mt-4">
                    We respecteren uw privacy. U kunt zich op elk moment uitschrijven.
                </p>
            </form>
        </div>
    </div>
</template>
