<template>
    <div id="chart-dashboard-vacancy-status">
        <vue-apex-charts
            type="bar"
            height="350"
            :options="chartOptions"
            :series="series"
        ></vue-apex-charts>
    </div>
</template>

<script setup lang="ts">
    import VueApexCharts from 'vue3-apexcharts'
    import {useDashboardPageStore} from "../../store";
    import {computed, onMounted, ref} from "vue";
    import ErrorValidation from "../../../../model/transport-response/ErrorValidation";
    import {processApiResponse} from "../../../../module/HandleApiResponse";
    import VacancyStatusDashboardFactory from "../../../../model-factory/dashboard/VacancyStatusDashboardFactory";
    import VacancyStatusDashboard from "../../../../model/dashboard/VacancyStatusDashboard";

    const dashboardPageStore = useDashboardPageStore();
    const loading = computed(() => dashboardPageStore.loading);
    const errors = ref<ErrorValidation>(new ErrorValidation());
    const vacancyStatusDashboard = ref<VacancyStatusDashboard>({
        totalPending: 50,
        totalInProgress: 30,
        totalDone: 0,
    } as VacancyStatusDashboard);

    const chartOptions = computed(() => {
        return {

            chart: {
                type: 'bar',
                height: 350,
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    horizontal: false,
                },
            },
            dataLabels: {
                enabled: true,
            },
            // title: {align: 'left', text: 'title' },
            xaxis: {
                categories: [
                    'Pending',
                    'In-Progress',
                ],
                labels: {
                    formatter: (label: string, param, opts?: { i?: number, w?: {} }) => {
                        const w = opts?.w as { config?: { series?: [] } }; // ApexCharts
                        const index = opts?.i;
                        const total = w?.config?.series?.[index]?.data?.[0] ?? 0;

                        return `${label}: ${total}`;
                    },
                },
            },
        }
    });

    const series = computed(() => [
        {
            name: 'Status',
            data: [50, 75],
        },
        // {
        //     name: 'Pending',
        //     data: [vacancyStatusDashboard.value.totalPending],
        // },
        // {
        //     name: 'In-Progress',
        //     data: [vacancyStatusDashboard.value.totalInProgress],
        // },
    ]);


</script>

<style>
    #chart-dashboard-vacancy-status {
        padding: 0;
        max-width: 480px;
        margin: 35px auto;
    }
</style>
