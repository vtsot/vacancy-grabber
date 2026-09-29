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
                        <v-text-field
                            v-model="formData.notedAt"
                            :rules="formRules.notedAt"
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
                            v-model="formData.description"
                            :rules="formRules.description"
                            :loading="loading"
                            :autofocus="true"
                            clearable
                            label="Description"
                            type="text"
                        />
                    </v-col>
                </v-row>

            </v-form>
        </div>
    </v-card-text>

</template>

<script setup lang="ts">
    import {computed, onMounted, ref, useTemplateRef} from "vue";
    import {useVacancyPageStore} from "../store";
    import handleApiResponse, {
        clearFormErrors,
        normalizeFormToRequest,
        processApiResponse
    } from "../../../module/HandleApiResponse";
    import FormGlobalError from "../../../component/general/FormGlobalError.vue";
    import VacancyDetailsFactory from "../../../model-factory/vacancy/VacancyDetailsFactory";
    import VacancyDetails from "../../../model/vacancy/VacancyDetails";
    import ErrorResponse from "../../../model/transport-response/ErrorResponse";
    import {formatDateUrl} from "../../../utils/DateUtils";
    import {itemsIsResumeStatusOptions} from "./types/itemsIsResumeStatusOptions";
    import {itemsViewedFormOptions} from "./types/itemsViewedFormOptions";
    import {useSourcePageStore} from "../../source-page/store";
    import SourceSummary from "../../../model/source/SourceSummary";
    import SourceSummaryCollectionFactory from "../../../model-factory/source/SourceSummaryCollectionFactory";
    import SourceSummaryCollection from "../../../model/source/SourceSummaryCollection";
    import {useCompanyPageStore} from "../../company-page/store";
    import CompanySummary from "../../../model/company/CompanySummary";
    import CompanySummaryCollectionFactory from "../../../model-factory/company/CompanySummaryCollectionFactory";
    import CompanySummaryCollection from "../../../model/company/CompanySummaryCollection";
    import {scrollToFormFieldError} from "../../../utils/ScrollUtils";
    import dayjs from "dayjs";
    import {filterUrlParams} from "../../../utils/UrlUtils";
    import VacancyNote from "../../../model/vacancy/VacancyNote";
    import VacancyNoteFactory from "../../../model-factory/vacancy/VacancyNoteFactory";

    const vacancyPageStore = useVacancyPageStore();
    const emit = defineEmits(['form:done', 'form:unable-load'])
    const loading = ref(false);

    const props = defineProps({
        id: {type: String, required: true},
        noteId: {type: String, required: false}
    });

    const form = useTemplateRef('form');
    const formView = ref(true);
    const formData = ref({
        description: null as string || null,
        notedAt: null as string || formatDateUrl(dayjs()),
    });
    const formErrors = ref({
        message: null,
        fields: {
            description: null,
            notedAt: null,
        }
    });
    const formRules = computed(() => ({
        description: [() => formErrors.value.fields.description ?? null],
        notedAt: [() => formErrors.value.fields.notedAt ?? null],
    }))

    onMounted(async () => {

        // load vacancy note
        if (props.noteId) {
            formView.value = true;
            clearFormErrors(formErrors.value);

            // get details
            await processApiResponse(vacancyPageStore.loadNoteDetails(props.id as string, props.noteId as string), formErrors.value, new VacancyNoteFactory())
                .then((data: VacancyNote) => {
                    formView.value = true;
                    formData.value.description = data.description;
                    formData.value.notedAt = formatDateUrl(data.notedAt);
                }, (response: ErrorResponse) => {
                    formView.value = false;
                    emit('form:unable-load');
                }).finally(() => loading.value = false);
        }
    });

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
        const data = normalizeFormToRequest(formData.value);
        const result = props.id && props.noteId
            ? await handleApiResponse(vacancyPageStore.updateNote(props.id as string, props.noteId as string, data), formErrors.value)
            : await handleApiResponse(vacancyPageStore.createNote(props.id as string, data), formErrors.value);

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
