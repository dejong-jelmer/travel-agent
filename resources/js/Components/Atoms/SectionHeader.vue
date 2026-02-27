<template>
    <div class="inline-flex items-center gap-4 py-4 laptop:py-10">
        <div class="w-8 h-px bg-accent-primary"></div>
        <span class="text-2xl tablet:text-4xl laptop:text-5xl font-caveat tracking-wide text-brand-primary font-medium">
            <slot></slot>
        </span>
        <div class="w-8 h-px bg-accent-primary"></div>
    </div>
</template>
