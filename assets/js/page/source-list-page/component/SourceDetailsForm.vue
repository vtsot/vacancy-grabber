<template>

    <v-card-text v-if="formErrors.message">
        <form-global-error :errors="[formErrors.message as string]" />
    </v-card-text>

    <v-card-text>
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
                            v-model="formData.name"
                            :rules="formRules.name"
                            :loading="loading"
                            :autofocus="true"
                            clearable
                            :clear-icon="mdiCloseCircleOutline"
                            label="Source Name"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.url"
                            :rules="formRules.url"
                            :loading="loading"
                            clearable
                            label="URL"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-select
                            v-model="formData.active"
                            :rules="formRules.active"
                            :loading="loading"
                            :items="ItemsActiveOptions"
                            clearable
                            label="Active"
                        />
                    </v-col>
                </v-row>
            </v-form>
        </div>
    </v-card-text>

</template>

<script setup lang="ts">
    import {computed, ref, useTemplateRef, watch} from "vue";
    import {mdiAccountCancel, mdiCloseCircleOutline, mdiDelete} from '@mdi/js';
    import {useSourceListPageStore} from "../store";
    import {ItemsActiveOptions} from "../../../utils/ItemsActiveOptions";
    import {useAppFlashMessageStore} from "../../../component/general/app-flash-message/store";
    import handleApiResponse, {clearFormErrors, normalizeFormToRequest} from "../../../module/HandleApiResponse";
    import FormGlobalError from "../../../component/general/FormGlobalError.vue";

    const flashMessageStore = useAppFlashMessageStore();
    const sourceListPageStore = useSourceListPageStore();
    const emit = defineEmits(['form:done'])
    const loading = computed(() => sourceListPageStore.loading);

    const form = useTemplateRef('form');
    const formData = ref({
        name: null,
        url: null,
        active: true,
    });
    const formErrors = ref({
        message: null,
        fields: {
            name: null,
            url: null,
            active: null,
        }
    });
    const formRules = computed(() => ({
        name: [() => formErrors.value.fields.name ?? null],
        url: [() => formErrors.value.fields.url ?? null],
        active: [() => formErrors.value.fields.active ?? null],
    }))

    watch(
        () => sourceListPageStore.detailsErrors,
        async () => {
            formErrors.value = sourceListPageStore.detailsErrors;
            await form.value.validate();
        },
        {deep: true},
    );

    const onSubmit = async () => {

        clearFormErrors(formErrors.value);
        form.value.resetValidation();
        const {valid: formIsValid} = await form.value.validate();

        if (!formIsValid) {
            return;
        }

        const data = normalizeFormToRequest(formData.value);
        const result = await handleApiResponse(sourceListPageStore.createSourceDetails(data), formErrors.value);

        if (!result || formErrors.value.message) {
            await form.value.validate();
            // scrollToInvalidControl();
            return;
        }

        emit('form:done');
    };

    // Explicitly expose the method to the parent
    defineExpose({
        onSubmit,
    });

</script>
