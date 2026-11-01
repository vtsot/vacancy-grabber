<template>

    <v-card-text v-if="formErrors.message">
        <form-global-error :errors="[formErrors.message as string]"/>
    </v-card-text>

    <v-progress-linear indeterminate v-if="loading"></v-progress-linear>

    <v-card-text v-if="formView">

        <div class="v-col-md-12 v-col-lg-9 v-col-xl-8 v-col-xxl-6">
            <v-form
                ref="form"
                validate-on="submit"
                :disabled="loading"
                @submit.prevent="onSubmit"
            >

                <v-row>
                    <v-col cols="6">
                        Viewed:
                        <v-switch
                            v-model="formData.viewed"
                            :rules="formRules.viewed as string[]"
                            :loading="loading"
                            clearable
                            :true-icon="mdiCheck"
                            :false-icon="mdiClose"
                            color="success"
                            size="x-large"
                        />
                    </v-col>

                    <v-col cols="6">
                        <v-text-field
                            v-model="formData.deadLineAt"
                            :rules="formRules.deadLineAt as string[]"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Dead Line At"
                            @update:modelValue="onDeadLineAtUpdated"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col cols="6">
                        <v-select
                            v-model="formData.status"
                            :rules="formRules.status as string[]"
                            :loading="loading"
                            :items="itemsIsResumeStatusOptions"
                            clearable
                            label="Status"
                            @update:modelValue="onStatusUpdated"
                        />
                    </v-col>

                    <v-col cols="6">
                        <v-text-field
                            v-model="formData.resumeSentAt"
                            :rules="formRules.resumeSentAt as string[]"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Resume Sent At"
                            @update:modelValue="onResumeSentAtUpdated"
                        />
                        <!-- <a @click.prevent="onResumeSentAtToday" href="javascript:void(0)">Today</a>-->
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.notedAt"
                            :rules="formRules.notedAt as string[]"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Noted At"
                            @update:modelValue="onNotedAtUpdated"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <template v-for="(item, index) in noteTemplates" :key="item.key">
                            <a @click.prevent="onNoteAddTemplate(item.title)"
                               href="javascript:void(0)"
                            >
                                {{ item.title }}
                            </a>
                            <span v-if="index !== noteTemplates.length - 1"> | </span>
                        </template>
                        <v-textarea
                            v-model="formData.note"
                            :rules="formRules.note as string[]"
                            :loading="loading"
                            clearable
                            label="Note"
                            type="text"
                            @update:modelValue="onNoteUpdated"
                        />
                    </v-col>
                </v-row>

            </v-form>
        </div>
    </v-card-text>

</template>

