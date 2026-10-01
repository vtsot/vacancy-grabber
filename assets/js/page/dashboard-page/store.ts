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

    const loadDashboardSentResumeToAllResumeZoomableCollection = async () => {
        isLoading.value = true;
        return HttpClient
            .get('api.dashboard.sent-resume-to-all-resume-zoomable.collection')
            .finally(() => isLoading.value = false);
    };

    return {
        loading,
        // Actions
        loadDashboardVacancyStatus,
        loadDashboardSentResumeToAllResumeZoomableCollection,
    };
});
