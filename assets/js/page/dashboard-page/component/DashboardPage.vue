<template>
    <v-row>
        <v-col cols="12" md="6">
            <v-card variant="outlined" class="mx-auto" min-height="550px">
                <v-card-title>Vacancy Status</v-card-title>
                <v-card-item>
                    <dashboard-vacancy-status/>
                </v-card-item>
            </v-card>
        </v-col>

        <v-col cols="12" md="6">
            <v-card variant="outlined" class="mx-auto" min-height="550px">
                <v-card-title>Resume Sent</v-card-title>
                <v-card-item>
                    <dashboard-sent-resume-to-all-resume-zoomable/>
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
    import DashboardSentResumeToAllResumeZoomable from "./dashboard/DashboardSentResumeToAllResumeZoomable.vue";
    import DashboardVacancyStatus from "./dashboard/DashboardVacancyStatus.vue";
    import DashboardDeadLineZoomable from "./dashboard/DashboardDeadLineZoomable.vue";
</script>
