import {createApp as createVueApp, defineComponent} from 'vue';
import dayjs from 'dayjs';
// Vuetify
import 'vuetify/styles'
import {createVuetify} from 'vuetify'
import * as vuetifyComponents from 'vuetify/components'
import * as vuetifyDirectives from 'vuetify/directives'


export default function createApp(components: object = {}, mount = '#app') {
    const locale = document.querySelector('html')?.getAttribute('lang') as string;
    const lang = locale.split('_')[0] ?? 'en';

    // import days locale
    try {
        import('dayjs/locale/' + lang).then((v) => dayjs.locale(v));
    } catch (e) {
        import('dayjs/locale/en').then((v) => dayjs.locale(v));
    }


    console.log('components: ', components);

    // create vue
    const app = createVueApp(defineComponent({
        //components: {},
        components: {...components},

        setup() {
            console.log('setup');
        },

        created() {
            console.log('created');
        },
    }));

    // createVuetify
    app.use(createVuetify({
        // icons: {
        //     defaultSet: 'mdi',
        //     aliases,
        //     sets: {
        //         mdi,
        //     },
        // },
        // theme: {
        //     defaultTheme: 'qiraLightTheme',
        //     themes: {
        //         qiraLightTheme,
        //     },
        // },
        // locale: {
        //     adapter: createVueI18nAdapter({i18n, useI18n}),
        // },

        components: vuetifyComponents,
        directives: vuetifyDirectives,
    }));

    app.mount(mount);

    // return;
    //
    // RouteGenerator.loadRoutes()
    //     .then(() => {
    //         RouteGenerator.setLocale(locale);
    //         HttpClient.setLocale(locale);
    //
    //         const app = createVueApp(defineComponent({
    //             components: {
    //                 ...components,
    //             },
    //
    //             setup() {
    //                 const globalStore = useGlobalStore();
    //
    //                 return {globalStore};
    //             },
    //
    //             created() {
    //                 this.globalStore.init = false;
    //
    //                 if (userId) {
    //                     if (Cookies.get('token')) {
    //                         localStorage.setItem('token', Cookies.get('token'));
    //                         localStorage.setItem('refreshToken', Cookies.get('refreshToken'));
    //
    //                         Cookies.remove('token');
    //                         Cookies.remove('refreshToken');
    //                     }
    //
    //                     if (localStorage.getItem('token')) {
    //                         EventBus.emit(events.LOGIN_SUCCESS, localStorage.getItem('token'));
    //                     }
    //                 } else {
    //                     if (Cookies.get('token')) {
    //                         Cookies.remove('token');
    //                         Cookies.remove('refreshToken');
    //                     }
    //
    //                     if (localStorage.getItem('token')) {
    //                         localStorage.removeItem('token');
    //                     }
    //                 }
    //
    //                 Promise
    //                     .all([
    //                         this.globalStore.loadMe(),
    //                         this.globalStore.loadLocales(locale),
    //                         loadLocaleAsync(locale),
    //                     ])
    //                     .then(() => {
    //                         AnalyticTracker.trackAnalyticsEvent({
    //                             event: AnalyticsEvent.PageLoaded,
    //                             context: {url: window.location.href}
    //                         }, true),
    //                             this.globalStore.init = true;
    //                     });
    //             },
    //         }));
    //
    //         // if (sentryDsn) {
    //         //     Sentry.init({
    //         //         app,
    //         //         dsn: sentryDsn,
    //         //         environment: sentryEnv,
    //         //         integrations: [
    //         //             new Sentry.BrowserTracing(),
    //         //             new Sentry.Replay(),
    //         //         ],
    //         //         tracePropagationTargets: [
    //         //             'https://localhost',
    //         //             'https://qira.*',
    //         //         ],
    //         //         tracesSampleRate: 0.1,
    //         //         replaysSessionSampleRate: 0.1,
    //         //         replaysOnErrorSampleRate: 1.0,
    //         //     });
    //         // }
    //
    //         app.use(i18n);
    //         app.use(createPinia());
    //         app.use(VCalendar, {componentPrefix: 'vc'});
    //         app.use(DraggablePlugin);
    //
    //         app.use(createVuetify({
    //             icons: {
    //                 defaultSet: 'mdi',
    //                 aliases,
    //                 sets: {
    //                     mdi,
    //                 },
    //             },
    //             theme: {
    //                 defaultTheme: 'qiraLightTheme',
    //                 themes: {
    //                     qiraLightTheme,
    //                 },
    //             },
    //             locale: {
    //                 adapter: createVueI18nAdapter({i18n, useI18n}),
    //             },
    //             components: {
    //                 // general components
    //                 VApp,
    //                 VContainer,
    //                 VMain,
    //                 VFooter,
    //                 VProgressLinear,
    //                 MainHeader,
    //                 MainFooter,
    //                 VBtn,
    //
    //                 // extended vuetify components as async Components
    //                 VCarouselAsync: defineAsyncComponent(() => import('../extend/v-carousel-async/VCarouselAsync.vue')),
    //                 VCarouselItemAsync: defineAsyncComponent(() => import('../extend/v-carousel-item-async/VCarouselItemAsync.vue')),
    //                 VExpansionPanelAsync: defineAsyncComponent(() => import('../extend/v-expansion-panel-async/VExpansionPanelAsync.vue')),
    //                 VExpansionPanelTextAsync: defineAsyncComponent(() => import('../extend/v-expansion-panel-text-async/VExpansionPanelTextAsync.vue')),
    //                 VExpansionPanelTitleAsync: defineAsyncComponent(() => import('../extend/v-expansion-panel-title-async/VExpansionPanelTitleAsync.vue')),
    //                 VExpansionPanelsAsync: defineAsyncComponent(() => import('../extend/v-expansion-panels-async/VExpansionPanelsAsync.vue')),
    //
    //                 // define RMI components as async components
    //                 RepairGuideTechnicalDataCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-technical-data-category/RepairGuideTechnicalDataCategory.vue')),
    //                 RepairGuideInspectionDataCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-inspection-data-category/RepairGuideInspectionDataCategory.vue')),
    //                 RepairGuideBatteryManagementCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-battery-management-category/RepairGuideBatteryManagementCategory.vue')),
    //                 RepairGuideCabinAirFilterCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-cabin-air-filter-category/RepairGuideCabinAirFilterCategory.vue')),
    //                 RepairGuideComponentLocationCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-component-location-category/RepairGuideComponentLocationCategory.vue')),
    //                 RepairGuideWorkingValueCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-working-value-category/RepairGuideWorkingValueCategory.vue')),
    //                 RepairGuideRepairInstructionCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-repair-instruction-category/RepairGuideRepairInstructionCategory.vue')),
    //                 RepairGuideWarningLightCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-warning-light-category/RepairGuideWarningLightCategory.vue')),
    //                 RepairGuideFuseRelayCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-fuse-relay-category/RepairGuideFuseRelayCategory.vue')),
    //                 RepairGuideServiceResetCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-service-reset-category/RepairGuideServiceResetCategory.vue')),
    //                 RepairGuideWiringDiagramCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-wiring-diagram-category/RepairGuideWiringDiagramCategory.vue')),
    //                 RepairGuideTimingBeltCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-timing-belt-category/RepairGuideTimingBeltCategory.vue')),
    //                 RepairGuideTechnicalDrawingsCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-technical-drawings-category/RepairGuideTechnicalDrawingsCategory.vue')),
    //                 RepairGuideLubricantCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-lubricant-category/RepairGuideLubricantCategory.vue')),
    //                 RepairGuideComponentTestValuesCategory: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-component-test-values-category/RepairGuideComponentTestValuesCategory.vue')),
    //
    //                 // define CMS Embed Components as async Components
    //                 CtaBlock: defineAsyncComponent(() => import('../component/cms-embed/cta-block/CtaBlock.vue')),
    //                 AppImage: defineAsyncComponent(() => import('../component/general/app-image/AppImage.vue')),
    //                 FaqGroup: defineAsyncComponent(() => import('../component/cms-embed/faq-group/FaqGroup.vue')),
    //                 TestimonialGroup: defineAsyncComponent(() => import('../component/cms-embed/testimonial-group/TestimonialGroup.vue')),
    //                 AdvancedFeatureGroup: defineAsyncComponent(() => import('../component/cms-embed/advanced-feature-group/AdvancedFeatureGroup.vue')),
    //                 SimpleFeatureGroup: defineAsyncComponent(() => import('../component/cms-embed/simple-feature-group/SimpleFeatureGroup.vue')),
    //                 ResourcesBlock: defineAsyncComponent(() => import('../component/cms-embed/resources-block/resources-block/ResourcesBlock.vue')),
    //                 CourseSlider: defineAsyncComponent(() => import('../component/cms-embed/course-slider/CourseSlider.vue')),
    //                 TeaserMultiBlock: defineAsyncComponent(() => import('../component/cms-embed/teaser/TeaserMultiBlock.vue')),
    //                 TeaserSingleNarrowBlock: defineAsyncComponent(() => import('../component/cms-embed/teaser/TeaserSingleNarrowBlock.vue')),
    //                 TeaserSingleBigBlock: defineAsyncComponent(() => import('../component/cms-embed/teaser/TeaserSingleBigBlock.vue')),
    //                 TeaserBlock: defineAsyncComponent(() => import('../component/cms-embed/teaser/block/TeaserBlock.vue')),
    //                 TeaserBlockPlayButton: defineAsyncComponent(() => import('../component/cms-embed/teaser/block/TeaserBlockPlayButton.vue')),
    //                 TeaserBlockVideo: defineAsyncComponent(() => import('../component/cms-embed/teaser/block/TeaserBlockVideo.vue')),
    //                 TeaserBlockAdvancedVideo: defineAsyncComponent(() => import('../component/cms-embed/teaser/block/TeaserBlockAdvancedVideo.vue')),
    //                 CourseSliderGroup: defineAsyncComponent(() => import('../component/cms-embed/course-slider/CourseSliderGroup.vue')),
    //                 CourseSearchBar: defineAsyncComponent(() => import('../component/course/course-search-bar/CourseSearchBar.vue')),
    //                 SupportRequestCredits: defineAsyncComponent(() => import('../component/support-request/support-request-credits/SupportRequestCredits.vue')),
    //                 SubscriptionPlanList: defineAsyncComponent(() => import('../component/cms-embed/subscription/SubscriptionPlanList.vue')),
    //                 CourseList: defineAsyncComponent(() => import('../component/cms-embed/course-list/CourseList.vue')),
    //                 CourseCategories: defineAsyncComponent(() => import('../component/cms-embed/course-categories/CourseCategories.vue')),
    //                 SupportRequestList: defineAsyncComponent(() => import('../component/cms-embed/support-request-list/SupportRequestList.vue')),
    //                 SupportRequestCreate: defineAsyncComponent(() => import('../component/support-request/support-request-create/SupportRequestCreate.vue')),
    //                 PartnerBlock: defineAsyncComponent(() => import('../component/cms-embed/partner-block/PartnerBlock.vue')),
    //                 ButtonList: defineAsyncComponent(() => import('../component/cms-embed/button-list/ButtonList.vue')),
    //                 EarlyAccessBlock: defineAsyncComponent(() => import('../component/cms-embed/early-access-block/EarlyAccessBlock.vue')),
    //                 EarlyAccessFooter: defineAsyncComponent(() => import('../component/general/early-access/early-access-footer/EarlyAccessFooter.vue')),
    //                 RepairGuideForm: defineAsyncComponent(() => import('../component/repair-guide/repair-guide-form/RepairGuideForm.vue')),
    //                 SupportCreditList: defineAsyncComponent(() => import('../component/cms-embed/support-credit/SupportCreditList.vue')),
    //                 ContactForm: defineAsyncComponent(() => import('../component/cms-embed/contact-form/ContactForm.vue')),
    //                 SmartAssistant: defineAsyncComponent(() => import('../component/cms-embed/smart-assistant/SmartAssistant.vue')),
    //                 PaymentRequiredTeaser: defineAsyncComponent(() => import('../component/general/payment-required-teaser/PaymentRequiredTeaser.vue')),
    //             },
    //         }));
    //
    //         app.mount(mount);
    //     });
}
