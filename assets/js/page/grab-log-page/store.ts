import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import GrabLogInfo from "../../model/grab-log/GrabLogInfo";
import GrabLogInfoFilters from "../../model/grab-log/GrabLogInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";

export const useGrabLogPageStore = defineStore('grab-log-page-store', {
    state: () => ({
        loading: false,
        items: [] as GrabLogInfo[], // original loaded items
        filters: new GrabLogInfoFilters(),
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
        async loadCollection() {
            this.loading = true;
            return HttpClient
                .get('api.grab-log.collection', this.filters.normalizeUrlParams())
                .finally(() => this.loading = false);
        },
    },
});
