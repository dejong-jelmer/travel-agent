// plugins/i18n.js
import { createI18n } from "vue-i18n";

import nl from "../../lang/nl.json";

// Dutch, the language of the public site, comes with the bundle. The other languages are only used in the admin, so
// they are loaded on demand (loadLocaleMessages) and stay out of the bundle of the public pages.
const loaders = {
    en: () => import("../../lang/en.json"),
};

const i18n = createI18n({
    legacy: false,
    locale: "nl", // Will be overridden in app.js with server locale
    fallbackLocale: "nl",
    messages: { nl },
    silentTranslationWarn: true,
    missingWarn: false,
    silentFallbackWarn: true
});

/**
 * Loads the messages of a locale that is not in the bundle, once. Resolves at once for a locale that is already
 * there or unknown, which then falls back to Dutch.
 *
 * @param {string} locale
 * @returns {Promise<void>}
 */
export async function loadLocaleMessages(locale) {
    if (i18n.global.availableLocales.includes(locale) || !loaders[locale]) {
        return;
    }

    const messages = await loaders[locale]();
    i18n.global.setLocaleMessage(locale, messages.default);
}

export default i18n;
