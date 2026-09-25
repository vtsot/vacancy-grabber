import Axios, {AxiosRequestConfig} from 'axios';
import {EventBus, events} from './EventBus';
import RouteGenerator from './RouteGenerator';
import RouteParams from "../types/RouteParams";
import ErrorResponse from "../model/transport-response/ErrorResponse";
import SuccessResponse from "../model/transport-response/SuccessResponse";
import {ServerValidationErrorInterface} from "../types/ValidationErrorTypes";
// import {throttleAdapterEnhancer} from 'axios-extensions';

let currentLocale = 'en_GB';

const HttpClient = Axios.create({
    baseURL: '/',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0',
    },
    timeout: 30000,
    // adapter: throttleAdapterEnhancer(cacheAdapterEnhancer(Axios.defaults.adapter)),
    // adapter: throttleAdapterEnhancer(Axios.defaults.adapter as AxiosAdapter),
});

EventBus.on(events.LOGIN_SUCCESS, function (token) {
    HttpClient.defaults.headers.common['Authorization'] = 'Bearer ' + token;
});

EventBus.on(events.LOGOUT_SUCCESS, function () {
    EventBus.emit(events.LOGIN_REQUIRED);
});

EventBus.on(events.LOGIN_REQUIRED, function () {
    delete HttpClient.defaults.headers.common['Authorization'];
    localStorage.removeItem('token');
    localStorage.removeItem('refreshToken');
    window.location.href = RouteGenerator.generate('web.auth.logout', {}, true);
});

EventBus.on(events.LOCALE_CHANGE, function (locale) {
    currentLocale = locale as string;
});

HttpClient.interceptors.response.use(response => {
    return new SuccessResponse(response);
}, error => {
    if (error.response
        && error.response.status === 401
        && error.request.responseURL !== RouteGenerator.generate('api.auth.login', {}, true)
    ) {
        EventBus.emit(events.LOGIN_REQUIRED);
    }

    const data = error.response.data as ServerValidationErrorInterface;

    console.error('HttpClient.interceptors.response: Bad request', data);
    throw new ErrorResponse(
        data.code || 400,
        data.message ?? 'Bad request',
        data.errors
    );
});

export default {
    setLocale(locale: string) {
        currentLocale = locale;
    },

    hasAuthorization(): boolean {
        return !!HttpClient.defaults.headers.common['Authorization'];
    },

    get(routeName: string, routeParams: RouteParams = {}): Promise<SuccessResponse> {
        return HttpClient.get(RouteGenerator.generate(routeName, routeParams), {
            headers: {
                'Accept-Language': currentLocale,
            },
        } as AxiosRequestConfig);
    },

    post(routeName: string, routeParams: RouteParams = {}, data: object = {}): Promise<SuccessResponse> {
        return HttpClient.post(RouteGenerator.generate(routeName, routeParams), data, {
            headers: {
                'Accept-Language': currentLocale,
            },
        } as AxiosRequestConfig);
    },

    put(routeName: string, routeParams: RouteParams = {}, data: object = {}): Promise<SuccessResponse> {
        return HttpClient.put(RouteGenerator.generate(routeName, routeParams), data, {
            headers: {
                'Accept-Language': currentLocale,
            },
        } as AxiosRequestConfig);
    },

    delete(routeName: string, routeParams: RouteParams = {}): Promise<SuccessResponse> {
        return HttpClient.delete(RouteGenerator.generate(routeName, routeParams), {
            headers: {
                'Accept-Language': currentLocale,
            },
        } as AxiosRequestConfig);
    },

    patch(routeName: string, routeParams: RouteParams = {}, data: object = {}): Promise<SuccessResponse> {
        return HttpClient.patch(RouteGenerator.generate(routeName, routeParams), data, {
            headers: {
                'Accept-Language': currentLocale,
            },
        } as AxiosRequestConfig);
    },
};
