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
                            v-model="sourcePageStore.filters.startDay"
                            :rules="formRules.startDay"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Start Day"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-text-field
                            v-model="sourcePageStore.filters.endDay"
                            :rules="formRules.endDay"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Start Day"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-select
                            v-model="sourcePageStore.filters.active"
                            :rules="formRules.active"
                            :loading="loading"
                            :disabled="loading"
                            :items="itemsActiveFiltersOptions"
                            clearable
                            label="Is Active?"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="6">
                        <div v-if="formErrors.message" class="text-red">
                            {{ formErrors.message }}
                        </div>
                    </v-col>

                </v-row>

                <v-divider class="my-2"/>

                <v-row>
                    <v-col cols="6">
                        <v-text-field
                            v-model="sourcePageStore.filters.query"
                            :rules="formRules.query"
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
    import {useSourcePageStore} from "../store";
    import {itemsActiveFiltersOptions} from "../../../utils/itemsActiveOptions";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import {clearFormErrors, parseErrorValidation} from "../../../module/HandleApiResponse";

    const flashStore = useFlashStore();
    const sourcePageStore = useSourcePageStore();
    const emit = defineEmits(['filters:submit', 'filters:update', 'filters:reset', 'create:button'])
    const loading = computed(() => sourcePageStore.loading);

    onMounted(async () => {
        parseErrorValidation(sourcePageStore.errors, formErrors.value);
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
            active: boolean | null,
        },
    }

    const formErrors = computed<formErrorsInterface>(() => {
        const {message, fields} = sourcePageStore.errors;
        return {
            message: message ?? null,
            fields: Object.fromEntries(
                Object.entries(fields).map(([key, value]) => [key, value ?? null])
            )
        } as formErrorsInterface;
    });

    const formRules = computed(() => ({
        query: [() => formErrors.value.fields.query ?? null],
        active: [() => formErrors.value.fields.active ?? null],
        startDay: [() => formErrors.value.fields.startDay ?? null],
        endDay: [() => formErrors.value.fields.endDay ?? null],
    }));

    watch(
        () => sourcePageStore.errors,
        async () => {
            parseErrorValidation(sourcePageStore.errors, formErrors.value);
            await form.value.validate();
        },
        {deep: true},
    );

    const onReset = () => {
        sourcePageStore.filters.reset();
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
