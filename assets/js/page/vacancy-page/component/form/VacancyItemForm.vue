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
                            v-model="formData.title"
                            :rules="formRules.title as string[]"
                            :loading="loading"
                            :autofocus="true"
                            clearable
                            label="Title"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.externalId"
                            :rules="formRules.externalId as string[]"
                            :loading="loading"
                            clearable
                            label="External Id"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-textarea
                            v-model="formData.description"
                            :rules="formRules.description as string[]"
                            :loading="loading"
                            clearable
                            label="Description"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.url"
                            :rules="formRules.url as string[]"
                            :loading="loading"
                            clearable
                            label="URL"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.salary"
                            :rules="formRules.salary as string[]"
                            :loading="loading"
                            clearable
                            label="Salary"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-autocomplete
                            v-model="formData.sourceId"
                            :rules="formRules.sourceId as string[]"
                            :loading="loading"
                            :items="sourceSummaryItems"
                            item-value="id"
                            item-title="name"
                            clearable
                            label="Source"
                            autocomplete="off"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-autocomplete
                            v-model="formData.companyId"
                            :rules="formRules.companyId as string[]"
                            :loading="loading"
                            :items="companySummaryItems"
                            item-value="id"
                            item-title="name"
                            clearable
                            label="Company"
                            autocomplete="off"
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
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-select
                            v-model="formData.viewed"
                            :rules="formRules.viewed as string[]"
                            :loading="loading"
                            :items="itemsViewedFormOptions"
                            clearable
                            label="Viewed"
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
    import {itemsViewedFormOptions} from "../types/itemsViewedOptions";
    import {useSourcePageStore} from "../../../source-page/store";
    import SourceSummary from "../../../../model/source/SourceSummary";
    import SourceSummaryCollectionFactory from "../../../../model-factory/source/SourceSummaryCollectionFactory";
    import SourceSummaryCollection from "../../../../model/source/SourceSummaryCollection";
    import {useCompanyPageStore} from "../../../company-page/store";
    import CompanySummary from "../../../../model/company/CompanySummary";
    import CompanySummaryCollectionFactory from "../../../../model-factory/company/CompanySummaryCollectionFactory";
    import CompanySummaryCollection from "../../../../model/company/CompanySummaryCollection";
    import {scrollToFormFieldError} from "../../../../utils/ScrollUtils";

    const vacancyPageStore = useVacancyPageStore();
    const sourcePageStore = useSourcePageStore();
    const companyPageStore = useCompanyPageStore();
    const emit = defineEmits(['form:done', 'form:unable-load'])
    const loading = ref(false);

    const props = defineProps({
        id: {type: String, required: false}
    });

    const form = useTemplateRef('form');
    const formView = ref(true);
    const formData = ref({
        title: null as string || null,
        description: null as string || null,
        externalId: null as string || null,
        url: null as string || null,
        salary: null as string || null,
        sourceId: null as string || null,
        companyId: null as string || null,
        status: null as string || null,
        viewed: null as boolean || false,
        resumeSentAt: null as string || null,
        deadLineAt: null as string || null,
    });
    const formErrors = ref({
        message: null,
        fields: {
            title: null,
            description: null,
            externalId: null,
            url: null,
            salary: null,
            sourceId: null,
            companyId: null,
            status: null,
            viewed: null,
            resumeSentAt: null,
            deadLineAt: null,
        }
    });
    const formRules = computed(() => ({
        title: [() => formErrors.value.fields.title ?? null],
        description: [() => formErrors.value.fields.description ?? null],
        externalId: [() => formErrors.value.fields.externalId ?? null],
        url: [() => formErrors.value.fields.url ?? null],
        salary: [() => formErrors.value.fields.salary ?? null],
        sourceId: [() => formErrors.value.fields.sourceId ?? null],
        companyId: [() => formErrors.value.fields.companyId ?? null],
        status: [() => formErrors.value.fields.status ?? null],
        viewed: [() => formErrors.value.fields.viewed ?? null],
        resumeSentAt: [() => formErrors.value.fields.resumeSentAt ?? null],
        deadLineAt: [() => formErrors.value.fields.deadLineAt ?? null],
    }))

    const sourceSummaryItems = ref([] as SourceSummary[]);
    const companySummaryItems = ref([] as CompanySummary[]);

    onMounted(async () => {

        // load source
        await processApiResponse(sourcePageStore.loadSummary(), formErrors.value, new SourceSummaryCollectionFactory())
            .then((data: SourceSummaryCollection) => {
                sourceSummaryItems.value = data.items;
            });

        // load company
        await processApiResponse(companyPageStore.loadSummary(), formErrors.value, new CompanySummaryCollectionFactory())
            .then((data: CompanySummaryCollection) => {
                companySummaryItems.value = data.items;
            });


        // load vacancy
        if (props.id) {
            formView.value = true;
            clearFormErrors(formErrors.value);

            // get details
            await processApiResponse(vacancyPageStore.loadDetails(props.id), formErrors.value, new VacancyDetailsFactory())
                .then((data: VacancyDetails) => {
                    formView.value = true;
                    formData.value.title = data.title;
                    formData.value.description = data.description;
                    formData.value.externalId = data.externalId;
                    formData.value.url = data.url;
                    formData.value.salary = data.salary;
                    formData.value.sourceId = data.sourceId;
                    formData.value.companyId = data.companyId;
                    formData.value.status = data.status;
                    formData.value.viewed = !!data.viewed;
                    formData.value.resumeSentAt = formatDateUrl(data.resumeSentAt);
                    formData.value.deadLineAt = formatDateUrl(data.deadLineAt);
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
        const result = props.id
            ? await handleApiResponse(vacancyPageStore.update(props.id, data), formErrors.value)
            : await handleApiResponse(vacancyPageStore.create(data), formErrors.value);

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
