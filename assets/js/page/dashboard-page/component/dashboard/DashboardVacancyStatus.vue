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
    import {computed, PropType} from "vue";
    import VacancyStatusDashboard from "../../../../model/dashboard/VacancyStatusDashboard";

    const props = defineProps({
        vacancyStatusDashboard: {
            type: Object as PropType<VacancyStatusDashboard>,
            required: true,
        },
    });

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
                props.vacancyStatusDashboard?.totalPending,
                props.vacancyStatusDashboard?.totalInProgress,
            ],
        },
    ]);

</script>

<style>
    #chart-dashboard-vacancy-status {
        padding: 0;
        max-width: 480px;
        margin: 35px auto;
    }
</style>
