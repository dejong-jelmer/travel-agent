<template>
    <Layout>
        <section class="py-12 px-4">
            <article>
                <div class="max-w-[600px] mx-auto text-center">
                    <!-- Success Icon -->
                    <div class="mb-8">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" fill="none"
                            class="mx-auto w-20 h-20">
                            <circle cx="40" cy="40" r="40" class="fill-status-success/10" />
                            <circle cx="40" cy="40" r="32" class="fill-status-success/20" />
                            <path d="M25 40L35 50L55 30" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                stroke-linejoin="round" class="text-status-success" />
                        </svg>
                    </div>

                    <!-- Heading -->
                    <h1 class="text-2xl font-bold text-brand-dark mb-4">
                        Je bent ingeschreven! 🎉
                    </h1>

                    <!-- Confirmation Message -->
                    <p class="text-lg text-brand-primary mb-8 leading-relaxed">
                        Bedankt voor het bevestigen van je aanmelding. Je ontvangt vanaf nu
                        onze nieuwsbrief met de laatste updates, tips en exclusieve content.
                    </p>

                    <!-- What's Next Box -->
                    <div
                        class="bg-neutral-50 border border-ui-blue/30 rounded-2xl p-8 mb-8 text-left shadow-sm">
                        <h2 class="text-xl font-semibold text-brand-dark mb-4">
                            Wat kun je verwachten?
                        </h2>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <span class="text-status-success font-bold mr-2">✓</span>
                                <span class="text-brand-primary">
                                    Regelmatige updates over ons nieuwste reisaanbod
                                </span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-status-success font-bold mr-2">✓</span>
                                <span class="text-brand-primary">
                                    Exclusieve content en verhalen
                                </span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-status-success font-bold mr-2">✓</span>
                                <span class="text-brand-primary">
                                    Je kunt je altijd weer uitschrijven
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- CTA Button -->
                    <a href="/">
                        <Button>
                            Terug naar home
                        </Button>
                    </a>

                    <!-- Footer Note -->
                    <p class="mt-8 text-sm text-ui-blue">
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
