<template>

    <v-card-text>
        <div class="v-col-md-12 v-col-lg-9 v-col-xl-8 v-col-xxl-6">
            <v-form
                ref="form"
                validate-on="submit"
                @submit.prevent="onSubmit"
                :loading="loading"
            >
                <v-row>
                    <v-col cols="2">
                        <v-text-field
                            v-model="vacancyPageStore.filters.startDay"
                            :rules="formRules.startDay as string[]"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Created At From"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-text-field
                            v-model="vacancyPageStore.filters.endDay"
                            :rules="formRules.endDay as string[]"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Created At To"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-autocomplete
                            v-model="vacancyPageStore.filters.viewed"
                            :rules="formRules.viewed as string[]"
                            :loading="loading"
                            :disabled="loading"
                            :items="itemsViewedFiltersOptions"
                            clearable
                            label="Is Viewed?"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                            autocomplete="off"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-select
                            v-model="vacancyPageStore.filters.status"
                            :rules="formRules.status as string[]"
                            :loading="loading"
                            :disabled="loading"
                            :items="itemsIsResumeStatusOptions"
                            clearable
                            label="Status"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-autocomplete
                            v-model="vacancyPageStore.filters.source"
                            :rules="formRules.source as string[]"
                            :loading="loading"
                            :disabled="loading"
                            :items="sourceSummaryItems"
                            item-value="id"
                            item-title="name"
                            clearable
                            label="Source"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                            autocomplete="off"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-autocomplete
                            v-model="vacancyPageStore.filters.company"
                            :rules="formRules.company as string[]"
                            :loading="loading"
                            :disabled="loading"
                            :items="companySummaryItems"
                            item-value="id"
                            item-title="name"
                            clearable
                            label="Company"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                            autocomplete="off"
                        />
                    </v-col>

                </v-row>

                <v-divider class="my-2"/>

                <v-row>
                    <v-col cols="2">
                        <v-autocomplete
                            v-model="vacancyPageStore.filters.isResumeSent"
                            :rules="formRules.isResumeSent as string[]"
                            :loading="loading"
                            :disabled="loading"
                            :items="itemsIsResumeSentOptions"
                            clearable
                            label="Is Resume Sent?"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                            autocomplete="off"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-text-field
                            v-model="vacancyPageStore.filters.resumeSentStartDay"
                            :rules="formRules.resumeSentStartDay as string[]"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Resume Sent From"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-text-field
                            v-model="vacancyPageStore.filters.resumeSentEndDay"
                            :rules="formRules.resumeSentEndDay as string[]"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Resume Sent To"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="5">
                        <div v-if="formErrors.message" class="text-red">
                            {{ formErrors.message }}
                        </div>
                    </v-col>
                </v-row>
                <v-divider class="my-2"/>

                <v-row>
                    <v-col cols="6">
                        <v-text-field
                            v-model="vacancyPageStore.filters.query"
                            :rules="formRules.query as string[]"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            label="Search"
                            placeholder="Search"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-btn
                            text="Search"
                            :loading="loading"
                            :disabled="loading"
                            color="secondary"
                            class="ml-5 text-right"
                            type="submit"
                        />
                    </v-col>

                    <v-col cols="2">
                    </v-col>

                    <v-col cols="2">
                        <v-btn
                            text="Reset"
                            :loading="loading"
                            :disabled="loading"
                            color="secondary"
                            class="ml-5 text-right"
                            @click="onReset"
                            variant="tonal"
                        />
                    </v-col>
                </v-row>

            </v-form>
        </div>
    </v-card-text>
</template>

