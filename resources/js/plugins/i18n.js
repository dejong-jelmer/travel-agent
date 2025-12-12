// plugins/i18n.js
import { createI18n } from "vue-i18n";

import nl from "../../lang/nl.json";
import en from "../../lang/en.json";

const messages = {
    nl,
    en
};

const i18n = createI18n({
    legacy: false,
    locale: "nl", // Will be overridden in app.js with server locale
    fallbackLocale: "nl",
    messages: messages,
    silentTranslationWarn: true,
    missingWarn: false,
    silentFallbackWarn: true
});

export default i18n;
