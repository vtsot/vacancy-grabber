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
                    <v-col>
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
                </v-row>

                <v-row>
                    <v-col>
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
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.resumeSentAt"
                            :rules="formRules.resumeSentAt as string[]"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Resume Sent At"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.deadLineAt"
                            :rules="formRules.deadLineAt as string[]"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Dead Line At"
                        />
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
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
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
    import {formatDateUrl} from "../../../../utils/DateUtils";
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

    const onNoteUpdated = (value: string) => {

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
