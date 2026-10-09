<script setup>
const usps = [
    {
        titleKey: 'usp.usp-1.title',
        descriptionKey: 'usp.usp-1.description',
    },
    {
        titleKey: 'usp.usp-2.title',
        descriptionKey: 'usp.usp-2.description',
    },
    {
        titleKey: 'usp.usp-3.title',
        descriptionKey: 'usp.usp-3.description',
    }
];
</script>
<template>
    <!-- Text only, so the USPs do not look like the trip cards above them -->
    <section class="bg-white border-y border-brand-accent/20 py-14 tablet:py-20 px-6 laptop:px-8">
        <div class="max-w-6xl mx-auto">

            <!-- Header -->
            <div class="text-center mb-4 laptop:mb-8">
                <SectionHeader>{{ $t('usp.heading') }}</SectionHeader>
            </div>

            <!-- USPs, three columns from laptop on -->
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8 laptop:gap-12">
                <div v-for="usp in usps" :key="usp.titleKey">
                    <span class="block w-8 h-[3px] mb-4 rounded-full bg-brand-accent" aria-hidden="true"></span>
                    <h3 class="mb-3 text-xl font-semibold text-brand-primary">
                        {{ $t(usp.titleKey) }}
                    </h3>
                    <p class="text-base leading-[1.7] text-brand-text">
                        {{ $t(usp.descriptionKey) }}
                    </p>
                </div>
            </div>

        </div>
    </section>
</template>
