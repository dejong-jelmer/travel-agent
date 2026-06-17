<script setup>
import { Leaf, Phone } from 'lucide-vue-next';
import vvkrLogo from '@/../images/vvkr.png'

</script>
<template>
    <div class="w-full bg-brand-secondary">
        <div class="max-w-screen-desktop mx-auto px-6 laptop:px-8 py-2 flex justify-end">
            <div class="flex items-center gap-x-2 laptop:gap-x-3">
                <a href="https://www.vvkr.nl/" title="Aangesloten bij VvKR - Vereniging van Kleinschalige Reisorganisaties" target="_blank" rel="noopener noreferrer"
                    aria-label="VvKR - Vereniging van Kleinschalige Reisorganisaties" class="block transition hover:scale-110 ease-in-out duration-300">
                    <img :src="vvkrLogo" alt="Logo VvKR — Vereniging van Kleinschalige Reisorganisaties" class="h-7" aria-hidden="true">
                </a>
                <a href="https://www.sto-garant.nl/" target="_blank" rel="noopener noreferrer"
                    aria-label="STO Garant" class="block transition hover:scale-110 ease-in-out duration-300">
                    <StoSmall class="h-7 w-7" aria-hidden="true" />
                </a>
            </div>
        </div>
    </div>
</template>
