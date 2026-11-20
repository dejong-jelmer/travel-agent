<script setup>
import { ArrowRight } from '@lucide/vue';
import portrait from '@/../images/me_thumb.webp';

defineProps({
    name: {
        type: String,
        required: true,
    },
    bio: {
        type: String,
        required: true,
    },
    // Where 'more about me' leads; the about page unless set
    href: {
        type: String,
        default: () => route('about'),
    },
    // Url of a square photo, shown round; the portrait of the about page unless set
    photo: {
        type: String,
        default: portrait,
    },
});
</script>

<template>
    <BaseCard as="aside" aria-labelledby="author-box-name">
        <!-- Phones: photo next to the name, bio and link below them. From tablet up: photo in its own column. -->
        <div
            class="grid grid-cols-[72px_minmax(0,1fr)] tablet:grid-cols-[120px_minmax(0,1fr)] items-center tablet:items-start gap-x-4 gap-y-4 tablet:gap-x-7 tablet:gap-y-3">
            <img :src="photo" alt="" width="120" height="120" loading="lazy"
                class="w-[72px] h-[72px] tablet:w-[120px] tablet:h-[120px] rounded-full object-cover tablet:row-span-2" />

            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-4 h-px bg-brand-accent"></div>
                    <span class="text-xs font-medium tracking-widest uppercase text-brand-primary">
                        {{ $t('blog.author.label') }}
                    </span>
                </div>
                <h2 id="author-box-name" class="text-xl tablet:text-2xl font-semibold text-brand-primary">
                    {{ name }}
                </h2>
            </div>

            <div class="col-span-2 tablet:col-span-1 tablet:col-start-2 space-y-4">
                <p class="text-base text-brand-text leading-relaxed">
                    {{ bio }}
                </p>
                <DefaultLink :href="href" class="inline-flex items-center gap-1.5">
                    {{ $t('blog.author.link') }}
                    <ArrowRight class="w-4 h-4" aria-hidden="true" />
                </DefaultLink>
            </div>
        </div>
    </BaseCard>
</template>
