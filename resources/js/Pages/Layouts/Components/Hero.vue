<script setup>
import { useTypeWriter } from "@/Composables/typeWriter.js";

const title = "Omdat we";
const subtitle = [
    "nooit uitgeleerd zijn",
    "ons blik willen verruimen",
    "nieuwsgierig blijven",
    "oog hebben voor de toekomst",
    "geïnspireerd willen worden",
    "verbonden zijn",
    "de tijd willen nemen",
    "nieuwe ervaringen willen opdoen",
    "blijven ontdekken",
];
function getRandomNumber(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
}

const { output: typedText } = useTypeWriter(subtitle);
// const text = subtitle[getRandomNumber(0, subtitle.length - 1)];
</script>

<template>
    <div class="h-screen bg-cover bg-top flex text-white px-6 bg-[url('/images/hero-image.jpg')]">
    <div class="relative max-w-screen-desktop w-full mx-auto">
      <div class="absolute top-[35%] tablet:top-[45%] left-[10%] tablet:left-[20%]">
        <h1 class="text-4xl tablet:text-6xl font-normal leading-10 tablet:leading-[72px] font-elite select-none">
            {{ title }}
            <br>
            {{ typedText }}
        </h1>
      </div>
    </div>
    </div>
</template>