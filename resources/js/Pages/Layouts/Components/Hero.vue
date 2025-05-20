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

// const { output: typedText } = useTypeWriter(subtitle);
const text = subtitle[getRandomNumber(0, subtitle.length - 1)];
</script>

<template>
    <div
        class="mt-0 laptop:mt-[3%] h-screen max-w-screen-desktop px-6 w-full mx-auto"
    >
        <div
            class="h-screen w-full flex flex-col gap-y-10 justify-center laptop:flex-row laptop:items-start overflow-hidden"
        >
            <div class="w-full laptop:w-[45%] flex justify-center laptop:justify-start">
                <h1
                    class="ml-2 mt-1 overflow-visible font-tahu text-accent-earth text-5xl tablet:text-6xl laptop:text-7xl font-normal leading-[48px] tablet:leading-[60px] laptop:leading-[72px]"
                >
                    {{ title }}
                    <br />
                    {{ text }}.
                </h1>
            </div>
            <div class="w-full laptop:flex-grow flex justify-center laptop:justify-end">
                <img
                    class="max-h-[400px] tablet:max-h-[500px] laptop:max-h-[650px] w-full max-w-xs tablet:max-w-md laptop:max-w-[525px]"
                    src="/public/images/hero-image-2.jpg"
                    alt=""
                />
            </div>
        </div>
    </div>
</template>
