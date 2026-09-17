import RouteGenerator from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources';
import {EventBus, events as EVENTS} from './EventBus';
import RouteParams from "../types/RouteParams";

const defaultLocale = document.querySelector('html')?.getAttribute('data-default-locale') as string;
EventBus.on(EVENTS.LOCALE_CHANGE, function (locale) {
    RouteGenerator.setLocale(locale as string);
});

export default {
    generate(name: string, params: RouteParams = {}, absolute = false): string {
        let oldLocale: string | null = null;

        if (params._locale) {
            oldLocale = RouteGenerator.getLocale();
            RouteGenerator.setLocale(params._locale);
            delete params['_locale'];
        }

        const url = RouteGenerator.generate(name, params, absolute);

        if (oldLocale) {
            RouteGenerator.setLocale(oldLocale);
        }

        return url;
    },

    setLocale(locale: string) {
        RouteGenerator.setLocale(locale);
    },

    getLocale(): string|null {
        return RouteGenerator.getLocale();
    },

    homepage(): string {
        return RouteGenerator.getLocale() === defaultLocale
            ? RouteGenerator.generate('homepage', {})
            : RouteGenerator.generate('web.homepage', {});
    },

    async loadRoutes() {
        const response = await fetch('/js/routing.json');
        const routes = await response.json();

        RouteGenerator.setRoutingData(routes);
    },
};
