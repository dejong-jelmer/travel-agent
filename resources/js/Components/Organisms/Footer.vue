<script setup>
import { usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

const page = usePage();
const contact = page.props.contact;
const companyName = window.appName;
const year = new Date().getFullYear();
</script>
<template>
  <footer class="bg-brand-primary">
    <div
      class="max-w-screen-wide laptop:max-w-screen-desktop px-4 pt-16 pb-6 mx-auto tablet:px-6 laptop:pt-24"
    >
      <div
        class="grid tablet:flex grid-cols-1 gap-12 tablet:grid-cols-2 laptop:grid-cols-3 tablet:justify-between"
      >
        <!-- Logo + Slogan -->
        <div class="text-left">
          <div class="flex justify-start">
            <Logo class="text-brand-secondary w-[192px] h-[62px]" />
          </div>
          <p
            class="max-w-md mx-auto mt-6 leading-relaxed text-left text-brand-secondary"
          >
            {{ $t('footer.slogan') }}
          </p>
          <ul class="mt-5 space-y-4 text-sm">
            <li>
              <span class="flex items-center gap-1.5">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-5 h-5 text-white shrink-0"
                  viewBox="0 0 24 24"
                >
                  <g fill="none">
                    <path
                      d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"
                    />
                    <path
                      fill="currentColor"
                      d="M21 7a1 1 0 0 1 .117 1.993L21 9v10a1 1 0 0 1 .117 1.993L21 21H3a1 1 0 0 1-.117-1.993L3 19V9a1 1 0 0 1-.117-1.993L3 7zm-2 2H5v10h2v-7a1 1 0 1 1 2 0v7h2v-7a1 1 0 1 1 2 0v7h2v-7a1 1 0 1 1 2 0v7h2zm-1-5a1 1 0 1 1 0 2H6a1 1 0 0 1 0-2z"
                    />
                  </g>
                </svg>
                <span class="not-italic text-white"> KvK: {{ contact.kvk }} </span>
              </span>
            </li>
            <li>
              <span class="flex items-center gap-1.5">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-5 h-5 text-white shrink-0"
                  viewBox="0 0 24 24"
                >
                  <g fill="none" fill-rule="evenodd">
                    <path
                      d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"
                    />
                    <path
                      fill="currentColor"
                      d="M4 4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 2h16v12H4zm6.25 3.75a1.75 1.75 0 1 1-3.5 0a1.75 1.75 0 0 1 3.5 0M5 14.5A2.5 2.5 0 0 1 7.5 12h2a2.5 2.5 0 0 1 2.5 2.5v.5a1 1 0 1 1-2 0v-.5a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 0-.5.5v.5a1 1 0 1 1-2 0zm8-3.5a1 1 0 0 1 1-1h2a1 1 0 1 1 0 2h-2a1 1 0 0 1-1-1m1 2a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2z"
                    />
                  </g>
                </svg>
                <span class="not-italic text-white"> BTW: {{ contact.btw }} </span>
              </span>
            </li>
          </ul>
        </div>

        <!-- Contactblok -->
        <div class="col-span-1">
          <p class="text-lg font-medium text-white">{{ $t('footer.contact') }}</p>
          <div class="w-full justify-start">
            <ul class="mt-8 space-y-4 text-sm text-left">
              <li>
                <a class="flex items-start gap-1.5 group" href="/">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 text-white shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    />
                  </svg>
                  <span class="text-white transition">
                    <a class="email-field" v-html="contact.mail.display"></a>
                  </span>
                </a>
              </li>
              <li>
                <span class="flex items-start gap-1.5 group">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 text-white shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                    />
                  </svg>
                  <span class="text-white transition group-hover:text-white/75">
                    <a class="text-white tel-field"></a>
                  </span>
                </span>
              </li>

              <li class="flex items-start gap-1.5">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-5 h-5 text-white shrink-0"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                  />
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
            <span class="block tablet:inline">{{ $t('footer.all_rights') }}</span>
            &nbsp;
            <Link
              :href="route('terms')"
              class="inline-block text-accent-link underline transition hover:text-accent-link/75"
            >
              {{ $t('footer.conditions') }}
            </Link>
            &nbsp;<span>&middot;</span>&nbsp;
            <Link
              :href="route('privacy')"
              class="inline-block text-accent-link underline transition hover:text-accent-link/75"
            >
                {{ $t('footer.privacy') }}
            </Link>
          </p>

          <p class="mt-4 text-sm text-white tablet:order-first tablet:mt-0">
            &copy; {{ year }} {{ companyName }}
          </p>
        </div>
      </div>
    </div>
  </footer>
</template>
