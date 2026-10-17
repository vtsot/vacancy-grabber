import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import CompanyInfoFilters from "../../model/company/CompanyInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import {computed, ref} from "vue";
import CompanyInfo from "../../model/company/CompanyInfo";


export const useCompanyPageStore = defineStore('company-page-store', () => {
    // State
    const isLoading = ref(false);
    const items = ref<CompanyInfo[]>([] as CompanyInfo[]); // original loaded items
    const filters = ref<CompanyInfoFilters>(new CompanyInfoFilters());
    const errors = ref<ErrorValidation>(new ErrorValidation());
    const paginator = ref<Paginator>(new Paginator());
    const availableSortBy = ref<string[]>([] as string[]);
    const availableFilters = ref < {} as object > ({} as object);
    const appliedFilters = ref < {} as object > ({} as object);

    // Getters
    const loading = computed(() => isLoading.value);
    const hasSortBy = computed(() => (name: string): boolean => availableSortBy.value.includes(name as string));

    const loadSummary = async (data: object | {} = {}): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .get('api.company.summary', data)
            .finally(() => isLoading.value = false);
    };

    const loadCollection = async (): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .get('api.company.collection', filters.value.normalizeUrlParams())
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
        loadSummary,
        loadCollection,
    };

});