<script setup lang="ts">
    import {computed, onMounted, ref, useTemplateRef} from "vue";
    import {useVacancyPageStore} from "../../store";
    import handleApiResponse, {
        clearFormErrors,
        normalizeFormToRequest,
        processApiResponse
    } from "../../../../module/HandleApiResponse";
    import FormGlobalError from "../../../../component/general/FormGlobalError.vue";
    import VacancyDetailsFactory from "../../../../model-factory/vacancy/VacancyDetailsFactory";
    import VacancyDetails from "../../../../model/vacancy/VacancyDetails";
    import ErrorResponse from "../../../../model/transport-response/ErrorResponse";
    import {formatDate, formatDateUrl} from "../../../../utils/DateUtils";
    import {itemsIsResumeStatusOptions} from "../types/itemsIsResumeStatusOptions";
    import {useSourcePageStore} from "../../../source-page/store";
    import {useCompanyPageStore} from "../../../company-page/store";
    import {scrollToFormFieldError} from "../../../../utils/ScrollUtils";
    import {mdiCheck, mdiClose} from "@mdi/js";
    import dayjs from "dayjs";

    const vacancyPageStore = useVacancyPageStore();
    const sourcePageStore = useSourcePageStore();
    const companyPageStore = useCompanyPageStore();
    const emit = defineEmits(['form:done', 'form:unable-load'])
    const loading = ref(false);

    const props = defineProps({
        id: {type: String, required: true}
    });

    const form = useTemplateRef('form');
    const formView = ref(true);
    const formData = ref({
        viewed: null as boolean || false,
        status: null as string || null,
        resumeSentAt: null as string || null,
        deadLineAt: null as string || null,
        notedAt: null as string || null,
        note: null as string || null,
    });
    const formErrors = ref({
        message: null,
        fields: {
            viewed: null,
            status: null,
            resumeSentAt: null,
            deadLineAt: null,
            notedAt: null,
            note: null,
        }
    });
    const formRules = computed(() => ({
        viewed: [() => formErrors.value.fields.viewed ?? null],
        status: [() => formErrors.value.fields.status ?? null],
        resumeSentAt: [() => formErrors.value.fields.resumeSentAt ?? null],
        deadLineAt: [() => formErrors.value.fields.deadLineAt ?? null],
        notedAt: [() => formErrors.value.fields.notedAt ?? null],
        note: [() => formErrors.value.fields.note ?? null],
    }))

    const noteTemplates = computed(() => [
        {key: 'DOU', title: 'Отправил заявку на DOU'},
        {key: 'Djinni', title: 'Отправил заявку на Djinni'},
    ]);

    onMounted(async () => {
        // load vacancy
        if (props.id) {
            formView.value = true;
            clearFormErrors(formErrors.value);

            // get details
            await processApiResponse(vacancyPageStore.loadDetails(props.id), formErrors.value, new VacancyDetailsFactory())
                .then((data: VacancyDetails) => {
                    formData.value.viewed = !!data.viewed;
                    formData.value.status = data.status;
                    formData.value.resumeSentAt = formatDateUrl(data.resumeSentAt);
                    formData.value.deadLineAt = formatDateUrl(data.deadLineAt);
                    formData.value.notedAt = null;
                    formData.value.note = null;
                }, (response: ErrorResponse) => {
                    formView.value = false;
                    emit('form:unable-load');
                }).finally(() => loading.value = false);
        }
    });

    const onStatusUpdated = () => {
        formData.value.viewed = true;
    }

    const onResumeSentAtToday = () => {
        formData.value.resumeSentAt = formatDateUrl(dayjs());
        onResumeSentAtUpdated();
    }

    const onResumeSentAtUpdated = () => {
        formData.value.viewed = true;
    }

    const onDeadLineAtUpdated = () => {
        formData.value.viewed = true;
    }

    const onNotedAtUpdated = () => {
        formData.value.viewed = true;
    }

    const onNoteAddTemplate = (value: string) => {

        if (formData.value.resumeSentAt === null) {
            formData.value.resumeSentAt = formatDateUrl(dayjs());
        }

        if (formData.value.notedAt === null) {
            formData.value.notedAt = formatDateUrl(dayjs());
        }

        if (formData.value.note === null) {
            formData.value.note = '';
        }

        formData.value.note = formData.value.note + value + "\n";
        onNoteUpdated(formData.value.note as string);
    }

    const onNoteUpdated = (value: string) => {
        formData.value.viewed = true;

        if (value.length === 0
            && formData.value.notedAt
        ) {
            formData.value.notedAt = null;
        }

        if (value.length > 0
            && !formData.value.notedAt
        ) {
            const today = dayjs();
            formData.value.notedAt = formatDateUrl(today);
        }
    }

    const onSubmit = async () => {
        loading.value = true;
        clearFormErrors(formErrors.value);
        form.value.resetValidation();
        const {valid: formIsValid} = await form.value.validate();

        if (!formIsValid) {
            loading.value = true;
            scrollToFormFieldError();
            return;
        }

        loading.value = true;
        const data = {
            properties: Object.keys(normalizeFormToRequest(formData.value)),
            ... normalizeFormToRequest(formData.value),
        };

        const result = await handleApiResponse(vacancyPageStore.createDetails(props.id as string, data), formErrors.value);

        if (!result || formErrors.value.message) {
            await form.value.validate();
            loading.value = false;
            scrollToFormFieldError();
            return;
        }

        loading.value = false;
        emit('form:done');
    };

    // Explicitly expose the method to the parent
    defineExpose({
        onSubmit,
    });

</script>
