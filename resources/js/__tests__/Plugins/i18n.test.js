import { describe, it, expect, beforeEach, vi } from "vitest";

describe("i18n", () => {
    let i18n;
    let loadLocaleMessages;

    beforeEach(async () => {
        // A fresh instance per test, without the messages an earlier test loaded
        vi.resetModules();
        ({ default: i18n, loadLocaleMessages } = await import("@/plugins/i18n.js"));
    });

    it("comes with Dutch only", () => {
        expect(i18n.global.availableLocales).toEqual(['nl']);
    });

    it("loads the English messages on demand", async () => {
        await loadLocaleMessages('en');

        expect(i18n.global.availableLocales).toContain('en');
        expect(i18n.global.t('slider.previous', {}, { locale: 'en' })).toBe('Previous');
        expect(i18n.global.t('slider.previous', {}, { locale: 'nl' })).toBe('Vorige');
    });

    it("leaves Dutch and unknown locales alone", async () => {
        await loadLocaleMessages('nl');
        await loadLocaleMessages('fr');

        expect(i18n.global.availableLocales).toEqual(['nl']);
    });
});
