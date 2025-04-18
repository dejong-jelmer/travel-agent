<script setup>
import { SideTrain, Mobile, Sto, Calamiteiten, VvKR } from "@/Pages/Icons";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const contact = page.props.contact;
</script>
<template>
    <div class="w-full bg-light-green border-b-2 border-ochre-yellow">
        <div
            class="max-w-screen-desktop mx-auto px-6 laptop:px-2 py-2 flex justify-between"
        >
            <div class="flex gap-x-2 laptop:gap-x-9">
                <div class="flex items-center gap-x-1 laptop:gap-x-3">
                    <SideTrain class="h-4 laptop:h-6 text-warm-terracotta"></SideTrain>
                    <p class="text-2xs laptop:text-xs text-deep-bleu">Duurzaam op reis</p>
                </div>
                <div class="flex items-center gap-x-0 laptop:gap-x-3">
                    <Mobile class="h-4 laptop:h-6 text-warm-terracotta"></Mobile>
                    <p class="text-2xs laptop:text-xs text-deep-bleu">
                        <a :href="`tel:${contact.telephone.tel}`">{{ contact.telephone.display }}</a>
                    </p>
                </div>
            </div>
            <div class="flex gap-x-1 laptop:gap-x-6">
                <Sto class="h-6"></Sto>
                <Calamiteiten class="h-6"></Calamiteiten>
                <!-- <VvKR class="h-6"></VvKR> -->
            </div>
        </div>
    </div>
</template>
