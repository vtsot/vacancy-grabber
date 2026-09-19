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
import SourceDetails from "../../model/source/SourceDetails";
import SourceDetailsFactory from "../../model-factory/source/SourceDetailsFactory";

const paginator = new Paginator();
const filters = new SourceInfoFilters(paginator.page, paginator.perPage);
const sourceInfoFactory = new SourceInfoFactory();
const sourceDetailsFactory = new SourceDetailsFactory();

export const useSourceListPageStore = defineStore('source-list-page', {
    state: () => ({
        loading: false,
        items: [] as SourceInfo[],
        filters: filters,
        paginator: new Paginator(),
        sourceDetails: null as SourceDetails | null,
    }),

    actions: {

        async loadSourceDetails(id: string): Promise<void> {
            this.loading = true;

            return HttpClient
                .get('api.source.details', {id})
                .then((response: SuccessResponse) => {

                    this.sourceDetails = sourceInfoFactory.supports(response.data)
                        ? sourceInfoFactory.create(response.data)
                        : null;

                    console.log('this.sourceDetails: ', this.sourceDetails);

                }, (error: ErrorResponse) => {
                    console.log(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        async loadSourceCollection(): Promise<void> {
            this.loading = true;

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
                    console.log(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        async itemActive(id: string, active: boolean): Promise<void> {
            this.loading = true;

            return HttpClient
                .patch('api.source.patch', {id}, {active})
                .then((response: SuccessResponse) => {
                    console.log('store:itemActive:response: ', response);

                    this.sourceDetails = sourceDetailsFactory.supports(response.data)
                        ? sourceDetailsFactory.create(response.data)
                        : null;

                    console.log('this.sourceDetails: ', this.sourceDetails);


                    // const collection = response.dataCollection;
                    // const factory = new SourceInfoFactory();
                    //
                    // if (collection instanceof DataCollection) {
                    //     this.items.splice(0, this.items.length, ...collection.items.map(
                    //         item => factory.create(item as YupInferType<typeof SourceInfoSchema>),
                    //     ));
                    //     //this.filters = collection.filters;
                    //     this.paginator = collection.paginator;
                    // }
                }, (error: ErrorResponse) => {
                    console.log(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        async itemGrab(id: string): Promise<void> {
            this.loading = true;

            return HttpClient
                .post('api.source.grab', {id: id})
                .then((response: SuccessResponse) => {

                    // const collection = response.dataCollection;
                    // const factory = new SourceInfoFactory();
                    //
                    // if (collection instanceof DataCollection) {
                    //     this.items.splice(0, this.items.length, ...collection.items.map(
                    //         item => factory.create(item as YupInferType<typeof SourceInfoSchema>),
                    //     ));
                    //     //this.filters = collection.filters;
                    //     this.paginator = collection.paginator;
                    // }
                }, (error: ErrorResponse) => {
                    console.log(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        updateAction(id: string) {
            this.loading = true;
            console.log('updateAction: ', id)
            this.loading = false;
        },

        async itemDelete(id: string): Promise<void> {
            this.loading = true;

            return HttpClient
                .delete('api.source.delete', {id: id})
                .then((response: SuccessResponse) => {

                    // const collection = response.dataCollection;
                    // const factory = new SourceInfoFactory();
                    //
                    // if (collection instanceof DataCollection) {
                    //     this.items.splice(0, this.items.length, ...collection.items.map(
                    //         item => factory.create(item as YupInferType<typeof SourceInfoSchema>),
                    //     ));
                    //     //this.filters = collection.filters;
                    //     this.paginator = collection.paginator;
                    // }

                    //this.loadSourceCollection();
                }, (error: ErrorResponse) => {
                    console.log(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

    },
});
