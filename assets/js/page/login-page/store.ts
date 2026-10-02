import {defineStore} from 'pinia';
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import {computed, ref} from "vue";
import {EventBus, events} from "../../module/EventBus";
import {ClientValidationErrorInterface} from "../../types/ValidationErrorTypes";
import RouteGenerator from "../../module/RouteGenerator";
import {parseFormErrors} from "../../module/HandleApiResponse";
import ErrorResponse from "../../model/transport-response/ErrorResponse";

export const useLoginPageStore = defineStore('login-page-store', () => {
    // State
    const isLoading = ref(false);

    // Getters
    const loading = computed(() => isLoading.value);

    // Actions
    const apiLogin = async (username: string, password: string, errors: ClientValidationErrorInterface): Promise<void> => {
        isLoading.value = true;
        return HttpClient
            .post('api.auth.login', {}, {username, password})
            .then((response: SuccessResponse) => {
                setUserToken(response.data.token, response.data.refresh_token ?? null);
                webLogin(response.data.token, errors);
            })
            .catch((response: ErrorResponse) => {
                parseFormErrors(response, errors);
            })
            .finally(() => isLoading.value = false);
    };

    const webLogin = async (token: string, errors: ClientValidationErrorInterface): Promise<void> => {
        isLoading.value = true;
        return HttpClient
            .post('web.auth.login', {}, {token, target: null})
            .then((response: SuccessResponse) => {
                window.location.href = response.data.redirect ?? RouteGenerator.generate('web.homepage', {}, true);
            }).catch((response: ErrorResponse) => {
                parseFormErrors(response, errors);
            })
            .finally(() => isLoading.value = false);
    };

    const setUserToken = (token: string | null, refreshToken: string | null = null): void => {
        EventBus.emit(events.LOGIN_SUCCESS, token);
        token !== null
            ? localStorage.setItem('token', token)
            : localStorage.removeItem('token');

        refreshToken !== null
            ? localStorage.setItem('refreshToken', refreshToken)
            : localStorage.removeItem('refreshToken');

    };

    return {
        loading,
        // Actions
        apiLogin,
    };
});
