<template>
    <NewsletterStatusTemplate
        title="Je bent uitgeschreven."
        message="Je hebt je succesvol afgemeld voor onze nieuwsbrief. Je ontvangt geen verdere e-mails meer, maar je kunt altijd opnieuw inschrijven als je op de hoogte wilt blijven van ons nieuwste reisaanbod."
        box-title="Wat nu?"
    >
        <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" fill="none"
                class="mx-auto w-20 h-20">
                <circle cx="40" cy="40" r="40" class="fill-status-warning/10" />
                <circle cx="40" cy="40" r="32" class="fill-status-warning/20" />
                <path d="M30 30L50 50M50 30L30 50" stroke="currentColor" stroke-width="4"
                    stroke-linecap="round" stroke-linejoin="round" class="text-status-warning" />
            </svg>
        </template>

        <template #list-items>
            <li class="flex items-center gap-2">
                <span class="w-2 h-2 bg-accent-primary rounded-full flex-shrink-0"></span>
                <span class="text-brand-primary">
                    Je ontvangt geen nieuwsbrieven meer van ons
                </span>
            </li>
            <li class="flex items-center gap-2">
                <span class="w-2 h-2 bg-accent-primary rounded-full flex-shrink-0"></span>
                <span class="text-brand-primary">
                    Je kunt je altijd opnieuw inschrijven op een later moment
                </span>
            </li>
            <li class="flex items-center gap-2">
                <span class="w-2 h-2 bg-accent-primary rounded-full flex-shrink-0"></span>
                <span class="text-brand-primary">
                    Voor vragen kun je contact met ons opnemen
                </span>
            </li>
        </template>
    </NewsletterStatusTemplate>
</template>
