<script setup>
import { useI18n } from 'vue-i18n'
import { useContactForm } from '@/composables/useContactForm'

const { t } = useI18n()
const { form, errors, honeypot, submit, resetObject } = useContactForm()
</script>

<template>
    <section id="contact" class="relative overflow-hidden bg-brand-primary scroll-mt-[120px]">
        <span aria-hidden="true"
            class="pointer-events-none select-none absolute -top-24 right-52 font-serif leading-none text-white/[0.045] text-[20rem] laptop:text-[28rem]">?</span>

        <div class="relative z-10 max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-6 laptop:px-8 pt-20 tablet:pt-28">
            <!-- eyebrow -->
            <div class="flex items-center gap-3.5 mb-7">
                <span class="block w-9 h-0.5 bg-brand-accent"></span>
                <span class="text-xs uppercase tracking-[0.18em] font-poppins font-semibold text-brand-accent">
                    {{ t('home.contact_cta.eyebrow') }}
                </span>
            </div>

            <!-- heading + body -->
            <div class="grid laptop:grid-cols-2 gap-10 laptop:gap-16 laptop:items-start mb-12 tablet:mb-16">
                <h2 class="font-poppins text-white text-brand-sand leading-[1.05] text-4xl laptop:text-5xl wide:text-6xl">
                    {{ t('home.contact_cta.heading') }}
                </h2>
                <p class="font-poppins font-light text-white/85 text-base laptop:text-lg leading-relaxed max-w-md laptop:pt-2">
                    {{ t('home.contact_cta.body') }}
                </p>
            </div>

            <!-- form -->
            <form @submit.prevent="submit" @change="resetObject(errors)">
                <div class="grid tablet:grid-cols-2 gap-10 tablet:gap-14 items-start">
                    <div class="space-y-5">
                        <div class="border-l-4 border-brand-accent pl-4 mb-6">
                            <h3 class="font-poppins text-white text-xl laptop:text-2xl font-semibold text-brand-sand leading-tight">
                                {{ t('forms.contact.your_details_heading') }}
                            </h3>
                            <p class="font-poppins text-sm font-light text-white/85 mt-1">
                                {{ t('forms.contact.your_details_subheading') }}
                            </p>
                        </div>

                        <Input type="text" name="name" :placeholder="t('forms.contact.name_label')"
                            :required="false" :show-label="false" v-model="form.name" :feedback="errors?.name" />
                        <Input type="email" name="email" :placeholder="t('forms.contact.email_label')"
                            :required="false" :show-label="false" v-model="form.email" :feedback="errors?.email" />
                        <Input type="phone" name="phone" :placeholder="t('forms.contact.phone_label')"
                            :required="false" :show-label="false" v-model="form.phone" :feedback="errors?.phone" />
                    </div>

                    <div class="flex flex-col h-full">
                        <div class="border-l-4 border-brand-accent pl-4 mb-6">
                            <h3 class="font-poppins text-white text-xl laptop:text-2xl font-semibold text-brand-sand leading-tight">
                                {{ t('forms.contact.your_message_heading') }}
                            </h3>
                            <p class="font-poppins text-sm font-light text-white/85 mt-1">
                                {{ t('forms.contact.your_message_subheading') }}
                            </p>
                        </div>

                        <TextArea name="text" :label="t('forms.contact.message_label')" :required="false"
                            :show-label="false" v-model="form.text" :feedback="errors?.text"
                            class="flex-1 min-h-[167px]" />
                    </div>
                </div>

                <VueHoneypot ref="honeypot" />

                <div class="flex justify-end mt-8">
                    <Button>{{ t('forms.contact.submit_button') }} →</Button>
                </div>
            </form>
        </div>
    </section>
</template>
