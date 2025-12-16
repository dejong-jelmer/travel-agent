<script setup>
import { useI18n } from 'vue-i18n'
import teaserImage from '@/../images/pexels-sonny-vermeer-505472791-17476670.jpg'

const { t } = useI18n()
</script>
<template>
    <Layout>
        <section class="section relative overflow-hidden floating-elements">
            <div class="absolute top-0 left-0 w-full h-2 decorative-line"></div>
            <!-- Geometrische vormen -->
            <div class="absolute top-16 left-8 w-4 h-4 bg-accent-sage/20 rounded-full"></div>
            <div class="absolute top-32 right-24 w-2 h-2 bg-accent-primary/30 rounded-full"></div>
            <div class="absolute bottom-24 left-16 w-3 h-3 bg-accent-terracotta/15 rounded-full"></div>
            <article class="relative py-8 desktop:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">
                    <div class="text-center">
                        <SectionHeader>{{ t('about.page_title') }}</SectionHeader>
                    </div>
                    <div class="max-w-screen-laptop mx-auto px-4">
                        <div class="max-w-4xl mx-auto px-4 pt-6 sm:px-6 desktop:px-8 grid gap-4 desktop:gap-6">
                            <div class="grid grid-cols-1 desktop:grid-cols-2 gap-4 desktop:gap-6 ">
                                <div class="text-left space-y-6">
                                    <p class="text-lg sm:text-xl desktop:text-xl text-brand-primary leading-relaxed">
                                        {{ t('about.intro_paragraph') }}
                                    </p>

                                    <p
                                        class="text-base sm:text-lg desktop:text-xl text-brand-primary leading-relaxed"
                                        v-html="t('about.coming_soon')">
                                    </p>
                                </div>
                                <div class="order-first desktop:order-last">
                                    <img :src="teaserImage" :alt="t('about.image_alt')"
                                        class="w-full h-auto rounded-lg shadow-lg object-cover" />
                                </div>
                            </div>
                            <div>
                                <p class="text-base sm:text-lg desktop:text-xl text-brand-primary leading-relaxed"
                                    v-html="t('about.philosophy')">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
            <Newsletter />
        </section>
    </Layout>
</template>
