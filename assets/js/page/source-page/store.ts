import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import SourceInfo from "../../model/source/SourceInfo";
import SourceInfoFilters from "../../model/source/SourceInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import {computed, ref} from "vue";

export const useSourcePageStore = defineStore('source-page-store', () => {
    // State
    const isLoading = ref(false);
    const items = ref<SourceInfo[]>([] as SourceInfo[]); // original loaded items
    const filters = ref<SourceInfoFilters>(new SourceInfoFilters());
    const errors = ref<ErrorValidation>(new ErrorValidation());
    const paginator = ref<Paginator>(new Paginator());
    const availableSortBy = ref<string[]>([] as string[]);
    const availableFilters = ref < {} as object > ({} as object);
    const appliedFilters = ref < {} as object > ({} as object);

    // Getters
    const loading = computed(() => isLoading.value);
    const hasSortBy = computed(() => (name: string): boolean => availableSortBy.value.includes(name as string));

    // Actions
    const create = async (data: object): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .post('api.source.post', {}, data)
            .finally(() => isLoading.value = false);
    };

    const update = async (id: string, data: object): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .put('api.source.put', {id}, data)
            .finally(() => isLoading.value = false);
    };

    const loadDetails = async (id: string): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .get('api.source.details', {id})
            .finally(() => isLoading.value = false);
    };

    const loadSummary = async (): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .get('api.source.summary')
            .finally(() => isLoading.value = false);
    };

    const loadCollection = async (): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .get('api.source.collection', filters.value.normalizeUrlParams())
            .finally(() => isLoading.value = false);
    };

    const patch = async (id: string, data: object): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .patch('api.source.patch', {id}, data)
            .finally(() => isLoading.value = false);
    };

    const deleteItem = async (id: string): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .delete('api.source.delete', {id})
            .finally(() => isLoading.value = false);
    };

    const grab = async (id: string): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .post('api.source.grab', {id: id})
            .finally(() => isLoading.value = false);
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
