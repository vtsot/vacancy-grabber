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
                            v-model="pageStore.filters.startDay"
                            :rules="formRules.startDay"
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
                            v-model="pageStore.filters.endDay"
                            :rules="formRules.endDay"
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
                            v-model="pageStore.filters.viewed"
                            :rules="formRules.viewed"
                            :loading="loading"
                            :disabled="loading"
                            :items="itemsViewedOptions"
                            clearable
                            label="Is Viewed?"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-select
                            v-model="pageStore.filters.status"
                            :rules="formRules.status"
                            :loading="loading"
                            :disabled="loading"
                            :items="itemsStatusOptions"
                            clearable
                            label="Status"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-select
                            v-model="pageStore.filters.source"
                            :rules="formRules.source"
                            :loading="loading"
                            :disabled="loading"
                            :items="itemsSourceOptions"
                            clearable
                            label="Source"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-autocomplete
                            v-model="pageStore.filters.company"
                            :rules="formRules.company"
                            :loading="loading"
                            :disabled="loading"
                            :items="itemsCompanyOptions"
                            clearable
                            label="Company"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                </v-row>

                <v-divider class="my-2"/>

                <v-row>
                    <v-col cols="2">
                        <v-autocomplete
                            v-model="pageStore.filters.isResumeSent"
                            :rules="formRules.isResumeSent"
                            :loading="loading"
                            :disabled="loading"
                            :items="itemsIsResumeSentOptions"
                            clearable
                            label="Is Resume Sent?"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-text-field
                            v-model="pageStore.filters.resumeSentStartDay"
                            :rules="formRules.resumeSentStartDay"
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
                            v-model="pageStore.filters.resumeSentEndDay"
                            :rules="formRules.resumeSentEndDay"
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
                            v-model="pageStore.filters.query"
                            :rules="formRules.query"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            label="Search"
                            placeholder="Search"
                            @update:model-value="onUpdate()"
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

                    <v-col cols="2">
                        <v-btn
                            text="Create"
                            :loading="loading"
                            :disabled="loading"
                            color="primary"
                            class="ml-5 text-right"
                            @click="$emit('create:button')"
                        />
                    </v-col>


                </v-row>

            </v-form>
        </div>
    </v-card-text>
</template>

<script setup lang="ts">
    import {computed, onMounted, ref, useTemplateRef, watch} from "vue";
    import {usePageStore} from "../store";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import {clearFormErrors, parseErrorValidation} from "../../../module/HandleApiResponse";
    import {itemsViewedOptions} from "./types/itemsViewedOptions";
    import {itemsIsResumeSentOptions} from "./types/itemsIsResumeSentOptions";
    import _cloneDeep from "lodash/cloneDeep";
    import {BaseSelectItemType} from "../../../types/BaseSelectItemType";

    const flashStore = useFlashStore();
    const pageStore = usePageStore();
    const emit = defineEmits(['filters:submit', 'filters:update', 'filters:reset', 'create:button'])
    const loading = computed(() => pageStore.loading);

    onMounted(async () => {
        parseErrorValidation(pageStore.errors, formErrors.value);
        await form.value.validate();
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
        const { message, fields } = pageStore.errors;
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
        () => pageStore.errors,
        async () => {
            parseErrorValidation(pageStore.errors, formErrors.value);
            await form.value.validate();
        },
        {deep: true},
    );


    const itemsSourceOptions = computed<BaseSelectItemType[]>(() => {
        if (pageStore.availableFilters?.source !== null
            && Array.isArray(pageStore.availableFilters?.source)
        ) {
            type itemType = {id: string; name: string | null };
            const items = _cloneDeep(pageStore.availableFilters?.source ?? []);
            return items
                .filter((item: itemType) => item.name !== '' && item.name !== null)
                .map((item: itemType) => ({value: item.id, title: item.name} as BaseSelectItemType));
        }

        return [];
    });

    const itemsCompanyOptions = computed<BaseSelectItemType[]>(() => {
        if (pageStore.availableFilters?.company !== null
            && Array.isArray(pageStore.availableFilters?.company)
        ) {
            type itemType = {id: string; name: string | null };
            const items = _cloneDeep(pageStore.availableFilters?.company ?? []);
            return items
                .filter((item: itemType) => item.name !== '' && item.name !== null)
                .map((item: itemType) => ({value: item.id, title: item.name} as BaseSelectItemType));
        }

        return [];
    });

    const itemsStatusOptions = computed<BaseSelectItemType[]>(() => {
        if (pageStore.availableFilters?.status !== null
            && Array.isArray(pageStore.availableFilters?.status)
        ) {
            type itemType = {value: string; name: string | null };
            const items = _cloneDeep(pageStore.availableFilters?.status ?? []);

            return items
                .filter((item: itemType) => item.name !== '' && item.name !== null)
                .map((item: itemType) => ({value: item.value, title: item.name} as BaseSelectItemType));
        }

        return [];
    });

    const onReset = () => {
        pageStore.filters.reset();
        emit('filters:reset');
    };

    const onUpdate = async () => {
        // //clearFormErrors(formErrors.value);
        // form.value.resetValidation();
        // const {valid: formIsValid} = await form.value.validate();
        //
        // if (!formIsValid) {
        //     return;
        // }

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
