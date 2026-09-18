import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import ErrorResponse from "../../model/transport-response/ErrorResponse";
import DataCollection from "../../model/transport-response/DataCollection";
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import SourceInfo from "../../model/source/SourceInfo";
import SourceInfoFactory, {SourceInfoSchema} from "../../model-factory/source/SourceInfoFactory";
import SourceInfoFilters from "../../model/source/SourceInfoFilters";

const paginator = new Paginator();
const filters = new SourceInfoFilters(paginator.page, paginator.perPage);

export const useSourceListPageStore = defineStore('source-list-page', {
    state: () => ({
        loading: false,
        items: [] as SourceInfo[],
        filters: filters,
        paginator: new Paginator(),
    }),

    actions: {
        loadSourceCollection() {
            this.loading = true;

            HttpClient
                .get('api.source.collection', this.filters.normalizeUrlParams())
                .then((response: SuccessResponse) => {

                    const collection = response.dataCollection;
                    const factory = new SourceInfoFactory();

                    if (collection instanceof DataCollection) {
                        this.items.splice(0, this.items.length, ...collection.items.map(
                            item => factory.create(item as YupInferType<typeof SourceInfoSchema>),
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

        itemActive(id: string, active: boolean) {
            this.loading = true;

            HttpClient
                .patch('api.source.patch', {id: id}, {active: active})
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

        itemGrab(id: string) {
            this.loading = true;

            HttpClient
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

        itemDelete(id: string) {
            this.loading = true;

            HttpClient
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
