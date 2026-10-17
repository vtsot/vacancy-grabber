<template>
    <v-row>
        <v-col cols="12" md="6">
            <v-card variant="outlined" class="mx-auto" min-height="550px">
                <v-card-title>Vacancy Status</v-card-title>
                <v-card-item>
                    <dashboard-vacancy-status :vacancy-status-dashboard="vacancyStatusDashboard"/>
                </v-card-item>
            </v-card>
        </v-col>

        <v-col cols="12" md="6">
            <v-card variant="outlined" class="mx-auto" min-height="550px">
                <v-card-title>Monthly Resume Sent</v-card-title>
                <v-card-item>
                    <dashboard-resume-sent-monthly :items="monthlyItems"/>
                </v-card-item>
            </v-card>
        </v-col>
    </v-row>

    <!--    <v-row justify="center">-->
    <!--        <v-col cols="12" md="6">-->
    <!--            <v-card variant="outlined" class="mx-auto" min-height="550px">-->
    <!--                <v-card-title>Dead Line</v-card-title>-->
    <!--                <v-card-item>-->
    <!--                    <dashboard-dead-line-zoomable/>-->
    <!--                </v-card-item>-->
    <!--            </v-card>-->
    <!--        </v-col>-->
    <!--    </v-row>-->

</template>

<script setup lang="ts">
    import DashboardResumeSentMonthly from "./dashboard/DashboardResumeSentMonthly.vue";
    import DashboardVacancyStatus from "./dashboard/DashboardVacancyStatus.vue";
    import {computed, onMounted, ref} from "vue";
    import {processApiResponse} from "../../../module/HandleApiResponse";
    import ResumeSentDashboardInfoCollectionFactory
        from "../../../model-factory/dashboard/ResumeSentDashboardInfoCollectionFactory";
    import ResumeSentDashboardInfoCollection
        from "../../../model/dashboard/ResumeSentDashboardInfoCollection";
    import {useDashboardPageStore} from "../store";
    import ErrorValidation from "../../../model/transport-response/ErrorValidation";
    import VacancyStatusDashboard from "../../../model/dashboard/VacancyStatusDashboard";
    import VacancyStatusDashboardFactory from "../../../model-factory/dashboard/VacancyStatusDashboardFactory";
    import ResumeSentDashboardInfo from "../../../model/dashboard/ResumeSentDashboardInfo";

    const dashboardPageStore = useDashboardPageStore();
    const loading = computed(() => dashboardPageStore.loading);
    const errors = ref<ErrorValidation>(new ErrorValidation());

    const vacancyStatusDashboard = ref<VacancyStatusDashboard>({
        totalPending: 0,
        totalInProgress: 0,
        totalDone: 0
    } as VacancyStatusDashboard);

    const monthlyItems = ref<ResumeSentDashboardInfo[]>([] as ResumeSentDashboardInfo[]);

    onMounted(async () => {
        // loadDashboardVacancyStatus
        await processApiResponse(dashboardPageStore.loadDashboardVacancyStatus(), errors.value, new VacancyStatusDashboardFactory())
            .then((data: VacancyStatusDashboard) => {
                vacancyStatusDashboard.value.totalPending = data.totalPending;
                vacancyStatusDashboard.value.totalInProgress = data.totalInProgress;
                vacancyStatusDashboard.value.totalDone = data.totalDone;
            });

        // loadDashboardResumeSentCollection
        await processApiResponse(dashboardPageStore.loadDashboardResumeSentCollection(), errors.value, new ResumeSentDashboardInfoCollectionFactory())
            .then((data: ResumeSentDashboardInfoCollection) => {
                monthlyItems.value = data.items;
            });
    });

</script>
