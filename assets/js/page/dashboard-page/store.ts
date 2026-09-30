import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import GrabLogInfo from "../../model/grab-log/GrabLogInfo";
import GrabLogInfoFilters from "../../model/grab-log/GrabLogInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import {computed, ref} from "vue";

export const useDashboardPageStore = defineStore('dashboard-page-store', () =>{
    // State
    const isLoading = ref(false);
    // const items = ref<GrabLogInfo[]>([] as GrabLogInfo[]); // original loaded items
    // const filters = ref<GrabLogInfoFilters>(new GrabLogInfoFilters());
    // const errors = ref<ErrorValidation>(new ErrorValidation());
    // const paginator = ref<Paginator>(new Paginator());
    // const availableSortBy = ref<string[]>([] as string[]);
    // const availableFilters = ref<{} as object>({} as object);
    // const appliedFilters = ref<{} as object>({} as object);

    // Getters
    const loading = computed(() => isLoading.value);
    // const hasSortBy = computed(() => (name: string): boolean => availableSortBy.value.includes(name as string));

    // Actions
    const loadDashboardSentResumeToAllResumeZoomableCollection = async () => {
        isLoading.value = true;
        return HttpClient
            .get('api.dashboard.sent-resume-to-all-resume-zoomable.collection')
            .finally(() => isLoading.value = false);
    };

    return {
        loading,
        loadDashboardSentResumeToAllResumeZoomableCollection,
        // items,
        // filters,
        // errors,
        // paginator,
        // availableSortBy,
        // availableFilters,
        // appliedFilters,
        // hasSortBy,
        //Actions
        // loadCollection
    };
});
