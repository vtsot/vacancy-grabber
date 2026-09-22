import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import ErrorResponse from "../../model/transport-response/ErrorResponse";
import SourceInfo from "../../model/source/SourceInfo";
import SourceInfoFilters from "../../model/source/SourceInfoFilters";
import {clearFormErrors, parseErrorValidation, parseFormErrors} from "../../module/HandleApiResponse";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import SourceDetailsFactory from "../../model-factory/source/SourceDetailsFactory";
import SourceInfoCollection from "../../model/source/SourceInfoCollection";
import SourceInfoCollectionFactory from "../../model-factory/source/SourceInfoCollectionFactory";

export const useSourceListPageStore = defineStore('source-page', {
    state: () => ({
        loading: false,
        items: [] as SourceInfo[],
        filters: new SourceInfoFilters(),
        paginator: new Paginator(),
        errors: new ErrorValidation(),
        applied: {},
        available: {},
        sortBy: [] as string[],
    }),

    getters: {
        applied: (state) => state.applied,
        hasAvailable: (state) => (name: string): boolean => Object.keys(state.available || {}).includes(name),
        hasSortBy: (state) => (name: string): boolean => state.sortBy.includes(name),
    },

    actions: {

        async createSourceDetails(data: object): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.source.post', {}, data)
                .finally(() => this.loading = false);
        },

        updateSourceDetails(id: string, data: object) {
            this.loading = true;
            return HttpClient
                .put('api.source.put', {id}, data)
                .finally(() => this.loading = false);
        },

        async loadSourceDetails(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .get('api.source.details', {id})
                .then((response: SuccessResponse) => {
                    const factory = new SourceDetailsFactory();
                    if (!factory.supports(response.data)) {
                        throw new ErrorResponse(400, 'Unable to load source details');
                    }

                    return response;
                })
                .finally(() => this.loading = false);
        },

        // async loadSourceCollection2(): Promise<SuccessResponse> {
        //     this.loading = true;
        //     return HttpClient
        //         .get('api.source.collection')
        //         .then((response: SuccessResponse) => {
        //             const factory = new SourceInfoCollectionFactory();
        //             if (!factory.supports(response.data)
        //                 || !response.dataCollection instanceof DataCollection
        //             ) {
        //                 parseErrorValidation({message: 'Wrong data source collection received'}, this.errors);
        //                 return;
        //             }
        //
        //             return response;
        //         })
        //         .finally(() => this.loading = false);
        // },

        async loadSourceCollection(): Promise<void> {
            this.loading = true;
            clearFormErrors(this.errors);

            return HttpClient
                .get('api.source.collection', this.filters.normalizeUrlParams())
                .then((response: SuccessResponse) => {
                    const collection = response.dataCollection
                    const factory = new SourceInfoCollectionFactory();
                    if (!factory.supports(response.data)
                        || !collection instanceof SourceInfoCollection
                    ) {
                        parseErrorValidation({message: 'Wrong data source collection received'}, this.errors);
                        return;
                    }

                    this.items.splice(0, this.items.length, ...factory.create(response.data).items);
                    this.paginator = collection.paginator;
                    this.applied = collection.filters.applied;
                    this.available = collection.filters.available;
                    this.sortBy.splice(0, this.sortBy.length, ...collection.filters.sortBy);

                }, (error: ErrorResponse) => {
                    parseFormErrors(error, this.errors);
                })
                .finally(() => this.loading = false);
        },

        async itemActive(id: string, active: boolean): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .patch('api.source.patch', {id}, {active})
                .finally(() => this.loading = false);
        },

        async itemGrab(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.source.grab', {id: id})
                .finally(() => this.loading = false);
        },

        async itemDelete(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .delete('api.source.delete', {id: id})
                .finally(() => this.loading = false);
        },

    },
});
