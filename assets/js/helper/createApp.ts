import {createApp as createVueApp, defineComponent} from 'vue';
import dayjs from 'dayjs';
import Cookies from 'js-cookie';
// Vuetify
// import 'vuetify/styles';
import {createVuetify} from 'vuetify';
import * as vuetifyComponents from 'vuetify/components';
import * as vuetifyDirectives from 'vuetify/directives';
import {aliases, mdi} from 'vuetify/iconsets/mdi-svg';
// app
import {createPinia} from 'pinia';
import HttpClient from "../module/HttpClient";
import RouteGenerator from "../module/RouteGenerator";
import {useGlobalStore} from '../component/global/store';
import AppHeader from '../component/general/app-header/AppHeader.vue';
import AppAlert from "../component/general/app-alert/AppAlert.vue";
import AppFlash from "../component/general/app-flash/AppFlash.vue";
import {EventBus, events} from "../module/EventBus";
import {useHeaderStore} from "../component/general/app-header/store";

export default function createApp(components: object = {}, mount = '#app') {
    const locale = document.querySelector('html')?.getAttribute('lang') as string;
    const lang = locale.split('_')[0] ?? 'en';
    const userId = document.documentElement.dataset.userId || null;

    // import days locale
    try {
        import('dayjs/locale/' + lang).then((v) => dayjs.locale(v));
    } catch (e) {
        import('dayjs/locale/en').then((v) => dayjs.locale(v));
    }

    // load routes
    RouteGenerator.loadRoutes()
        .then(() => {
            RouteGenerator.setLocale(lang);
            HttpClient.setLocale(locale);

            // create vue
            const app = createVueApp(defineComponent({
                components: {...components},

                setup() {
                    const globalStore = useGlobalStore();
                    const headerStore = useHeaderStore();

                    return {globalStore, headerStore};
                },

                created() {
                    this.globalStore.init = false;

                    if (userId) {
                        if (Cookies.get('token')) {
                            localStorage.setItem('token', Cookies.get('token'));
                            localStorage.setItem('refreshToken', Cookies.get('refreshToken'));
                            Cookies.remove('token');
                            Cookies.remove('refreshToken');
                        }

                        if (localStorage.getItem('token')) {
                            EventBus.emit(events.LOGIN_SUCCESS, localStorage.getItem('token'));
                        }
                    } else {
                        if (Cookies.get('token')) {
                            Cookies.remove('token');
                            Cookies.remove('refreshToken');
                        }

                        if (localStorage.getItem('token')) {
                            localStorage.removeItem('token');
                        }
                    }

                    const delay = (ms, value) => new Promise(resolve => setTimeout(() => resolve(value), ms));
                    const tasks = [delay(200, true)];

                    Promise
                        .all(tasks)
                        .then(() => this.globalStore.init = true);
                },
            }));

            app.use(createPinia());
            app.use(createVuetify({
                icons: {
                    defaultSet: 'mdi',
                    aliases,
                    sets: {mdi},
                },
                // theme: {
                //     defaultTheme: 'qiraLightTheme',
                //     themes: {
                //         qiraLightTheme,
                //     },
                // },
                // locale: {
                //     adapter: createVueI18nAdapter({i18n, useI18n}),
                // },

                components: {
                    ...vuetifyComponents,
                    AppHeader,
                    AppAlert,
                    AppFlash,
                },
                directives: vuetifyDirectives,
            }));

            app.mount(mount);
        });
}
