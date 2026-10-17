<template>

    <v-card-text>
        <div class="v-col-md-12 v-col-lg-9 v-col-xl-8 v-col-xxl-6">
            <v-form
                ref="form"
                validate-on="submit"
                @submit.prevent="onSubmit"
                :loading="loading"
            >

                <v-row v-if="formErrors.message">
                    <v-col cols="12">
                        <div class="text-red">
                            {{ formErrors.message }}
                        </div>
                    </v-col>

                </v-row>

                <v-divider v-if="formErrors.message" class="my-2"/>

                <v-row>
                    <v-col cols="6">
                        <v-text-field
                            v-model="companyPageStore.filters.query"
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
                    </v-col>
                </v-row>

            </v-form>
        </div>
    </v-card-text>

</template>

<script setup lang="ts">
    import {computed, onMounted, ref, useTemplateRef, watch} from "vue";
    import {useCompanyPageStore} from "../store";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import {clearFormErrors, parseErrorValidation} from "../../../module/HandleApiResponse";

    const flashStore = useFlashStore();
    const companyPageStore = useCompanyPageStore();
    const emit = defineEmits(['filters:submit', 'filters:update', 'filters:reset'])
    const loading = computed(() => companyPageStore.loading);

    onMounted(async () => {
        parseErrorValidation(companyPageStore.errors, formErrors.value);
        await form.value.validate();
    });

    const form = useTemplateRef('form');

    interface formErrorsInterface {
        message: string | null,
        fields: {
            query: string | null,
        },
    }

    const formErrors = computed<formErrorsInterface>(() => {
        const {message, fields} = companyPageStore.errors;
        return {
            message: message ?? null,
            fields: Object.fromEntries(
                Object.entries(fields).map(([key, value]) => [key, value ?? null])
            )
        } as formErrorsInterface;
    });

    const formRules = computed(() => ({
        query: [() => formErrors.value.fields.query ?? null],
    }));

    watch(
        () => companyPageStore.errors,
        async () => {
            parseErrorValidation(companyPageStore.errors, formErrors.value);
            await form.value.validate();
        },
        {deep: true},
    );

    const onReset = () => {
        companyPageStore.filters.reset();
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
