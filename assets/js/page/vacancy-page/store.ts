import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import ErrorResponse from "../../model/transport-response/ErrorResponse";
import VacancyInfoFilters from "../../model/vacancy/VacancyInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import VacancyDetailsFactory from "../../model-factory/vacancy/VacancyDetailsFactory";

export const usePageStore = defineStore('vacancy-page-store', {
    state: () => ({
        loading: false,
        filters: new VacancyInfoFilters(),
        errors: new ErrorValidation(),
        paginator: new Paginator(),
        sortBy: [] as string[] | [] | any as any,
        availableSortBy: [] as string[] | [],
        availableFilters: {
            source: [] as {id: string, name: string} | [] | null,
            company: [] as {id: string, name: string} | [] | null,
            status: [] as {value: string, name: string} | [] | null,
        },
        appliedFilters: {} as object | null,
    }),

    getters: {
        hasSortBy: (state) => (name: string): boolean => state.availableSortBy.includes(name),
    },

    actions: {

        async createDetails(data: object): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.vacancy.post', {}, data)
                .finally(() => this.loading = false);
        },

        updateDetails(id: string, data: object) {
            this.loading = true;
            return HttpClient
                .put('api.vacancy.put', {id}, data)
                .finally(() => this.loading = false);
        },

        async loadDetails(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .get('api.vacancy.details', {id})
                .then((response: SuccessResponse) => {
                    const factory = new VacancyDetailsFactory();
                    if (!factory.supports(response.data)) {
                        console.error('vacancy-page-store:loadDetails: ', response.data);
                        throw new ErrorResponse(400, 'Unable to load vacancy details');
                    }

                    return response;
                })
                .finally(() => this.loading = false);
        },

        async loadCollection() {
            this.loading = true;
            return HttpClient
                .get('api.vacancy.collection', this.filters.normalizeUrlParams())
                .finally(() => this.loading = false);
        },

        async itemActive(id: string, active: boolean): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .patch('api.vacancy.patch', {id}, {active})
                .finally(() => this.loading = false);
        },

        async itemGrab(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.vacancy.grab', {id: id})
                .finally(() => this.loading = false);
        },

        async itemDelete(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .delete('api.vacancy.delete', {id: id})
                .finally(() => this.loading = false);
        },

    },
});