<script setup lang="ts">
    import {computed, onMounted, ref, useTemplateRef, watch} from "vue";
    import {useVacancyPageStore} from "../store";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import {clearFormErrors, parseErrorValidation, processApiResponse} from "../../../module/HandleApiResponse";
    import {itemsViewedFiltersOptions} from "./types/itemsViewedOptions";
    import {itemsIsResumeSentOptions} from "./types/itemsIsResumeSentOptions";
    import SourceSummary from "../../../model/source/SourceSummary";
    import CompanySummary from "../../../model/company/CompanySummary";
    import {itemsIsResumeStatusOptions} from "./types/itemsIsResumeStatusOptions";
    import SourceSummaryCollectionFactory from "../../../model-factory/source/SourceSummaryCollectionFactory";
    import SourceSummaryCollection from "../../../model/source/SourceSummaryCollection";
    import CompanySummaryCollectionFactory from "../../../model-factory/company/CompanySummaryCollectionFactory";
    import CompanySummaryCollection from "../../../model/company/CompanySummaryCollection";
    import {useSourcePageStore} from "../../source-page/store";
    import {useCompanyPageStore} from "../../company-page/store";

    const flashStore = useFlashStore();
    const vacancyPageStore = useVacancyPageStore();
    const sourcePageStore = useSourcePageStore();
    const companyPageStore = useCompanyPageStore();
    const emit = defineEmits(['filters:submit', 'filters:update', 'filters:reset', 'create:button'])
    const loading = computed(() => vacancyPageStore.loading);

    const sourceSummaryItems = ref([] as SourceSummary[]);
    const companySummaryItems = ref([] as CompanySummary[]);

    onMounted(async () => {
        parseErrorValidation(vacancyPageStore.errors, formErrors.value);
        await form.value.validate();

        // load source
        await processApiResponse(sourcePageStore.loadSummary(), formErrors.value, new SourceSummaryCollectionFactory())
            .then((data: SourceSummaryCollection) => {
                sourceSummaryItems.value = data.items;
            });

        // load company
        await processApiResponse(companyPageStore.loadSummary({with_vacancy_only: true}), formErrors.value, new CompanySummaryCollectionFactory())
            .then((data: CompanySummaryCollection) => {
                companySummaryItems.value = data.items;
            });
    });

    const form = useTemplateRef('form');

    interface formErrorsInterface {
        message: string | null,
        fields: {
            query: string | null,
            viewed: string | null,
            startDay: string | null,
            endDay: string | null,

            isResumeSent: string | null,
            resumeSentStartDay: string | null,
            resumeSentEndDay: string | null,

            source: string | null,
            company: string | null,
            status: string | null,
        },
    }

    const formErrors = computed<formErrorsInterface>(() => {
        const {message, fields} = vacancyPageStore.errors;
        return {
            message: message ?? null,
            fields: Object.fromEntries(
                Object.entries(fields).map(([key, value]) => [key, value ?? null])
            )
        } as formErrorsInterface;
    });

    const formRules = computed(() => ({
        query: [() => formErrors.value.fields.query ?? null],
        viewed: [() => formErrors.value.fields.viewed ?? null],
        startDay: [() => formErrors.value.fields.startDay ?? null],
        endDay: [() => formErrors.value.fields.endDay ?? null],

        isResumeSent: [() => formErrors.value.fields.isResumeSent ?? null],
        resumeSentStartDay: [() => formErrors.value.fields.resumeSentStartDay ?? null],
        resumeSentEndDay: [() => formErrors.value.fields.resumeSentEndDay ?? null],

        source: [() => formErrors.value.fields.source ?? null],
        company: [() => formErrors.value.fields.company ?? null],
        status: [() => formErrors.value.fields.status ?? null],
    }));

    watch(
        () => vacancyPageStore.errors,
        async () => {
            parseErrorValidation(vacancyPageStore.errors, formErrors.value);
            await form.value.validate();
        },
        {deep: true},
    );

    const onReset = () => {
        vacancyPageStore.filters.reset();
        emit('filters:reset');
    };

    const onUpdate = async () => {
        form.value.resetValidation();
        const {valid: formIsValid} = await form.value.validate();

        emit('filters:update');
    };

    const onSubmit = async () => {
        clearFormErrors(formErrors.value);
        form.value.resetValidation();
        const {valid: formIsValid} = await form.value.validate();

        if (!formIsValid) {
            flashStore.addError({text: formErrors.value.message});
            return;
        }

        emit('filters:submit');
    };

</script>
