<template>
    <div id="chart-dashboard-sent-resume-to-all-resume-zoomable">
        <vue-apex-charts
            type="area"
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
    import {processApiResponse} from "../../../../module/HandleApiResponse";
    import ErrorValidation from "../../../../model/transport-response/ErrorValidation";
    import SentResumeToAllResumeZoomableInfoCollectionFactory
        from "../../../../model-factory/dashboard/SentResumeToAllResumeZoomableInfoCollectionFactory";
    import SentResumeToAllResumeZoomableInfoCollection
        from "../../../../model/dashboard/SentResumeToAllResumeZoomableInfoCollection";
    import SentResumeToAllResumeZoomableInfo from "../../../../model/dashboard/SentResumeToAllResumeZoomableInfo";
    import _forEach from "lodash/forEach";
    import dayjs from "dayjs";
    import {formatDate, formatDateUrl} from "../../../../utils/DateUtils";

    const dashboardPageStore = useDashboardPageStore();
    const loading = computed(() => dashboardPageStore.loading);
    const errors = ref<ErrorValidation>(new ErrorValidation());
    const items = ref<SentResumeToAllResumeZoomableInfo[]>([] as SentResumeToAllResumeZoomableInfo[]);

    const chartOptions = computed(() => {
        return {
            chart: {
                type: 'area',
                stacked: false,
                height: 350,
                zoom: {
                    type: 'x',
                    enabled: true,
                    autoScaleYaxis: true,
                },
                toolbar: {
                    autoSelected: 'zoom',
                },
            },
            dataLabels: {
                enabled: false,
            },
            markers: {
                size: 0,
            },
            title: {
                text: 'Resume Sent',
                align: 'left',
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100],
                },
            },
            yaxis: {
                // labels: {
                //     formatter: function (val) {
                //         console.log('formatter:0:  ', val);
                //         return val;
                //         ///return '$' + (val / 1000000).toFixed(0)
                //     },
                // },
                // title: {text: 'Count Sent Resume'},
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    formatter: (val) => {
                        return formatDate(dayjs(val));
                    },
                },
            },
            tooltip: {
                shared: false,
                // y: {
                //     formatter: function (val) {
                //         console.log('formatter:2:  ', val);
                //         return val;
                //         //return '$' + (val / 1000000).toFixed(0)
                //     },
                // },
            },
        }
    });

    const series = computed(() => {

        const vacancies = {
            name: 'Vacancies Created',
            data: [],
        };

        const resumeSent = {
            name: 'Resume Sent',
            data: [],
        };

        const deadLine = {
            name: 'Dead Line',
            data: [],
        };

        _forEach(items.value, (item: SentResumeToAllResumeZoomableInfo) => {
            vacancies.data.push([formatDateUrl(item.date), item.vacancies]);
            resumeSent.data.push([formatDateUrl(item.date), item.resumeSent]);
            deadLine.data.push([formatDateUrl(item.date), item.deadLine]);
        });

        return [
            //vacancies,
            resumeSent,
            deadLine,
        ];
    });

    onMounted(async () => {
        // load source
        await processApiResponse(dashboardPageStore.loadDashboardSentResumeToAllResumeZoomableCollection(), errors.value, new SentResumeToAllResumeZoomableInfoCollectionFactory())
            .then((data: SentResumeToAllResumeZoomableInfoCollection) => {
                items.value = data.items;
            });
    });
</script>

<style>
    #chart-dashboard-sent-resume-to-all-resume-zoomable {
        max-width: 650px;
        margin: 35px auto;
    }
</style>
