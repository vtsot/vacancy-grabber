import {defineStore} from 'pinia';
import HttpClient from "../../module/HttpClient";
import {computed, ref} from "vue";

export const useDashboardPageStore = defineStore('dashboard-page-store', () =>{
    // State
    const isLoading = ref(false);

    // Getters
    const loading = computed(() => isLoading.value);

    // Actions
    const loadDashboardVacancyStatus = async () => {
        isLoading.value = true;
        return HttpClient
            .get('api.dashboard.vacancy-status')
            .finally(() => isLoading.value = false);
    };

    const loadDashboardResumeSentCollection = async () => {
        isLoading.value = true;
        return HttpClient
            .get('api.dashboard.resume-sent.collection')
            .finally(() => isLoading.value = false);
    };

    return {
        loading,
        // Actions
        loadDashboardVacancyStatus,
        loadDashboardResumeSentCollection,
    };
});
