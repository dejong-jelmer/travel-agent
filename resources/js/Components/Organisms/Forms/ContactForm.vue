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
            .post(route("submit.contact"), form)
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
    <div class="grid gap-y-8 laptop:gap-y-16">

        <!-- Header sectie -->
        <div class="text-center">
            <div
                class="max-w-2xl mx-auto bg-white backdrop-blur-sm rounded-xl p-6 tablet:p-8 border border-brand-primary/20 shadow-sm">
                <SectionHeader>{{ $t('forms.contact.heading') }}</SectionHeader>
                <p class="text-left text-sm laptop:text-base text-brand-text leading-relaxed">
                    {{ $t('forms.contact.intro') }}
                    <br />
                    <br />
                    <span class="inline-flex items-center gap-2">
                        <AtSign class="w-5 h-5 text-brand-accent" />
                        <span class="hidden tablet:inline-flex">{{ $t('forms.contact.email') }}</span>
                        <span class="font-bold text-brand-link">
                            <a class="email-field default-link" href="#" v-html="contact.mail.display"></a>
                        </span>
                    </span>
                    <br />
                    <span class="inline-flex items-center gap-2 text-brand-text">
                        <Pencil class="w-5 h-5 text-brand-accent" />
                        {{ $t('forms.contact.or_use_form') }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Formulier sectie -->
        <div id="contact-form" class="max-w-4xl mx-auto scroll-mt-[180px]">
            <form @submit.prevent="submit" @change="resetObject(errors)" class="space-y-8">
                <div
                    class="bg-brand-secondary backdrop-blur-sm rounded-2xl p-6 tablet:p-8 border border-brand-subtle/20 shadow-sm">
                    <div class="grid tablet:grid-cols-2 gap-8 tablet:gap-12">
                        <!-- Links: persoonlijke gegevens -->
                        <div class="space-y-6">
                            <div class="border-l-4 border-brand-accent pl-4 mb-6">
                                <h3 class="text-lg font-semibold text-brand-primary">{{
                                    $t('forms.contact.your_details_heading') }}</h3>
                                <p class="text-sm text-brand-primary/60">{{ $t('forms.contact.your_details_subheading')
                                    }}</p>
                            </div>

                            <Input type="text" name="name" :placeholder="$t('forms.contact.name_label')"
                                :required="false" :show-label="false" v-model="form.name" :feedback="errors?.name"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02]" />
                            <Input type="email" name="email" :placeholder="$t('forms.contact.email_label')"
                                :required="false" :show-label="false" v-model="form.email" :feedback="errors?.email"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02]" />
                            <Input type="phone" name="phone" :placeholder="$t('forms.contact.phone_label')"
                                :required="false" :show-label="false" v-model="form.phone" :feedback="errors?.phone"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02]" />
                        </div>

                        <!-- Rechts: bericht -->
                        <div class="space-y-6">
                            <div class="border-l-4 border-brand-accent pl-4 mb-6">
                                <h3 class="text-lg font-semibold text-brand-primary">{{
                                    $t('forms.contact.your_message_heading') }}</h3>
                                <p class="text-sm text-brand-primary/60">{{ $t('forms.contact.your_message_subheading')
                                    }}</p>
                            </div>

                            <TextArea name="text" :label="$t('forms.contact.message_label')" :required="false"
                                :show-label="false" v-model="form.text" :feedback="errors?.text"
                                class="transition-all duration-300 focus-within:transform focus-within:scale-[1.02] h-auto" />

                            <VueHoneypot ref="honeypot" />

                            <!-- Submit button -->
                            <div class="flex justify-end">
                                <Button>
                                    {{ $t('forms.contact.submit_button') }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
