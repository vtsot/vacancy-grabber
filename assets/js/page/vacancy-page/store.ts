import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import ErrorResponse from "../../model/transport-response/ErrorResponse";
import VacancyInfo from "../../model/vacancy/VacancyInfo";
import VacancyInfoFilters from "../../model/vacancy/VacancyInfoFilters";
import {clearFormErrors, parseErrorValidation, parseFormErrors} from "../../module/HandleApiResponse";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import VacancyDetailsFactory from "../../model-factory/vacancy/VacancyDetailsFactory";
import VacancyInfoCollection from "../../model/vacancy/VacancyInfoCollection";
import VacancyInfoCollectionFactory from "../../model-factory/vacancy/VacancyInfoCollectionFactory";
import CompanySummary from "../../model/company/CompanySummary";
import CompanySummaryFactory from "../../model-factory/company/CompanySummaryFactory";

export const usePageStore = defineStore('vacancy-page', {
    state: () => ({
        loading: false,
        items: [] as VacancyInfo[],
        filters: new VacancyInfoFilters(),
        paginator: new Paginator(),
        errors: new ErrorValidation(),
        applied: {},
        available: {
            company: [] as CompanySummary[],
        },
        sortBy: [] as string[],
    }),

    getters: {
        applied: (state) => state.applied,
        hasAvailable: (state) => (name: string): boolean => Object.keys(state.available || {}).includes(name),
        hasSortBy: (state) => (name: string): boolean => state.sortBy.includes(name),
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
                        throw new ErrorResponse(400, 'Unable to load vacancy details');
                    }

                    return response;
                })
                .finally(() => this.loading = false);
        },

        // async loadVacancyCollection2(): Promise<SuccessResponse> {
        //     this.loading = true;
        //     return HttpClient
        //         .get('api.Vacancy.collection')
        //         .then((response: SuccessResponse) => {
        //             const factory = new VacancyInfoCollectionFactory();
        //             if (!factory.supports(response.data)
        //                 || !response.dataCollection instanceof DataCollection
        //             ) {
        //                 parseErrorValidation({message: 'Wrong data Vacancy collection received'}, this.errors);
        //                 return;
        //             }
        //
        //             return response;
        //         })
        //         .finally(() => this.loading = false);
        // },

        async loadCollection(): Promise<void> {
            this.loading = true;
            clearFormErrors(this.errors);

            return HttpClient
                .get('api.vacancy.collection', this.filters.normalizeUrlParams())
                .then((response: SuccessResponse) => {
                    const collection = response.dataCollection
                    const factory = new VacancyInfoCollectionFactory();
                    if (!factory.supports(response.data)
                        || !collection instanceof VacancyInfoCollection
                    ) {
                        parseErrorValidation({message: 'Wrong data vacancy collection received'}, this.errors);
                        return;
                    }

                    const companySummaryFactory = new CompanySummaryFactory();

                    this.items.splice(0, this.items.length, ...factory.create(response.data).items);
                    this.paginator = collection.paginator;
                    this.applied = collection.filters.applied;
                    this.available.company = collection.filters.available.company?.map(item => companySummaryFactory.create(item));
                    this.sortBy.splice(0, this.sortBy.length, ...collection.filters.sortBy);

                }, (error: ErrorResponse) => {
                    parseFormErrors(error, this.errors);
                })
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
