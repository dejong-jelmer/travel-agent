<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    title: {
        type: String,
        required: true
    },
    message: {
        type: String,
        required: true
    },
    boxTitle: {
        type: String,
        required: true
    }
})
</script>

<template>
    <Layout>
        <section class="py-24 px-4">
            <article>
                <div class="max-w-screen-tablet mx-auto">
                    <!-- Icon -->
                    <div class="mb-8 text-center">
                        <slot name="icon" />
                    </div>

                    <!-- Heading -->
                    <h1 class="text-2xl font-bold text-brand-primary mb-4">
                        {{ title }}
                    </h1>

                    <!-- Message -->
                    <p class="text-lg text-brand-primary mb-8 leading-relaxed">
                        {{ message }}
                    </p>

                    <!-- What's Next Box -->
                    <div class="bg-white border border-brand-light/30 rounded-2xl p-8 mb-8 text-left shadow-sm">
                        <h2 class="text-xl font-semibold text-brand-primary mb-4">
                            {{ boxTitle }}
                        </h2>
                        <ul class="space-y-3">
                            <slot name="list-items" />
                        </ul>
                    </div>

                    <!-- CTA Button -->
                    <div class="mt-8 laptop:mt-12 text-center">
                        <Link :href="route('home')" as="a">
                            <Button>
                                Terug naar homepagina
                            </Button>
                        </Link>
                    </div>

                    <!-- Footer Note -->
                    <p class="mt-8 text-sm text-brand-light text-center">
                        Heb je vragen?
                        <DefaultLink href="/contact">
                            Neem contact met ons op
                        </DefaultLink>
                    </p>
                </div>
            </article>
        </section>
    </Layout>
</template>
