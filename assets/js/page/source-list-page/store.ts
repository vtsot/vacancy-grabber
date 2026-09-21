import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import ErrorResponse from "../../model/transport-response/ErrorResponse";
import DataCollection from "../../model/transport-response/DataCollection";
import {InferType as YupInferType} from "yup";
import SourceInfo from "../../model/source/SourceInfo";
import SourceInfoFactory, {SourceInfoSchema} from "../../model-factory/source/SourceInfoFactory";
import SourceInfoFilters from "../../model/source/SourceInfoFilters";
import {clearFormErrors, parseFormErrors} from "../../module/HandleApiResponse";
import ErrorValidation from "../../model/transport-response/ErrorValidation";

const sourceInfoFactory = new SourceInfoFactory();
const paginator = new Paginator();
const filters = new SourceInfoFilters(paginator.page, paginator.perPage);

export const useSourceListPageStore = defineStore('source-list-page', {
    state: () => ({
        loading: false,
        items: [] as SourceInfo[],
        filters: filters,
        paginator: new Paginator(),
        errors: new ErrorValidation(),
    }),

    actions: {

        async createSourceDetails(data: object): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.source.create', {}, data)
                .finally(() => {
                    this.loading = false
                });
        },

        async loadSourceDetails(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .get('api.source.details', {id})
                .finally(() => {
                    this.loading = false;
                });
        },

        async loadSourceCollection(): Promise<void> {
            this.loading = true;
            clearFormErrors(this.errors);

            return HttpClient
                .get('api.source.collection', this.filters.normalizeUrlParams())
                .then((response: SuccessResponse) => {

                    const collection = response.dataCollection;
                    if (collection instanceof DataCollection) {
                        this.items.splice(0, this.items.length, ...collection.items.map(
                            item => sourceInfoFactory.create(item as YupInferType<typeof SourceInfoSchema>),
                        ));
                        //this.filters = collection.filters;
                        this.paginator = collection.paginator;
                    }
                }, (error: ErrorResponse) => {
                    parseFormErrors(error, this.errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        async itemActive(id: string, active: boolean): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .patch('api.source.patch', {id}, {active})
                .finally(() => {
                    this.loading = false;
                });
        },

        async itemGrab(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .post('api.source.grab', {id: id})
                .finally(() => {
                    this.loading = false;
                });
        },

        updateAction(id: string) {
            this.loading = true;
            //clearFormErrors(this.errors);

            console.log('updateAction: ', id)
            this.loading = false;
        },

        async itemDelete(id: string): Promise<SuccessResponse> {
            this.loading = true;
            return HttpClient
                .delete('api.source.delete', {id: id})
                .finally(() => {
                    this.loading = false;
                });
        },

    },
});
