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
        totalPending: 0,
        totalInProgress: 0,
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
                    distributed: true, // dependent color for each column
                    // columnWidth: '45%',
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    horizontal: false,
                },
            },
            colors: ['#2196F3', '#00BFA5'],
            dataLabels: {
                enabled: false,
            },
            // title: {align: 'left', text: 'title' },
            xaxis: {
                categories: [
                    'Pending',
                    'In-Progress',
                ],
                labels: {
                    formatter: (label: string, param, opts?: { i?: number, w?: {} }) => {

                        const index = opts?.i;
                        const w = opts?.w as { config?: { series?: { data?: [], name?: string, group?: string }[] } }; // ApexCharts

                        if (index !== undefined && index >= 0
                            && (w?.config?.series?.[0]?.data?.[index] ?? null)
                        ) {
                            const total = w?.config?.series?.[0]?.data?.[index] ?? 0;

                            return `${label}: ${total}`;
                        }

                        return label;
                    }
                },
            },
        }
    });

    const series = computed(() => [
        {
            name: 'Vacancies',
            data: [
                vacancyStatusDashboard.value.totalPending,
                vacancyStatusDashboard.value.totalInProgress,
            ],
        },
    ]);

    onMounted(async () => {
        // load source
        await processApiResponse(dashboardPageStore.loadDashboardVacancyStatus(), errors.value, new VacancyStatusDashboardFactory())
            .then((data: VacancyStatusDashboard) => {
                vacancyStatusDashboard.value.totalPending = data.totalPending;
                vacancyStatusDashboard.value.totalInProgress = data.totalInProgress;
                vacancyStatusDashboard.value.totalDone = data.totalDone;
            });
    });

</script>

<style>
    #chart-dashboard-vacancy-status {
        padding: 0;
        max-width: 480px;
        margin: 35px auto;
    }
</style>
