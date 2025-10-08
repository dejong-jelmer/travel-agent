<template>
    <Layout>
        <section class="py-12 px-4">
            <article>
                <div class="max-w-[600px] mx-auto text-center">
                    <!-- Unsubscribe Icon -->
                    <div class="mb-8">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" fill="none"
                            class="mx-auto w-20 h-20">
                            <circle cx="40" cy="40" r="40" class="fill-status-warning/10" />
                            <circle cx="40" cy="40" r="32" class="fill-status-warning/20" />
                            <path d="M30 30L50 50M50 30L30 50" stroke="currentColor" stroke-width="4"
                                stroke-linecap="round" stroke-linejoin="round" class="text-status-warning" />
                        </svg>
                    </div>

                    <!-- Heading -->
                    <h1 class="text-2xl font-bold text-primary-dark mb-4">
                        Je bent uitgeschreven 📨
                    </h1>

                    <!-- Confirmation Message -->
                    <p class="text-lg text-primary-default mb-8 leading-relaxed">
                        Je hebt je succesvol afgemeld voor onze nieuwsbrief. Je ontvangt geen
                        verdere e-mails meer, maar je kunt altijd opnieuw inschrijven als je
                        op de hoogte wilt blijven van ons nieuwste reisaanbod.
                    </p>

                    <!-- What's Next Box -->
                    <div
                        class="bg-neutral-50 border border-secondary-stone/30 rounded-2xl p-8 mb-8 text-left shadow-sm">
                        <h2 class="text-xl font-semibold text-primary-dark mb-4">
                            Wat nu?
                        </h2>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <span class="text-status-warning font-bold mr-2">⚠</span>
                                <span class="text-primary-default">
                                    Je ontvangt geen nieuwsbrieven meer van ons
                                </span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-status-warning font-bold mr-2">⚠</span>
                                <span class="text-primary-default">
                                    Je kunt je altijd opnieuw inschrijven op een later moment
                                </span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-status-warning font-bold mr-2">⚠</span>
                                <span class="text-primary-default">
                                    Voor vragen kun je contact met ons opnemen
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
                    <p class="mt-8 text-sm text-secondary-stone">
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
