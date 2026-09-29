import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import SourceInfo from "../../model/source/SourceInfo";
import SourceInfoFilters from "../../model/source/SourceInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";

export const useSourcePageStore = defineStore('source-page-store', {
    state: () => ({
        loading: false,
        items: [] as SourceInfo[], // original loaded items
        filters: new SourceInfoFilters(),
        errors: new ErrorValidation(),
        paginator: new Paginator(),
        availableSortBy: [] as string[] | [],
        availableFilters: {} as object | {},
        appliedFilters: {} as object | null,
    }),

    getters: {
        hasSortBy: (state) => (name: string): boolean => state.availableSortBy.includes(name),
    },

    actions: {

        async create(data: object): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.source.post', {}, data)
                .finally(() => this.loading = false);
        },

        async update(id: string, data: object) {
            this.loading = true;
            return HttpClient
                .put('api.source.put', {id}, data)
                .finally(() => this.loading = false);
        },

        async loadDetails(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .get('api.source.details', {id})
                .finally(() => this.loading = false);
        },

        async loadSummary() {
            this.loading = true;
            return HttpClient
                .get('api.source.summary')
                .finally(() => this.loading = false);
        },

        async loadCollection() {
            this.loading = true;
            return HttpClient
                .get('api.source.collection', this.filters.normalizeUrlParams())
                .finally(() => this.loading = false);
        },

        async patch(id: string, data: object) {
            this.loading = true;
            return HttpClient
                .patch('api.source.patch', {id}, data)
                .finally(() => this.loading = false);
        },

        async delete(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .delete('api.source.delete', {id})
                .finally(() => this.loading = false);
        },

        async grab(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.source.grab', {id: id})
                .finally(() => this.loading = false);
        },
    },
});
