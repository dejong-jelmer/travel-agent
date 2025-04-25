<script setup>
import { useTypeWriter } from '@/Composables/typeWriter.js'

const title = 'Omdat we';
const subtitle = [
    'nooit uitgeleerd zijn',
    'ons blik willen verruimen',
    'nieuwsgierig blijven',
    'oog hebben voor de toekomst',
    'geïnspireerd willen worden',
    'verbonden zijn',
    'de tijd willen nemen',
    'nieuwe ervaringen willen opdoen',
    'blijven ontdekken',
    ];


const { output: typedText } = useTypeWriter (subtitle);


</script>

<template>
    <div class="h-screen bg-cover bg-top flex text-white px-6 bg-[url('/images/hero-image.jpg')]">
    <div class="relative max-w-screen-desktop w-full mx-auto">
      <div class="absolute top-[35%] tablet:top-[45%] left-[10%] tablet:left-[20%]">
        <h1 class="text-4xl tablet:text-5xl font-semibold leading-10 tablet:leading-[72px]">
            {{ title }}
            <br>
            {{ typedText }}
        </h1>
      </div>
    </div>
    </div>
</template>
<script>
export default {
    data() {
      return {
        // title: "Omdat we",
        // subtiles: [
        //     "nooit uitgeleerd zijn",
        //     ]
      };
    }
}
</script>
