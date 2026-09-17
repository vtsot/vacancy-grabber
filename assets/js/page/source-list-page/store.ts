import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import Filters from "../../model/transport-response/Filters";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import ErrorResponse from "../../model/transport-response/ErrorResponse";
import DataCollection from "../../model/transport-response/DataCollection";
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import SourceInfo from "../../model/source/SourceInfo";
import SourceInfoFactory, {SourceInfoSchema} from "../../model-factory/source/SourceInfoFactory";
import SourceInfoFilters from "../../model/source/SourceInfoFilters";

export const useSourceListPageStore = defineStore('source-list-page', {
    state: () => ({
        loading: false,
        items: [] as SourceInfo[],
        filters: {} as SourceInfoFilters,
        paginator: null as Paginator | null,
    }),

    actions: {

        // setSourceInfoFilters(filters: SourceInfoFilters) {
        //     this.filters = filters;
        // },

        loadSourceCollection(filters: SourceInfoFilters | null) {
            this.loading = true;
            const params = filters.normalizeUrlParams();

            HttpClient
                .get('api.source.collection', params)
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

        saveActive(item: SourceInfo) {
            this.loading = true;

            HttpClient
                .patch('api.source.patch', {id: item.id, active: item.active})
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
    },
});
