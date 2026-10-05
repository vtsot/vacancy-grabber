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
                    <v-col cols="4">
                        <v-autocomplete
                            v-model="notePageStore.filters.company"
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

                    <v-col cols="8">
                        <div v-if="formErrors.message" class="text-red">
                            {{ formErrors.message }}
                        </div>
                    </v-col>

                </v-row>

                <v-divider v-if="formErrors.message" class="my-2"/>

                <v-row>
                    <v-col cols="6">
                        <v-text-field
                            v-model="notePageStore.filters.query"
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
    import {useNotePageStore} from "../store";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import {clearFormErrors, parseErrorValidation, processApiResponse} from "../../../module/HandleApiResponse";
    import SourceSummary from "../../../model/source/SourceSummary";
    import CompanySummary from "../../../model/company/CompanySummary";
    import SourceSummaryCollectionFactory from "../../../model-factory/source/SourceSummaryCollectionFactory";
    import SourceSummaryCollection from "../../../model/source/SourceSummaryCollection";
    import CompanySummaryCollectionFactory from "../../../model-factory/company/CompanySummaryCollectionFactory";
    import CompanySummaryCollection from "../../../model/company/CompanySummaryCollection";
    import {useCompanyPageStore} from "../../company-page/store";

    const flashStore = useFlashStore();
    const notePageStore = useNotePageStore();
    const companyPageStore = useCompanyPageStore();
    const emit = defineEmits(['filters:submit', 'filters:update', 'filters:reset'])
    const loading = computed(() => notePageStore.loading);

    const companySummaryItems = ref([] as CompanySummary[]);

    onMounted(async () => {
        parseErrorValidation(notePageStore.errors, formErrors.value);
        await form.value.validate();

        // load company
        await processApiResponse(companyPageStore.loadSummary({with_note_only: true}), formErrors.value, new CompanySummaryCollectionFactory())
            .then((data: CompanySummaryCollection) => {
                companySummaryItems.value = data.items;
            });
    });

    const form = useTemplateRef('form');

    interface formErrorsInterface {
        message: string | null,
        fields: {
            query: string | null,
            company: string | null,
        },
    }

    const formErrors = computed<formErrorsInterface>(() => {
        const {message, fields} = notePageStore.errors;
        return {
            message: message ?? null,
            fields: Object.fromEntries(
                Object.entries(fields).map(([key, value]) => [key, value ?? null])
            )
        } as formErrorsInterface;
    });

    const formRules = computed(() => ({
        query: [() => formErrors.value.fields.query ?? null],
        company: [() => formErrors.value.fields.company ?? null],
    }));

    watch(
        () => notePageStore.errors,
        async () => {
            parseErrorValidation(notePageStore.errors, formErrors.value);
            await form.value.validate();
        },
        {deep: true},
    );

    const onReset = () => {
        notePageStore.filters.reset();
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
