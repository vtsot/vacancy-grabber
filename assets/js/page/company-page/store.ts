import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import CompanyInfoFilters from "../../model/company/CompanyInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";

export const useCompanyPageStore = defineStore('company-page-store', {
    state: () => ({
        loading: false,
        filters: new CompanyInfoFilters(),
        errors: new ErrorValidation(),
        paginator: new Paginator(),
        sortBy: [] as string[] | [] | any as any,
        availableSortBy: [] as string[] | [],
        availableFilters: {
        //     source: [] as {id: string, name: string} | [] | null,
        //     company: [] as {id: string, name: string} | [] | null,
        //     status: [] as {value: string, name: string} | [] | null,
        } as object,
        appliedFilters: {} as object | null,
    }),

    getters: {
        hasSortBy: (state) => (name: string): boolean => state.availableSortBy.includes(name),
    },

    actions: {

        async create(data: object): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.company.post', {}, data)
                .finally(() => this.loading = false);
        },

        async update(id: string, data: object) {
            this.loading = true;
            return HttpClient
                .put('api.company.put', {id}, data)
                .finally(() => this.loading = false);
        },

        async loadDetails(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .get('api.company.details', {id})
                .finally(() => this.loading = false);
        },

        async loadSummary() {
            this.loading = true;
            return HttpClient
                .get('api.company.summary')
                .finally(() => this.loading = false);
        },

        async loadCollection() {
            this.loading = true;
            return HttpClient
                .get('api.company.collection', this.filters.normalizeUrlParams())
                .finally(() => this.loading = false);
        },

        patch(id: string, data: object) {
            this.loading = true;
            return HttpClient
                .patch('api.company.patch', {id}, data)
                .finally(() => this.loading = false);
        },

        async delete(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .delete('api.company.delete', {id: id})
                .finally(() => this.loading = false);
        },
    },
});
