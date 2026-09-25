import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import ErrorResponse from "../../model/transport-response/ErrorResponse";
import GrabLogInfo from "../../model/grab-log/GrabLogInfo";
import GrabLogInfoFilters from "../../model/grab-log/GrabLogInfoFilters";
import {clearFormErrors, parseErrorValidation, parseFormErrors} from "../../module/HandleApiResponse";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import GrabLogInfoCollection from "../../model/grab-log/GrabLogInfoCollection";
import GrabLogInfoCollectionFactory from "../../model-factory/grab-log/GrabLogInfoCollectionFactory";

export const usePageStore = defineStore('grab-log-page-store', {
    state: () => ({
        loading: false,
        items: [] as GrabLogInfo[],
        filters: new GrabLogInfoFilters(),
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
        async loadCollection(): Promise<void> {
            this.loading = true;
            clearFormErrors(this.errors);

            return HttpClient
                .get('api.grab-log.collection', this.filters.normalizeUrlParams())
                .then((response: SuccessResponse) => {
                    const collection = response.dataCollection
                    const factory = new GrabLogInfoCollectionFactory();
                    if (!factory.supports(response.data)
                        || !collection instanceof GrabLogInfoCollection
                    ) {
                        parseErrorValidation({message: 'Wrong data grab-log collection received'}, this.errors);
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
    },
});
