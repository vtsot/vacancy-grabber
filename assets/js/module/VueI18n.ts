import {createI18n} from 'vue-i18n';
import HttpClient from './HttpClient';
import {EventBus, events} from './EventBus';

export const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    globalInjection: true,
    messages: {
    },
});

const loadedLocales: string[] = [];

function setI18nLocale(locale) {
    if (i18n.global.locale.value !== locale) {
        EventBus.emit(events.LOCALE_CHANGE, locale);
    }

    i18n.global.locale.value = locale;
}

export function loadLocaleAsync(locale: string) {
    return new Promise((resolve) => {
        if (loadedLocales.includes(locale)) {

            setI18nLocale(locale);
            resolve(true);
        } else {
            HttpClient
                .get('api.translation_collection', {_locale: locale})
                .then((response) => {
                    const items: Record<string, string> = response.dataCollection?.items[0] as any;
                    i18n.global.setLocaleMessage(locale, items);
                    loadedLocales.push(locale);
                    setI18nLocale(locale);
                    resolve(true);
                });
        }
    });
}

export const translate = (key: string) => {
    if (!key) {
        return '';
    }
    return i18n.global.t(key);
};
