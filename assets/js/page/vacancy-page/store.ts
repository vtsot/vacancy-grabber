import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import ErrorResponse from "../../model/transport-response/ErrorResponse";
import VacancyInfoFilters from "../../model/vacancy/VacancyInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import VacancyDetailsFactory from "../../model-factory/vacancy/VacancyDetailsFactory";

interface VacancyPageStoreInterface {
    // sortBy: string[],
    // available: {
    //     company: {id: string, name: string}[],
    //     status: {value: string, name: string}[],
    // },
    // applied: object | {},
    loading: boolean,
    filters: VacancyInfoFilters,
    errors: ErrorValidation,
    sortBy: string[],
    availableSortBy: string[],
}

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

        // available: {
        //     company: [] as {id: string, name: string} | [] | any as any,
        //     status: [] as {value: string, name: string} [] | any as any,
        // } as any,
        // applied: {} as object | {} | any as any,

        // this._availableFilters = availableFilters;
        // this._appliedFilters = appliedFilters;

    }),

    getters: {
        //applied: (state) => state.applied,
        //hasAvailable: (state) => (name: string): boolean => Object.keys(state.available || {}).includes(name),
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


        // async loadCollection2(): Promise<SuccessResponse> {
        //     this.loading = true;
        //     return //processApiResponse(
        //         HttpClient
        //             .get('api.vacancy.collection', this.filters.normalizeUrlParams())
        //             .finally(() => this.loading = false),
        //         // this.errors,
        //         // new VacancyInfoCollectionFactory()
        //     // );
        // },

        async loadCollection() {
            this.loading = true;
            return HttpClient
                .get('api.vacancy.collection', this.filters.normalizeUrlParams())
                .finally(() => this.loading = false);
        },

        //
        // async loadCollection(): Promise<void> {
        //     this.loading = true;
        //     clearFormErrors(this.errors);
        //
        //     return HttpClient
        //         .get('api.vacancy.collection', this.filters.normalizeUrlParams())
        //         .then((response: SuccessResponse) => {
        //
        //
        //             const collection = response.dataCollection
        //             const factory = new VacancyInfoCollectionFactory();
        //             if (!factory.supports(response.data)
        //                 || !collection instanceof VacancyInfoCollection
        //             ) {
        //                 parseErrorValidation({message: 'aaaWrong data vacancy collection received'}, this.errors);
        //                 return;
        //             }
        //
        //             //const itemFactory = new VacancyInfoFactory();
        //             const companySummaryFactory = new CompanySummaryFactory();
        //             //
        //             // //this.items.splice(0, this.items.length, ...factory.create(response.data).items);
        //             //
        //             // try {
        //             //     this.items.splice(0, this.items.length, ...itemFactory.create(response.data).items);
        //             // } catch (e) {
        //             //     console.log('e: ', e);
        //             // }
        //
        //
        //             this.items.splice(0, this.items.length, ...factory.create(response.data).items);
        //             this.paginator = collection.paginator;
        //             this.applied = collection.filters.applied;
        //             // this.available.company = collection.filters.available.company?.map(item => companySummaryFactory.create(item));
        //             // this.sortBy.splice(0, this.sortBy.length, ...collection.filters.sortBy);
        //
        //         }, (error: ErrorResponse) => {
        //             parseFormErrors(error, this.errors);
        //         })
        //         .finally(() => this.loading = false);
        // },

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
