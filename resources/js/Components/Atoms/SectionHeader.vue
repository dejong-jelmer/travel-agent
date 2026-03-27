<template>
    <div class="inline-flex items-center gap-4 mb-10 laptop:mb-15">
        <div class="w-8 h-px bg-brand-accent"></div>
        <span class="text-2xl tablet:text-3xl laptop:text-4xl font-nunito tracking-wide text-brand-primary font-medium">
            <slot></slot>
        </span>
        <div class="w-8 h-px bg-brand-accent"></div>
    </div>
</template>
