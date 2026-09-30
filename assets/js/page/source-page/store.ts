import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import SourceInfo from "../../model/source/SourceInfo";
import SourceInfoFilters from "../../model/source/SourceInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import {computed, ref} from "vue";


// export const useSourcePageStore = defineStore('source-page-store', {
//     state: () => ({
//         loading: false,
//         items: [] as SourceInfo[], // original loaded items
//         filters: new SourceInfoFilters(),
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
//
//         async create(data: object): Promise<SuccessResponse> {
//             this.loading = true;
//             return HttpClient
//                 .post('api.source.post', {}, data)
//                 .finally(() => this.loading = false);
//         },
//
//         async update(id: string, data: object) {
//             this.loading = true;
//             return HttpClient
//                 .put('api.source.put', {id}, data)
//                 .finally(() => this.loading = false);
//         },
//
//         async loadDetails(id: string): Promise<SuccessResponse> {
//             this.loading = true;
//             return HttpClient
//                 .get('api.source.details', {id})
//                 .finally(() => this.loading = false);
//         },
//
//         async loadSummary() {
//             this.loading = true;
//             return HttpClient
//                 .get('api.source.summary')
//                 .finally(() => this.loading = false);
//         },
//
//         async loadCollection() {
//             this.loading = true;
//             return HttpClient
//                 .get('api.source.collection', this.filters.normalizeUrlParams())
//                 .finally(() => this.loading = false);
//         },
//
//         async patch(id: string, data: object) {
//             this.loading = true;
//             return HttpClient
//                 .patch('api.source.patch', {id}, data)
//                 .finally(() => this.loading = false);
//         },
//
//         async delete(id: string): Promise<SuccessResponse> {
//             this.loading = true;
//             return HttpClient
//                 .delete('api.source.delete', {id})
//                 .finally(() => this.loading = false);
//         },
//
//         async grab(id: string): Promise<SuccessResponse> {
//             this.loading = true;
//             return HttpClient
//                 .post('api.source.grab', {id: id})
//                 .finally(() => this.loading = false);
//         },
//     },
// });

export const useSourcePageStore = defineStore('source-page-store', () => {
    // State
    const loading = ref(false);
    const items = ref<SourceInfo[]>([] as SourceInfo[]); // original loaded items
    const filters = ref<SourceInfoFilters>(new SourceInfoFilters());
    const errors = ref<ErrorValidation>(new ErrorValidation());
    const paginator = ref<Paginator>(new Paginator());
    const availableSortBy = ref<string[]>([] as string[]);
    const availableFilters = ref < {} as object > ({} as object);
    const appliedFilters = ref < {} as object > ({} as object);

    // Getters
    const hasSortBy = computed(() => (name: string): boolean => availableSortBy.value.includes(name as string));

    // Actions
    const create = async (data: object): Promise<SuccessResponse> => {
        loading.value = true;
        return HttpClient
            .post('api.source.post', {}, data)
            .finally(() => loading.value = false);
    };

    const update = async (id: string, data: object): Promise<SuccessResponse> => {
        loading.value = true;
        return HttpClient
            .put('api.source.put', {id}, data)
            .finally(() => loading.value = false);
    };

    const loadDetails = async (id: string): Promise<SuccessResponse> => {
        loading.value = true;
        return HttpClient
            .get('api.source.details', {id})
            .finally(() => loading.value = false);
    };

    const loadSummary = async (): Promise<SuccessResponse> => {
        loading.value = true;
        return HttpClient
            .get('api.source.summary')
            .finally(() => loading.value = false);
    };

    const loadCollection = async (): Promise<SuccessResponse> => {
        loading.value = true;
        return HttpClient
            .get('api.source.collection', filters.value.normalizeUrlParams())
            .finally(() => loading.value = false);
    };

    const patch = async (id: string, data: object): Promise<SuccessResponse> => {
        loading.value = true;
        return HttpClient
            .patch('api.source.patch', {id}, data)
            .finally(() => loading.value = false);
    };

    const deleteItem = async (id: string): Promise<SuccessResponse> => {
        loading.value = true;
        return HttpClient
            .delete('api.source.delete', {id})
            .finally(() => loading.value = false);
    };

    const grab = async (id: string): Promise<SuccessResponse> => {
        loading.value = true;
        return HttpClient
            .post('api.source.grab', {id: id})
            .finally(() => loading.value = false);
    };

    return {
        loading: loading.value,
        items,
        filters,
        errors,
        paginator,
        availableSortBy,
        availableFilters,
        appliedFilters,
        hasSortBy,
        // Actions
        create,
        update,
        loadDetails,
        loadSummary,
        loadCollection,
        patch,
        deleteItem,
        grab,
    };
});
