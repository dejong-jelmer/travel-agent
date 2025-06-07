<script setup>
import { usePage } from '@inertiajs/vue3';
import { Logo } from "@/Pages/Icons";

const page = usePage();
const contact = page.props.contact;
const companyName = window.appName;
const year = new Date().getFullYear();

</script>
<template>
    <footer class="bg-primary-default">
        <div class="max-w-screen-desktop px-4 pt-16 pb-6 mx-auto tablet:px-6 laptop:pt-24">
            <div class="grid tablet:flex grid-cols-1 gap-12 tablet:grid-cols-2 laptop:grid-cols-3 tablet:justify-between">
                <!-- Logo + Slogan -->
                <div class="text-center tablet:text-left">
                    <div class="flex justify-center tablet:justify-start text-light-green">
                        <Logo class="text-white w-[192px] h-[62px]" />
                    </div>
                    <p
                        class="max-w-md mx-auto mt-6 leading-relaxed text-center tablet:mx-0 tablet:text-left text-gray-400">
                        Voor duurzame reizen met een verhaal.
                    </p>

                    <!-- social icons (commented out, unchanged) -->
                </div>

                <!-- Contactblok -->
                <div class="text-center tablet:text-left col-span-1">
                    <p class="text-lg font-medium text-white">Contact</p>
                    <div class="w-full flex justify-center tablet:justify-start">
                        <ul class="mt-8 space-y-4 text-sm text-left">
                            <li>
                                <a class="flex items-start gap-1.5 group" href="/">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-white transition">
                                        <a class="email-field" v-html="contact.mail.display"></a>
                                    </span>
                                </a>
                            </li>

                            <li>
                                <span class="flex items-start gap-1.5 group">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span class="text-white transition group-hover:text-white/75">
                                        <a class="text-white tel-field"></a>
                                    </span>
                                </span>
                            </li>

                            <li class="flex items-start gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white shrink-0" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <address class="-mt-0.5 not-italic text-white hover:underline">
                                    <a :href="contact.mapsLink" target="_blank">
                                        {{ contact.fullAddress }}
                                    </a>
                                </address>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Bottom section -->
            <div class="pt-6 mt-12 border-t border-gray-800">
                <div class="text-center tablet:flex tablet:justify-between tablet:text-left">
                    <p class="text-sm text-gray-400">
                        <span class="block tablet:inline">Alle rechten voorbehouden.</span>
                        &nbsp;
                        <a class="inline-block text-teal-500 underline transition hover:text-teal-500/75" href="/">
                            Algemene voorwaarden
                        </a>
                        &nbsp;<span>&middot;</span>&nbsp;
                        <a class="inline-block text-teal-500 underline transition hover:text-teal-500/75" href="/">
                            Privacybeleid
                        </a>
                    </p>

                    <p class="mt-4 text-sm text-gray-500 tablet:order-first tablet:mt-0">
                        &copy; {{ year }} {{ companyName }}
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>
