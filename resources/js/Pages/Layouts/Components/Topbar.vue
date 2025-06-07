<script setup>
import { Tree, Mobile, Sto, Calamiteiten, VvKR } from "@/Pages/Icons";
import { usePage } from "@inertiajs/vue3";
import { computed } from 'vue';

const props = defineProps({
  isScrolled: {
    type: Boolean,
    default: false
  }
});

const page = usePage();
const contact = page.props.contact;

const topbarClasses = computed(() => {
  return [
    'w-full transition-all duration-500',
    props.isScrolled 
      ? 'bg-secondary-sage border-b border-accent-gold/30' 
      : 'bg-secondary-sage/95 backdrop-blur-sm border-b-2 border-accent-gold'
  ]
});

const textClasses = computed(() => {
  return [
    'transition-colors duration-300',
    props.isScrolled ? 'text-white' : 'text-primary-dark'
  ]
});
</script>

<template>
    <div :class="topbarClasses">
        <div class="max-w-screen-desktop mx-auto px-6 py-2 flex justify-between items-center">
            <div class="flex gap-x-3 laptop:gap-x-8 items-center">
                <div class="flex items-center gap-x-2 laptop:gap-x-3">
                    <Tree class="h-5 laptop:h-6 text-accent-gold drop-shadow-sm"></Tree>
                    <p class="text-xs laptop:text-sm font-medium" :class="textClasses">
                        Ga duurzaam op reis
                    </p>
                </div>
                <div class="hidden laptop:flex items-center gap-x-2 laptop:gap-x-3">
                    <Mobile class="h-5 laptop:h-6 text-accent-gold drop-shadow-sm"></Mobile>
                    <a class="text-xs laptop:text-sm font-medium tel-field" :class="textClasses"></a>
                </div>
            </div>
            
            <!-- Certificering badges -->
            <div class="flex gap-x-2 laptop:gap-x-4 items-center">
                <div class="flex items-center gap-x-1">
                    <!-- <span class="w-2 h-2 bg-accent-gold rounded-full animate-pulse"></span> -->
                    <!-- Uncomment indien badges beschikbaar zijn -->
                    <!-- <Sto class="h-6 laptop:h-8 opacity-80 hover:opacity-100 transition-opacity" /> -->
                    <!-- <Calamiteiten class="h-6 laptop:h-8 opacity-80 hover:opacity-100 transition-opacity" /> -->
                    <!-- < class="h-6 opacity-80 hover:opacity-100 transition-opacity" /> -->
                </div>
            </div>
        </div>
    </div>
</template>