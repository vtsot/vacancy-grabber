import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import GrabLogInfo from "../../model/grab-log/GrabLogInfo";
import GrabLogInfoFilters from "../../model/grab-log/GrabLogInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import {computed, ref} from "vue";

export const useGrabLogPageStore = defineStore('grab-log-page-store', () =>{
    // State
    const loading = ref(false);
    const items = ref<GrabLogInfo[]>([] as GrabLogInfo[]);
    const filters = ref<GrabLogInfoFilters>(new GrabLogInfoFilters());
    const errors = ref<ErrorValidation>(new ErrorValidation());
    const paginator = ref<Paginator>(new Paginator());
    const availableSortBy = ref<string[]>([] as string[]);
    const availableFilters = ref<{} as object>({} as object);
    const appliedFilters = ref<{} as object>({} as object);

    // Getters
    const hasSortBy = computed(() => (name: string): boolean => availableSortBy.value.includes(name as string));

    // Actions
    const loadCollection = async () => {
        loading.value = true;
        return HttpClient
            .get('api.grab-log.collection', filters.value.normalizeUrlParams())
            .finally(() => loading.value = false);
    };

    return {
        loading,
        items,
        filters,
        errors,
        paginator,
        availableSortBy,
        availableFilters,
        appliedFilters,
        hasSortBy,
        loadCollection
    };


});

// export const useGrabLogPageStore = defineStore('grab-log-page-store', {
//     state: () => ({
//         loading: false,
//         items: [] as GrabLogInfo[], // original loaded items
//         filters: new GrabLogInfoFilters(),
//         errors: new ErrorValidation(),
//         paginator: new Paginator(),
//         availableSortBy: [] as string[] | [],
//         availableFilters: {} as object | {},
//         appliedFilters: {} as object | null,
//     }),
//
//     getters: {
//         hasSortBy: (state) => (name: string): boolean => state.availableSortBy.includes(name),
//     },
//
//     actions: {
//         async loadCollection() {
//             this.loading = true;
//             return HttpClient
//                 .get('api.grab-log.collection', this.filters.normalizeUrlParams())
//                 .finally(() => this.loading = false);
//         },
//     },
// });
