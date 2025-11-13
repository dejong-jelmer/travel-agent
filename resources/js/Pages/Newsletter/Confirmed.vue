<template>
    <NewsletterStatusTemplate
        title="Je bent ingeschreven!"
        message="Bedankt voor het bevestigen van je aanmelding. Je ontvangt vanaf nu onze nieuwsbrief met de laatste updates, tips en exclusieve content."
        box-title="Wat kun je verwachten?"
    >
        <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" fill="none"
                class="mx-auto w-20 h-20">
                <circle cx="40" cy="40" r="40" class="fill-status-success/10" />
                <circle cx="40" cy="40" r="32" class="fill-status-success/20" />
                <path d="M25 40L35 50L55 30" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                    stroke-linejoin="round" class="text-status-success" />
            </svg>
        </template>

        <template #list-items>
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
        </template>
    </NewsletterStatusTemplate>
</template>
