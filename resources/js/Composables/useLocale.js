// resources/js/composables/useLocale.js
import { computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

export function useLocale() {
    const { locale: i18nLocale } = useI18n();
    const page = usePage();

    const currentLocale = computed(() => page.props.locale);
    // const availableLocales = computed(() => page.prop    s.locales || []);
    // Convert array to object with locale names
    const availableLocales = computed(() => {
        const locales = page.props.locales || [];
        const localeNames = {
            nl: "Nederlands",
            en: "English",
        };
        // Create object from array
        return Object.fromEntries(
            locales.map((locale) => [locale, localeNames[locale] || locale])
        );
    });

    const switchLocale = (newLocale) => {
        if (newLocale === currentLocale.value) return;
         if (!Object.keys(availableLocales.value).includes(newLocale)) {
            console.warn(`Locale '${newLocale}' is not available`);
            return;
        }

        router.post(
            route("locale.switch"),
            {
                locale: newLocale,
            },
            {
                preserveState: false,
                preserveScroll: true,
                onSuccess: () => {
                    i18nLocale.value = newLocale;
                },
            }
        );
    };

    return {
        currentLocale,
        availableLocales,
        switchLocale,
    };
}
