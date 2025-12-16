<script setup>
import axios from "@/axios";
import { reactive, ref } from "vue";
import { useToast } from "vue-toastification";
import { Phone, AtSign, Pencil } from 'lucide-vue-next';


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
                toast.success($t('forms.contact.success'));
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
        class="grid gap-y-12 laptop:gap-y-24 px-6 tablet:px-24 py-12 tablet:py-24  laptop:border-2 laptop:border-brand-primary/20 laptop:rounded-2xl laptop:shadow-xl backdrop-blur-sm">

        <!-- Header sectie -->
        <div class="text-center">
            <SectionHeader>{{ $t('forms.contact.heading') }}</SectionHeader>
            <div
                class="max-w-2xl mx-auto bg-white backdrop-blur-sm rounded-xl p-6 tablet:p-8 border border-brand-primary/20 shadow-sm">
                <p class="text-left text-base tablet:text-lg text-brand-primary leading-relaxed">
                    {{ $t('forms.contact.intro') }}
                    <br />
                    <span class="inline-flex items-center gap-2 mt-4">
                    <Phone class="w-5 h-5 text-accent-primary" />
                        <span class="hidden tablet:inline-flex">{{ $t('forms.contact.call') }}</span>
                        <span class="font-bold text-brand-primary">
                            <a class="tel-field default-link"
                                href="#">+3112345678</a>
                        </span>
                    </span>
                    <br />
                    <span class="inline-flex items-center gap-2 mt-2">
                        <AtSign class="w-5 h-5 text-accent-primary" />
                        <span class="hidden tablet:inline-flex">{{ $t('forms.contact.email') }}</span>
                        <span class="font-bold text-brand-secondary">
                            <a class="email-field default-link"
                                href="#" v-html="contact.mail.display"></a>
                        </span>
                    </span>
                    <br />
                    <span class="inline-flex items-center gap-2 mt-4 text-brand-primary">
                        <Pencil class="w-5 h-5 text-accent-primary" />
                        {{ $t('forms.contact.or_use_form') }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Formulier sectie -->
        <div id="contact-form" class="max-w-4xl mx-auto scroll-mt-[180px]">
            <form @submit.prevent="submit" @change="resetObject(errors)" class="space-y-8">
                <div
                    class="bg-brand-secondary backdrop-blur-sm rounded-2xl p-6 tablet:p-8 border border-accent-sage/20 shadow-sm">
                    <div class="grid tablet:grid-cols-2 gap-8 tablet:gap-12">
                        <!-- Links: persoonlijke gegevens -->
                        <div class="space-y-6">
                            <div class="border-l-4 border-accent-primary pl-4 mb-6">
                                <h3 class="text-lg font-semibold text-brand-primary">{{ $t('forms.contact.your_details_heading') }}</h3>
                                <p class="text-sm text-brand-primary/60">{{ $t('forms.contact.your_details_subheading') }}</p>
                            </div>

                            <Input type="text" name="name" :label="$t('forms.contact.name_label')" :required="false" :show-label="false"
                                v-model="form.name" :feedback="errors?.name"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02]" />
                            <Input type="email" name="email" :label="$t('forms.contact.email_label')" :required="false" :show-label="false"
                                v-model="form.email" :feedback="errors?.email"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02]" />
                            <Input type="phone" name="phone" :label="$t('forms.contact.phone_label')"
                                :required="false" :show-label="false" v-model="form.phone"
                                :feedback="errors?.phone"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02]" />
                        </div>

                        <!-- Rechts: bericht -->
                        <div class="space-y-6">
                            <div class="border-l-4 border-accent-primary pl-4 mb-6">
                                <h3 class="text-lg font-semibold text-brand-primary">{{ $t('forms.contact.your_message_heading') }}</h3>
                                <p class="text-sm text-brand-primary/60">{{ $t('forms.contact.your_message_subheading') }}</p>
                            </div>

                            <TextArea name="text" :label="$t('forms.contact.message_label')" :required="false" :show-label="false" v-model="form.text"
                                :feedback="errors?.text"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02] h-auto" />
                        </div>
                    </div>
                </div>

                <VueHoneypot ref="honeypot" />

                <!-- Submit button -->
                <div class="flex justify-center pt-6">
                    <Button>
                        {{ $t('forms.contact.submit_button') }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
