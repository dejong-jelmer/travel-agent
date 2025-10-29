<script setup>
import axios from "@/axios";
import { reactive, ref } from "vue";
import { useToast } from "vue-toastification";

const props = defineProps({
    contact: Object,
});

const errors = reactive({});
const form = reactive({
    name: "",
    email: "",
    phone: "",
    text: "",
});
const honeypot = ref(null);
const toast = useToast();

function resetObject(obj) {
    Object.keys(obj).forEach((key) => delete obj[key]);
}

function submit() {
    try {
        honeypot.value.validate();
        resetObject(errors);
        axios
            .post(route("contact"), form)
            .then((response) => {
                toast.success("Bedankt! Uw bericht is succesvol verstuurd.");
                resetObject(form);
            })
            .catch((error) => {
                if (error.response && error.response.data && error.response.data.errors) {
                    Object.assign(errors, error.response.data.errors);
                }
            });
    } catch (error) { }
}
</script>

<template>
    <div
        class="px-6 tablet:px-24 py-12 tablet:py-24 bg-gradient-to-br from-neutral-50 to-nature-sage/10 laptop:border-2 laptop:border-nature-sage/30 laptop:rounded-2xl laptop:shadow-xl backdrop-blur-sm">

        <!-- Header sectie -->
        <div class="text-center mb-12 tablet:mb-16">
            <SectionHeader>
                Contact
            </SectionHeader>
            <div
                class="max-w-2xl mx-auto bg-white/70 backdrop-blur-sm rounded-xl p-6 tablet:p-8 border border-nature-sage/20 shadow-sm">
                <p class="text-left text-base tablet:text-lg text-brand-primary leading-relaxed">
                    Als je vragen hebt of meer wilt weten neem contact op via één van de onderstaaden methoden.
                    <br />
                    <span class="inline-flex items-center gap-2 mt-4">
                        <svg class="w-5 h-5 text-ui-gold" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                        </svg>
                        <span class="hidden tablet:inline-flex">Bel</span>
                        <span class="font-bold text-ui-gold">

                            <a class="tel-field text-nature-terracotta hover:text-brand-dark font-medium underline decoration-ui-gold/30 hover:decoration-ui-gold transition-colors duration-300"
                                href="#">+3112345678</a>
                        </span>
                    </span>
                    <br />
                    <span class="inline-flex items-center gap-2 mt-2">
                        <svg class="w-5 h-5 text-ui-gold" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                        <span class="hidden tablet:inline-flex">Stuur een mail naar</span>
                        <span class="font-bold text-ui-gold">
                            <a class="email-field text-nature-terracotta hover:text-brand-dark font-medium underline decoration-ui-gold/30 hover:decoration-ui-gold transition-colors duration-300"
                                href="#" v-html="contact.mail.display"></a>
                        </span>
                    </span>
                    <br />
                    <span class="inline-flex items-center gap-2 mt-4 text-brand-dark">
                        <svg class="w-5 h-5 text-ui-gold" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        Of&nbsp;gebruik&nbsp;het
                        <a href="#contact-form"
                            class="text-nature-terracotta hover:text-brand-dark font-medium underline decoration-ui-gold/30 hover:decoration-ui-gold transition-colors duration-300">contactformulier</a>
                        <span class="hidden tablet:inline-flex">hieronder.</span>
                    </span>
                </p>
            </div>
        </div>

        <!-- Formulier sectie -->
        <div id="contact-form" class="max-w-4xl mx-auto scroll-mt-[180px]">
            <form @submit.prevent="submit" @change="resetObject(errors)" class="space-y-8">
                <div
                    class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 tablet:p-8 border border-nature-sage/20 shadow-sm">
                    <div class="grid tablet:grid-cols-2 gap-8 tablet:gap-12">
                        <!-- Links: persoonlijke gegevens -->
                        <div class="space-y-6">
                            <div class="border-l-4 border-ui-gold pl-4 mb-6">
                                <h3 class="text-lg font-semibold text-brand-dark">Uw gegevens</h3>
                                <p class="text-sm text-ui-blue">Hoe kunnen we u bereiken?</p>
                            </div>

                            <Input type="text" name="name" label="Naam" :required="false" :show-label="false"
                                v-model="form.name" :feedback="errors?.name"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02]" />
                            <Input type="email" name="email" label="Uw Emailadres" :required="false" :show-label="false"
                                v-model="form.email" :feedback="errors?.email"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02]" />
                            <Input type="phone" name="phone" label="Uw telefoonnummer (optioneel)"
                                :required="false" :show-label="false" v-model="form.phone"
                                :feedback="errors?.phone"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02]" />
                        </div>

                        <!-- Rechts: bericht -->
                        <div class="space-y-6">
                            <div class="border-l-4 border-ui-gold pl-4 mb-6">
                                <h3 class="text-lg font-semibold text-brand-dark">Uw bericht</h3>
                                <p class="text-sm text-ui-blue">Waarmee kunnen we u helpen?</p>
                            </div>

                            <TextArea name="text" label="Bericht" :required="false" :show-label="false" v-model="form.text"
                                :feedback="errors?.text"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02] h-full" />
                        </div>
                    </div>
                </div>

                <VueHoneypot ref="honeypot" />

                <!-- Submit button -->
                <div class="flex justify-center pt-6">
                    <Button>
                        Verstuur bericht
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
